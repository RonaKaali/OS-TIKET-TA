<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get pending assignments for the logged-in agent
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil tiket yang ditugaskan ke agent ini dan belum di-acknowledge
        $assignments = Ticket::where('assigned_to', $user->id)
            ->whereNull('acknowledged_at')
            ->with(['priority', 'status', 'department'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'subject' => $ticket->subject,
                    'priority' => $ticket->priority?->name,
                    'status' => $ticket->status?->name,
                    'url' => route('agent.tickets.show', $ticket->ticket_number),
                ];
            });

        return response()->json([
            'assignments' => $assignments,
        ]);
    }

    /**
     * Acknowledge/confirm assignments
     */
    public function acknowledge(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'required|integer|exists:tiket,id',
        ]);

        // Update tickets - set acknowledged_at untuk tiket yang milik user
        Ticket::whereIn('id', $validated['ticket_ids'])
            ->where('assigned_to', $user->id)
            ->update([
                'acknowledged_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Assignments acknowledged successfully',
        ]);
    }
}
