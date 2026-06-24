<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\{Ticket, TicketThread, Status, Attachment, CannedResponse};
use App\Services\FileEncryptionService;
use App\Support\RoleUi;
use App\Traits\LoggableActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    use LoggableActivity;
    public function __construct()
    {
        $this->middleware(['auth', 'permission:admin.panel']);
    }

    public function index(Request $r)
    {
        $q = Ticket::query()->with(['status', 'priority', 'department', 'assignee', 'requester']);

        if ($r->filled('status')) {
            $q->whereHas('status', fn($qq) => $qq->where('slug', $r->status));
        }
        if ($r->filled('dept')) {
            $q->where('department_id', $r->dept);
        }
        if ($r->filled('assigned')) {
            $q->where('assigned_to', $r->assigned);
        }
        if ($r->filled('search')) {
            $s = '%' . $r->search . '%';
            $q->where(function ($qq) use ($s) {
                $qq->where('ticket_number', 'like', $s)
                    ->orWhere('subject', 'like', $s)
                    ->orWhere('reporter_email', 'like', $s);
            });
        }

        // Batasi untuk agen lapangan: hanya lihat tiket yang ditugaskan kepadanya
        if (!RoleUi::canManageAllTickets($r->user())) {
            $q->where('assigned_to', $r->user()->id)
              ->whereHas('status', fn($qq) => $qq->where('slug', '!=', 'menunggu_verifikasi_kepala_bidang'));
        }

        $tickets = $q->latest()->paginate(20)->withQueryString();

        // Log aktivitas READ (list)
        $this->logRead('Ticket', null, [
            'action' => 'list',
            'filters' => $r->only(['status', 'dept', 'assigned', 'search']),
        ]);

        return view('agent.tickets.index', compact('tickets'));
    }

    public function print(Ticket $ticket)
    {
        $ticket->load(['department', 'assignee', 'priority', 'requester']);

        if (!RoleUi::canManageAllTickets(request()->user()) && $ticket->assigned_to !== request()->user()->id) {
            abort(403);
        }

        return view('agent.tickets.print', compact('ticket'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['threads.attachments', 'status', 'priority', 'department', 'assignee', 'requester']);

        // Cegah agen melihat tiket yang bukan miliknya, kecuali admin/super admin
        if (!RoleUi::canManageAllTickets(request()->user()) && $ticket->assigned_to !== request()->user()->id) {
            abort(403);
        }

        // Auto-acknowledge: jika agent yang ditugaskan melihat tiket ini dan belum di-acknowledge
        if ($ticket->assigned_to === request()->user()->id && is_null($ticket->acknowledged_at)) {
            $ticket->update(['acknowledged_at' => now()]);
            Log::info('Agent ' . request()->user()->id . ' acknowledged ticket ' . $ticket->ticket_number);

            // Sinkronisasi session acknowledgment
            \App\Support\AssignmentAcknowledgment::acknowledge(request(), request()->user(), [$ticket->id]);
        }

        // Daftar agen yang bisa ditugaskan - HANYA Agent 2
        $agents = \App\Models\User::whereHas('roles', function ($q) {
            $q->whereIn('roles.name', RoleUi::ASSIGNABLE_AGENT_ROLES);
        })->with('roles')->get()->unique('id');

        // Ambil daftar canned responses untuk dipilih agent
        $cannedResponses = CannedResponse::latest()->get();

        // Log aktivitas READ
        $this->logRead('Ticket', $ticket, ['ticket_number' => $ticket->ticket_number]);

        return view('agent.tickets.show', compact('ticket', 'agents', 'cannedResponses'));
    }

    public function reply(Request $r, Ticket $ticket)
    {
        $this->authorizeTicketAction($ticket, $r->user());

        $data = $r->validate([
            'message' => ['required', 'string', 'max:20000'],
            'attachments.*' => $this->attachmentRules(),
        ]);

        $thread = TicketThread::create([
            'ticket_id' => $ticket->id,
            'type' => 'reply',
            'user_id' => $r->user()->id,
            'body' => $data['message'],
        ]);

        $encryptionService = app(FileEncryptionService::class);
        
        foreach ($r->file('attachments', []) as $f) {
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
                'file_data' => $fileData['encrypted_content'],
                'is_encrypted' => \Illuminate\Support\Facades\DB::raw('true'),
            ]);
        }

        // Kembalikan status ke "open" (menunggu pelapor)
        $oldStatusId = $ticket->status_id;
        if ($openId = Status::where('slug', 'open')->value('id')) {
            $ticket->update(['status_id' => $openId]);
        }

        // Log aktivitas UPDATE (reply mengubah status)
        if ($oldStatusId != $ticket->status_id) {
            $this->logUpdate('Ticket', $ticket, [
                'status_id' => $oldStatusId,
            ], [
                'action' => 'reply',
                'status_changed' => true,
            ]);
        }

        // Notifikasi ke pelapor (email + telegram jika user terdaftar)
        try {
            // Cari user berdasarkan email
            $requester = \App\Models\User::where('email', $ticket->reporter_email)->first();

            if ($requester) {
                // User terdaftar, kirim ke semua channel (email + telegram)
                $requester->notify(new \App\Notifications\TicketReplyFromAgent($ticket, $thread));
                Log::info('Notifikasi balasan dikirim ke pelapor: ' . $ticket->reporter_email . ' (email' . (!empty($requester->nama_pengguna_telegram) ? ' + telegram' : '') . ')');
            } else {
                // Guest user, hanya email
                Notification::route('mail', $ticket->reporter_email)
                    ->notify(new \App\Notifications\TicketReplyFromAgent($ticket, $thread));
                Log::info('Notifikasi balasan dikirim ke pelapor (guest): ' . $ticket->reporter_email);
            }
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim notifikasi ke pelapor: ' . $e->getMessage());
        }

        return back()->with('ok', 'Balasan terkirim.');
    }

    public function setStatus(Request $r, Ticket $ticket)
    {
        $this->authorizeTicketAction($ticket, $r->user());

        $data = $r->validate([
            'status_id' => ['required', 'exists:status,id'],
        ]);

        $oldStatus = $ticket->status;
        $newStatus = Status::findOrFail($data['status_id']);

        // Simpan data original untuk logging
        $originalData = [
            'status_id' => $ticket->status_id,
        ];

        // Load relasi yang diperlukan
        $ticket->load(['status', 'priority', 'department']);

        $ticket->update([
            'status_id' => $newStatus->id,
            'closed_at' => $newStatus->is_closed ? now() : null,
        ]);

        // Log aktivitas UPDATE
        $this->logUpdate('Ticket', $ticket, $originalData, [
            'old_status' => $oldStatus->name ?? $oldStatus->slug,
            'new_status' => $newStatus->name ?? $newStatus->slug,
        ]);

        // Reload ticket dengan status baru
        $ticket->refresh();

        // Kirim notifikasi email ke pelapor jika status berubah menjadi "Dalam Proses" atau "Tertutup" atau "Ditugaskan"
        // Cek berdasarkan slug atau nama status
        $statusSlug = strtolower($newStatus->slug);
        $statusName = strtolower($newStatus->name);

        $shouldNotify = in_array($statusSlug, ['in_progress', 'in-progress', 'dalam-proses', 'closed', 'tertutup', 'assigned', 'ditugaskan']) ||
            str_contains($statusName, 'dalam proses') ||
            str_contains($statusName, 'tertutup') ||
            str_contains($statusName, 'ditugaskan');

        if ($shouldNotify) {
            try {
                // Cari user berdasarkan email
                $requester = \App\Models\User::where('email', $ticket->reporter_email)->first();

                if ($requester) {
                    // User terdaftar, kirim ke semua channel (email + telegram)
                    $requester->notify(new \App\Notifications\TicketStatusChanged($ticket, $oldStatus, $newStatus));
                    Log::info('Notifikasi status dikirim ke pelapor: ' . $ticket->reporter_email . ' (email' . (!empty($requester->nama_pengguna_telegram) ? ' + telegram' : '') . ')');
                } else {
                    // Guest user, hanya email
                    Notification::route('mail', $ticket->reporter_email)
                        ->notify(new \App\Notifications\TicketStatusChanged($ticket, $oldStatus, $newStatus));
                    Log::info('Notifikasi status dikirim ke pelapor (guest): ' . $ticket->reporter_email);
                }
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim notifikasi status ke pelapor: ' . $e->getMessage());
            }
        }

        return back()->with('ok', 'Status diperbarui.');
    }

    /**
     * Tandai tiket sebagai selesai (closed) dan kirim notifikasi ke pelapor.
     */
    public function complete(Ticket $ticket)
    {
        $this->authorizeTicketAction($ticket, request()->user(), 'Anda tidak dapat menyelesaikan tiket yang bukan tugas Anda.');

        $oldStatus = $ticket->status;
        $closedStatus = Status::where('slug', 'closed')->orWhere('name', 'like', '%selesai%')->first();

        if (!$closedStatus) {
            return back()->withErrors('Status "Selesai" tidak ditemukan. Hubungi Super Admin.');
        }

        $ticket->update([
            'status_id' => $closedStatus->id,
            'closed_at' => now(),
        ]);

        $ticket->refresh();

        // Log aktivitas
        $this->logUpdate('Ticket', $ticket, ['status_id' => $oldStatus->id ?? null], [
            'action' => 'completed_by_agent',
            'agent_id' => request()->user()->id,
        ]);

        // Kirim notifikasi email ke pelapor
        try {
            $requester = \App\Models\User::where('email', $ticket->reporter_email)->first();
            if ($requester) {
                $requester->notify(new \App\Notifications\TicketStatusChanged($ticket, $oldStatus, $closedStatus));
            } else {
                Notification::route('mail', $ticket->reporter_email)
                    ->notify(new \App\Notifications\TicketStatusChanged($ticket, $oldStatus, $closedStatus));
            }
            Log::info('Notifikasi penyelesaian tiket dikirim ke: ' . $ticket->reporter_email);
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim notifikasi selesai: ' . $e->getMessage());
        }

        return back()->with('ok', 'Tiket berhasil diselesaikan. Pelapor telah diberitahu melalui email.');
    }

    /**
     * Kembalikan tiket ke Super Admin (unassign).
     */
    public function returnToAdmin(Ticket $ticket)
    {
        $this->authorizeTicketAction($ticket, request()->user(), 'Anda tidak dapat mengembalikan tiket yang bukan tugas Anda.');

        $oldStatus = $ticket->status;
        $openStatus = Status::where('slug', 'open')->first();

        $ticket->update([
            'assigned_to' => null,
            'assigned_at' => null,
            'acknowledged_at' => null,
            'status_id' => $openStatus ? $openStatus->id : $ticket->status_id,
        ]);

        $ticket->refresh();

        // Log aktivitas
        $this->logUpdate('Ticket', $ticket, [
            'assigned_to' => request()->user()->id,
        ], [
            'action' => 'returned_to_admin',
            'agent_id' => request()->user()->id,
        ]);

        return back()->with('ok', 'Tiket dikembalikan ke Super Admin untuk ditugaskan ulang.');
    }

    protected function authorizeTicketAction(Ticket $ticket, ?\App\Models\User $user, string $message = 'Anda tidak memiliki akses ke tiket ini.'): void
    {
        if (RoleUi::canManageAllTickets($user)) {
            return;
        }

        abort_unless($user && $ticket->assigned_to === $user->id, 403, $message);
    }

    protected function attachmentRules(): array
    {
        return [
            'file',
            'max:10240',
            'mimes:jpg,jpeg,png,gif,pdf,doc,docx',
            'mimetypes:image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
    }
}
