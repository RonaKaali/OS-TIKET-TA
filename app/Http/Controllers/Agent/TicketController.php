<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\{Ticket, TicketThread, Status, Attachment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:admin.panel']);
    }

    public function index(Request $r)
    {
        $q = Ticket::query()->with(['status', 'priority', 'department', 'assignee']);

        if ($r->filled('status')) {
            $q->whereHas('status', fn($qq) => $qq->where('slug', $r->status));
        }
        if ($r->filled('dept')) {
            $q->where('id_departemen', $r->dept);
        }
        if ($r->filled('assigned')) {
            $q->where('ditugaskan_ke', $r->assigned);
        }
        if ($r->filled('search')) {
            $s = '%' . $r->search . '%';
            $q->where(function ($qq) use ($s) {
                $qq->where('nomor_tiket', 'like', $s)
                    ->orWhere('subjek', 'like', $s)
                    ->orWhere('email_pelapor', 'like', $s);
            });
        }

        // Batasi untuk agen biasa: hanya lihat tiket yang ditugaskan kepadanya
        if (!$r->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            $q->where('ditugaskan_ke', $r->user()->id);
        }

        $tickets = $q->latest()->paginate(20)->withQueryString();

        return view('agent.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['threads.attachments', 'status', 'priority', 'department', 'assignee']);

        // Cegah agen melihat tiket yang bukan miliknya, kecuali admin/super admin
        if (!request()->user()->hasAnyRole(['Super Admin', 'Admin']) && $ticket->ditugaskan_ke !== request()->user()->id) {
            abort(403);
        }

        // Ambil daftar agent yang bisa ditugaskan (hanya yang punya permission admin.panel)
        $agents = \App\Models\User::whereHas('permissions', function ($q) {
            $q->where('permissions.name', 'admin.panel');
        })->orWhereHas('roles', function ($q) {
            $q->whereIn('roles.name', ['Super Admin', 'Admin', 'Agent', 'Support Agent']);
        })->with('roles')->distinct()->get();

        return view('agent.tickets.show', compact('ticket', 'agents'));
    }

    public function reply(Request $r, Ticket $ticket)
    {
        $data = $r->validate([
            'message' => ['required', 'string', 'max:20000'],
            'attachments.*' => ['file', 'max:10240'],
        ]);

        $thread = TicketThread::create([
            'id_tiket' => $ticket->id,
            'tipe' => 'balasan',
            'id_pengguna' => $r->user()->id,
            'isi' => $data['message'],
        ]);

        foreach ($r->file('attachments', []) as $f) {
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

        // Kembalikan status ke "open" (menunggu pelapor)
        if ($openId = Status::where('slug', 'open')->value('id')) {
            $ticket->update(['id_status' => $openId]);
        }

        // Notifikasi ke pelapor (email + telegram jika user terdaftar)
        try {
            // Cari user berdasarkan email
            $requester = \App\Models\User::where('email', $ticket->email_pelapor)->first();

            if ($requester) {
                // User terdaftar, kirim ke semua channel (email + telegram)
                $requester->notify(new \App\Notifications\TicketReplyFromAgent($ticket, $thread));
                Log::info('Notifikasi balasan dikirim ke pelapor: ' . $ticket->email_pelapor . ' (email' . (!empty($requester->nama_pengguna_telegram) ? ' + telegram' : '') . ')');
            } else {
                // Guest user, hanya email
                Notification::route('mail', $ticket->email_pelapor)
                    ->notify(new \App\Notifications\TicketReplyFromAgent($ticket, $thread));
                Log::info('Notifikasi balasan dikirim ke pelapor (guest): ' . $ticket->email_pelapor);
            }
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim notifikasi ke pelapor: ' . $e->getMessage());
        }

        return back()->with('ok', 'Balasan terkirim.');
    }

    public function setStatus(Request $r, Ticket $ticket)
    {
        $data = $r->validate([
            'status_id' => ['required', 'exists:status,id'],
        ]);

        $oldStatus = $ticket->status;
        $newStatus = Status::findOrFail($data['status_id']);

        // Load relasi yang diperlukan
        $ticket->load(['status', 'priority', 'department']);

        $ticket->update([
            'id_status' => $newStatus->id,
            'ditutup_pada' => $newStatus->menutup ? now() : null,
        ]);

        // Reload ticket dengan status baru
        $ticket->refresh();

        // Kirim notifikasi email ke pelapor jika status berubah menjadi "Dalam Proses" atau "Tertutup" atau "Ditugaskan"
        // Cek berdasarkan slug atau nama status
        $statusSlug = strtolower($newStatus->slug);
        $statusName = strtolower($newStatus->nama);

        $shouldNotify = in_array($statusSlug, ['in_progress', 'in-progress', 'dalam-proses', 'closed', 'tertutup', 'assigned', 'ditugaskan']) ||
            str_contains($statusName, 'dalam proses') ||
            str_contains($statusName, 'tertutup') ||
            str_contains($statusName, 'ditugaskan');

        if ($shouldNotify) {
            try {
                // Cari user berdasarkan email
                $requester = \App\Models\User::where('email', $ticket->email_pelapor)->first();

                if ($requester) {
                    // User terdaftar, kirim ke semua channel (email + telegram)
                    $requester->notify(new \App\Notifications\TicketStatusChanged($ticket, $oldStatus, $newStatus));
                    Log::info('Notifikasi status dikirim ke pelapor: ' . $ticket->email_pelapor . ' (email' . (!empty($requester->nama_pengguna_telegram) ? ' + telegram' : '') . ')');
                } else {
                    // Guest user, hanya email
                    Notification::route('mail', $ticket->email_pelapor)
                        ->notify(new \App\Notifications\TicketStatusChanged($ticket, $oldStatus, $newStatus));
                    Log::info('Notifikasi status dikirim ke pelapor (guest): ' . $ticket->email_pelapor);
                }
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim notifikasi status ke pelapor: ' . $e->getMessage());
            }
        }

        return back()->with('ok', 'Status diperbarui.');
    }
}
