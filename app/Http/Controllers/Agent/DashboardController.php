<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke()
    {
        $userId = Auth::id();

        // Get tickets assigned to current agent
        $allTickets = Ticket::where('assigned_to', $userId)->get();

        // Calculate stats for this agent
        $stats = [
            'assigned' => $allTickets->where('status', 'open')->count(),
            'in_progress' => $allTickets->where('status', 'in_progress')->count(),
            'completed' => $allTickets->where('status', 'closed')->count(),
        ];

        // Get tickets to display (limit to 10 most recent)
        $tickets = Ticket::where('assigned_to', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('agent.dashboard', compact('stats', 'tickets'));
    }
}
