<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\{Ticket, User, Status};
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
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $agent = User::findOrFail($data['user_id']);

        // Validasi bahwa user yang dipilih harus memiliki permission admin.panel
        // atau memiliki role yang relevan (Agent, Admin, Super Admin)
        if (!$agent->can('admin.panel') && !$agent->hasAnyRole(['Super Admin', 'Admin', 'Agent', 'Support Agent'])) {
            return back()->withErrors(['user_id' => 'User yang dipilih tidak memiliki akses sebagai agent.']);
        }

        // Load relasi yang diperlukan
        $ticket->load(['status', 'priority', 'department']);

        // Update assignment
        $ticket->update(['assigned_to' => $agent->id]);

        // Update status menjadi "assigned" jika ada
        if ($assignedStatus = Status::where('slug', 'assigned')->first()) {
            $ticket->update(['status_id' => $assignedStatus->id]);
        }

        // Kirim notifikasi email ke agent yang ditugaskan
        try {
            $agent->notify(new \App\Notifications\TicketAssigned($ticket));
            Log::info('Email notifikasi assignment berhasil dikirim ke: ' . $agent->email);
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email assignment ke agent: ' . $e->getMessage());
        }

        return back()->with('ok', 'Tiket telah di-assign ke ' . $agent->name . '.');
    }
}
