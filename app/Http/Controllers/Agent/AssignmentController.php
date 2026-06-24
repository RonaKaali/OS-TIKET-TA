<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\{Ticket, User, Status};
use App\Support\RoleUi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:tickets.assign']);
    }

    public function __invoke(Request $r, Ticket $ticket)
    {
        $data = $r->validate([
            'user_id' => ['required', 'exists:pengguna,id'],
            'priority_id' => ['required', 'exists:prioritas,id'],
        ]);

        $agent = User::findOrFail($data['user_id']);

        // Validasi bahwa user yang dipilih harus memiliki permission admin.panel
        // atau memiliki role yang relevan (Agent, Admin, Super Admin)
        if (!$agent->can('admin.panel') && !$agent->hasAnyRole(RoleUi::ASSIGNABLE_AGENT_ROLES)) {
            return back()->withErrors(['user_id' => 'User yang dipilih tidak memiliki akses sebagai agent.']);
        }

        // Load relasi yang diperlukan
        $ticket->load(['status', 'priority', 'department']);

        // Update assignment and priority
        $ticket->update([
            'assigned_to' => $agent->id,
            'assigned_at' => now(),
            'priority_id' => $data['priority_id'],
        ]);

        // Update status menjadi "menunggu_verifikasi_kepala_bidang" jika ada
        if ($pendingStatus = Status::where('slug', 'menunggu_verifikasi_kepala_bidang')->first()) {
            $ticket->update(['status_id' => $pendingStatus->id]);
        }

        return back()->with('ok', 'Tiket telah di-assign ke ' . $agent->name . ' dan menunggu verifikasi Kepala Bidang.');
    }
}
