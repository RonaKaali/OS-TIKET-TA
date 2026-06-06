<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\{Ticket, TicketThread, HelpTopic, SlaPlan, Status, Attachment};
use App\Services\FileEncryptionService;
use App\Traits\LoggableActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    use LoggableActivity;
    public function create()
    {
        $topics = HelpTopic::with('department')->orderBy('name')->get();
        return view('portal.ticket.create', compact('topics'));
    }

    public function store(Request $r)
    {
        try {
            $user = $r->user();

            $data = $r->validate([
                'subject' => ['required', 'string', 'max:255'],
                'help_topic_id' => ['required', Rule::exists('topik_bantuan', 'id')],
                'priority_id' => ['nullable', Rule::exists('prioritas', 'id')],
                'message' => ['required', 'string', 'max:20000'],
                'attachments.*' => ['file', 'max:10240'], // 10MB
            ]);

            $topic = HelpTopic::with('department')->findOrFail($data['help_topic_id']);
            $status = Status::where('slug', 'open')->first()
                ?? Status::where('is_closed', false)->first()
                ?? Status::query()->firstOrFail();
            $slaId = SlaPlan::value('id');
            $grace = SlaPlan::whereKey($slaId)->value('grace_hours') ?? 48;

            $ticket = Ticket::create([
                'subject' => $data['subject'],
                'reporter_email' => $user->email,
                'reporter_name' => $user->name,
                'user_id' => $user->id,
                'department_id' => $topic->department_id,
                'help_topic_id' => $topic->id,
                'priority_id' => $data['priority_id'] ?: null,
                'status_id' => $status->id,
                'sla_plan_id' => $slaId,
                'due_at' => now()->addHours($grace),
                'custom_fields' => $this->extractCustomFields($topic, $r),
            ]);

            if (empty($ticket->ticket_number)) {
                throw new \RuntimeException('Nomor tiket gagal dibuat. Silakan coba lagi.');
            }

            $thread = TicketThread::create([
                'ticket_id' => $ticket->id,
                'type' => 'message',
                'user_id' => $user->id,
                'body' => $data['message'],
            ]);

            try {
                $this->storeAttachments($thread, $r->file('attachments', []));
            } catch (\Throwable $e) {
                \Log::error('Gagal menyimpan lampiran tiket: ' . $e->getMessage());
            }

            $ticket->refresh();
            $ticket->load(['threads.attachments', 'threads.user', 'status', 'priority', 'department']);

            $this->dispatchTicketNotifications($user, $ticket, $thread);

            try {
                $this->logCreate('Ticket', $ticket, [
                    'ticket_number' => $ticket->ticket_number,
                    'department_id' => $ticket->department_id,
                    'priority_id' => $ticket->priority_id,
                ]);
            } catch (\Throwable $e) {
                \Log::error('Gagal mencatat aktivitas pembuatan tiket: ' . $e->getMessage());
            }

            // Tampilkan langsung (hindari redirect yang gagal di serverless Vercel)
            session()->now('ok', 'Tiket berhasil dibuat.');

            return view('portal.ticket.show', compact('ticket'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Portal ticket store failed', [
                'message' => $e->getMessage(),
                'user_id' => $r->user()?->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal membuat laporan. Silakan coba lagi atau hubungi administrator.']);
        }
    }

    /**
     * Kirim notifikasi pembuatan tiket (gagal diam-diam, tidak memblokir response).
     */
    private function dispatchTicketNotifications($user, Ticket $ticket, TicketThread $thread): void
    {
        try {
            $user->notify(new \App\Notifications\NewTicketSubmitted($ticket, $thread));
        } catch (\Throwable $e) {
            \Log::error('Gagal mengirim notifikasi ke pelapor: ' . $e->getMessage());
        }

        try {
            $agents = \App\Models\User::whereHas('permissions', function ($q) {
                $q->where('permissions.name', 'admin.panel');
            })->orWhereHas('roles', function ($q) {
                $q->whereIn('roles.name', ['Super Admin', 'Admin', 'Agent', 'Support Agent']);
            })->get();

            if ($agents->isNotEmpty()) {
                Notification::send($agents, new \App\Notifications\NewTicketCreated($ticket, $thread));
            }
        } catch (\Throwable $e) {
            \Log::error('Gagal mengirim notifikasi ke agent: ' . $e->getMessage());
        }
    }

    public function show(string $number)
    {
        $ticket = Ticket::where('ticket_number', $number)
            ->with(['threads.attachments', 'threads.user', 'status', 'priority', 'department'])
            ->firstOrFail();

        $user = request()->user();

        // Akses langsung setelah tiket baru dibuat (menangani session tidak persisten di serverless)
        if (session('just_created_ticket') === $ticket->ticket_number) {
            return view('portal.ticket.show', compact('ticket'));
        }

        // Jika user login dan dia pemilik tiket, langsung allow
        if ($user && ($ticket->user_id === $user->id || $ticket->reporter_email === $user->email)) {
            try {
                $this->logRead('Ticket', $ticket, ['ticket_number' => $ticket->ticket_number]);
            } catch (\Throwable $e) {
                \Log::error('Gagal mencatat aktivitas baca tiket: ' . $e->getMessage());
            }

            return view('portal.ticket.show', compact('ticket'));
        }

        // Jika tidak login, cek apakah ada sesi verifikasi dari statusCheck
        $verifiedEmail = session('ticket_verified_email_' . $ticket->ticket_number);
        if ($verifiedEmail && $verifiedEmail === $ticket->reporter_email) {
            return view('portal.ticket.show', compact('ticket'));
        }

        // Jika tidak ada verifikasi, redirect ke status form
        return redirect()->route('portal.ticket.status.form')
            ->withErrors(['not_found' => 'Silakan verifikasi dengan nomor tiket dan email terlebih dahulu.']);
    }

    public function reply(Request $r, string $number)
    {
        $ticket = Ticket::where('ticket_number', $number)->firstOrFail();
        $user = $r->user();

        // Cek apakah user yang login adalah pemilik tiket
        abort_unless(
            $user && ($ticket->user_id === $user->id || $ticket->reporter_email === $user->email),
            403,
            'Anda tidak memiliki akses ke tiket ini.'
        );

        $data = $r->validate([
            'message' => ['required', 'string', 'max:20000'],
            'attachments.*' => ['file', 'max:10240'],
        ]);

        $thread = TicketThread::create([
            'ticket_id' => $ticket->id,
            'type' => 'message',
            'user_id' => $user->id, // Link ke user account
            'body' => $data['message'],
        ]);

        $this->storeAttachments($thread, $r->file('attachments', []));

        // set status -> answered (menunggu agen)
        if ($answeredId = Status::where('slug', 'answered')->value('id')) {
            $ticket->update(['status_id' => $answeredId]);
        }

        // Notifikasi ke agent yang ditugaskan atau semua admin
        try {
            $agents = $ticket->assigned_to && $ticket->assignee
                ? collect([$ticket->assignee])
                : \App\Models\User::whereHas('permissions', function ($q) {
                    $q->where('permissions.name', 'admin.panel');
                })->get();

            if ($agents->isNotEmpty()) {
                Notification::send($agents, new \App\Notifications\TicketReplyFromRequester($ticket, $thread));
            }
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim email ke agent: ' . $e->getMessage());
        }

        return back()->with('ok', 'Balasan terkirim.');
    }

    public function statusForm()
    {
        return view('portal.ticket.status');
    }

    public function statusCheck(Request $r)
    {
        $data = $r->validate([
            'ticket_number' => ['required', 'string'],
            'email' => ['required', 'email'],
        ]);

        $t = Ticket::where('ticket_number', $data['ticket_number'])->first();
        if (!$t || $t->reporter_email !== $data['email']) {
            return back()->withErrors(['not_found' => 'Tiket tidak ditemukan atau email tidak cocok.']);
        }

        // Simpan verifikasi email di session untuk akses ke halaman show
        session(['ticket_verified_email_' . $t->ticket_number => $data['email']]);

        return redirect()->route('portal.ticket.show', $t->ticket_number);
    }

    private function extractCustomFields(HelpTopic $topic, Request $r): ?array
    {
        $schema = $topic->form_schema ?? null;
        if (!$schema)
            return null;

        $arr = is_array($schema) ? $schema : json_decode($schema, true);
        $keys = collect($arr ?: [])->pluck('name')->toArray();

        return array_intersect_key($r->all(), array_flip($keys));
    }

    private function storeAttachments(TicketThread $thread, array $files): void
    {
        $encryptionService = app(FileEncryptionService::class);
        
        foreach ($files as $f) {
            if (!$f)
                continue;
            
            // Enkripsi dan simpan file
            $fileData = $encryptionService->storeEncrypted($f, 'attachments');
            
            Attachment::create([
                'ticket_thread_id' => $thread->id,
                'filename' => $fileData['encrypted_filename'],
                'original_filename' => $fileData['original_filename'],
                'mime' => $fileData['mime'],
                'size' => $fileData['size'], // Ukuran asli
                'path' => $fileData['path'],
                'is_encrypted' => true,
            ]);
        }
    }
}
