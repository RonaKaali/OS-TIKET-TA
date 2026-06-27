<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Status;
use App\Models\User;
use App\Services\SecurityEventLogService;
use App\Support\RoleUi;
use App\Traits\LoggableActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    use LoggableActivity;
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        if (!$user->hasRole(RoleUi::SUPPORT_AGENT)) {
            abort(403, 'Akses ditolak. Anda bukan Kepala Bidang.');
        }

        $pendingStatus = Status::where('slug', 'menunggu_verifikasi_kepala_bidang')->first();
        if (!$pendingStatus) {
            $tickets = collect();
        } else {
            $tickets = Ticket::with(['priority', 'department', 'assignee'])
                ->where('status_id', $pendingStatus->id)
                ->latest()
                ->paginate(10);
        }

        return view('agent.dashboard.verification-list', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $user = auth()->user();
        if (!$user->hasRole(RoleUi::SUPPORT_AGENT)) {
            abort(403, 'Akses ditolak. Anda bukan Kepala Bidang.');
        }

        $pendingStatus = Status::where('slug', 'menunggu_verifikasi_kepala_bidang')->first();
        if ($ticket->status_id !== $pendingStatus?->id) {
            return redirect()->route('agent.verification.index')
                ->with('error', 'Tiket ini sudah diverifikasi atau tidak dalam status verifikasi.');
        }

        $ticket->load(['threads.attachments', 'status', 'priority', 'department', 'assignee', 'requester']);

        // Log aktivitas READ (Kepala Bidang melihat surat tugas untuk verifikasi)
        try {
            app(SecurityEventLogService::class)->logActivity(
                userId: $user->id,
                action: 'READ',
                resource: 'Verification',
                resourceId: $ticket->id,
                resourceName: $ticket->ticket_number,
                additionalContext: ['ticket_subject' => $ticket->subject]
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal log aktivitas verifikasi: ' . $e->getMessage());
        }

        return view('agent.dashboard.verification-show', compact('ticket'));
    }

    public function verify(Request $request, Ticket $ticket)
    {
        $user = auth()->user();
        if (!$user->hasRole(RoleUi::SUPPORT_AGENT)) {
            abort(403, 'Akses ditolak. Anda bukan Kepala Bidang.');
        }

        $pendingStatus = Status::where('slug', 'menunggu_verifikasi_kepala_bidang')->first();
        if ($ticket->status_id !== $pendingStatus?->id) {
            return back()->with('error', 'Tiket ini tidak sedang menunggu verifikasi.');
        }

        $assignedStatus = Status::where('slug', 'assigned')->first();
        if ($assignedStatus) {
            $ticket->update(['status_id' => $assignedStatus->id]);

        // Log aktivitas UPDATE (Kepala Bidang memverifikasi surat tugas)
        try {
            app(SecurityEventLogService::class)->logActivity(
                userId: $user->id,
                action: 'UPDATE',
                resource: 'Verification',
                resourceId: $ticket->id,
                resourceName: $ticket->ticket_number,
                changes: [
                    'status_id' => [
                        'old' => $pendingStatus->name ?? $pendingStatus->slug,
                        'new' => $assignedStatus->name ?? $assignedStatus->slug,
                    ]
                ],
                additionalContext: [
                    'ticket_subject' => $ticket->subject,
                    'assigned_agent_id' => $ticket->assigned_to,
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal log aktivitas verifikasi: ' . $e->getMessage());
        }

        // Kirim notifikasi email ke agent yang ditugaskan setelah diverifikasi
        try {
            $agent = User::find($ticket->assigned_to);
            if ($agent) {
                $agent->notify(new \App\Notifications\TicketAssigned($ticket));
                Log::info('Email notifikasi assignment berhasil dikirim ke: ' . $agent->email);
            }
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email assignment ke agent: ' . $e->getMessage());
        }

        return back()->with('ok', 'Surat tugas untuk tiket #' . $ticket->ticket_number . ' telah diverifikasi dan diteruskan ke Agen.');
    }
}
