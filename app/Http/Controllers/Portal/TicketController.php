<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\{Ticket, TicketThread, HelpTopic, SlaPlan, Status, Attachment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function create()
    {
        $topics = HelpTopic::with('department')->orderBy('nama')->get();
        return view('portal.ticket.create', compact('topics'));
    }

    public function store(Request $r)
    {
        $user = $r->user();

        $data = $r->validate([
            'subject' => ['required', 'string', 'max:255'],
            'help_topic_id' => ['required', Rule::exists('topik_bantuan', 'id')],
            'priority_id' => ['nullable', Rule::exists('prioritas', 'id')],
            'message' => ['required', 'string', 'max:20000'],
            'attachments.*' => ['file', 'max:10240'], // 10MB
        ]);

        $topic = HelpTopic::with('department')->findOrFail($data['help_topic_id']);
        $status = Status::where('slug', 'open')->firstOrFail();
        $slaId = SlaPlan::value('id');
        $grace = SlaPlan::whereKey($slaId)->value('jam_grace') ?? 48;

        $ticket = Ticket::create([
            'subjek' => $data['subject'],
            'email_pelapor' => $user->email,
            'nama_pelapor' => $user->nama,
            'id_pengguna' => $user->id, // Link ke user account
            'id_departemen' => $topic->id_departemen,
            'id_topik_bantuan' => $topic->id,
            'id_prioritas' => $data['priority_id'] ?? null,
            'id_status' => $status->id,
            'id_rencana_sla' => $slaId,
            'jatuh_tempo_pada' => now()->addHours($grace),
            'bidang_kustom' => $this->extractCustomFields($topic, $r),
        ]);

        $thread = TicketThread::create([
            'id_tiket' => $ticket->id,
            'tipe' => 'pesan',
            'id_pengguna' => $user->id, // Link ke user account
            'isi' => $data['message'],
        ]);

        $this->storeAttachments($thread, $r->file('attachments', []));

        // Load relasi yang diperlukan untuk email notifikasi
        $ticket->load(['status', 'priority', 'department']);

        // Notifikasi ke pelapor (email + telegram)
        try {
            // Gunakan $user->notify() agar semua channel (mail + telegram) terkirim
            $user->notify(new \App\Notifications\NewTicketSubmitted($ticket, $thread));

            \Log::info('Notifikasi berhasil dikirim ke pelapor: ' . $user->email . ' (email' . (!empty($user->telegram_username) ? ' + telegram' : '') . ')');
        } catch (\Throwable $e) {
            \Log::error('Gagal mengirim notifikasi ke pelapor: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        // Notifikasi ke agent/admin yang memiliki akses admin.panel
        try {
            $agents = \App\Models\User::whereHas('permissions', function ($q) {
                $q->where('permissions.name', 'admin.panel');
            })->orWhereHas('roles', function ($q) {
                $q->whereIn('roles.name', ['Super Admin', 'Admin', 'Agent', 'Support Agent']);
            })->get();

            if ($agents->isNotEmpty()) {
                \Notification::send($agents, new \App\Notifications\NewTicketCreated($ticket, $thread));
                \Log::info('Email notifikasi berhasil dikirim ke ' . $agents->count() . ' admin/agent: ' . $agents->pluck('email')->implode(', '));
            } else {
                \Log::warning('Tidak ada admin/agent yang ditemukan untuk menerima notifikasi laporan baru');
            }
        } catch (\Throwable $e) {
            \Log::error('Gagal mengirim email ke agent: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        return redirect()
            ->route('portal.ticket.show', $ticket->nomor_tiket)
            ->with('ok', 'Tiket berhasil dibuat.');
    }

    public function show(string $number)
    {
        $ticket = Ticket::where('nomor_tiket', $number)
            ->with(['threads.attachments', 'status', 'priority', 'department'])
            ->firstOrFail();

        $user = request()->user();

        // Jika user login dan dia pemilik tiket, langsung allow
        if ($user && ($ticket->id_pengguna === $user->id || $ticket->email_pelapor === $user->email)) {
            return view('portal.ticket.show', compact('ticket'));
        }

        // Jika tidak login, cek apakah ada sesi verifikasi dari statusCheck
        $verifiedEmail = session('ticket_verified_email_' . $ticket->nomor_tiket);
        if ($verifiedEmail && $verifiedEmail === $ticket->email_pelapor) {
            return view('portal.ticket.show', compact('ticket'));
        }

        // Jika tidak ada verifikasi, redirect ke status form
        return redirect()->route('portal.ticket.status.form')
            ->withErrors(['not_found' => 'Silakan verifikasi dengan nomor tiket dan email terlebih dahulu.']);
    }

    public function reply(Request $r, string $number)
    {
        $ticket = Ticket::where('nomor_tiket', $number)->firstOrFail();
        $user = $r->user();

        // Cek apakah user yang login adalah pemilik tiket
        abort_unless(
            $user && ($ticket->id_pengguna === $user->id || $ticket->email_pelapor === $user->email),
            403,
            'Anda tidak memiliki akses ke tiket ini.'
        );

        $data = $r->validate([
            'message' => ['required', 'string', 'max:20000'],
            'attachments.*' => ['file', 'max:10240'],
        ]);

        $thread = TicketThread::create([
            'id_tiket' => $ticket->id,
            'tipe' => 'pesan',
            'id_pengguna' => $user->id, // Link ke user account
            'isi' => $data['message'],
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

        $t = Ticket::where('nomor_tiket', $data['ticket_number'])->first();
        if (!$t || $t->email_pelapor !== $data['email']) {
            return back()->withErrors(['not_found' => 'Tiket tidak ditemukan atau email tidak cocok.']);
        }

        // Simpan verifikasi email di session untuk akses ke halaman show
        session(['ticket_verified_email_' . $t->nomor_tiket => $data['email']]);

        return redirect()->route('portal.ticket.show', $t->nomor_tiket);
    }

    private function extractCustomFields(HelpTopic $topic, Request $r): ?array
    {
        $schema = $topic->skema_formulir ?? null;
        if (!$schema)
            return null;

        $arr = is_array($schema) ? $schema : json_decode($schema, true);
        $keys = collect($arr ?: [])->pluck('name')->toArray();

        return array_intersect_key($r->all(), array_flip($keys));
    }

    private function storeAttachments(TicketThread $thread, array $files): void
    {
        foreach ($files as $f) {
            if (!$f)
                continue;
            $path = $f->store('attachments', 'public');
            Attachment::create([
                'id_utas_tiket' => $thread->id,
                'nama_file' => $f->getClientOriginalName(),
                'mime' => $f->getClientMimeType(),
                'ukuran' => $f->getSize(),
                'path' => $path,
            ]);
        }
    }
}
