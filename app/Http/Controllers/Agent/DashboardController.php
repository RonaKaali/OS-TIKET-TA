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

        // Get tickets assigned to current agent with relationships
        $tickets = Ticket::where('assigned_to', $userId)
            ->with(['status', 'priority', 'department'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate stats for this agent by checking status_id
        $stats = [
            'assigned' => Ticket::where('assigned_to', $userId)
                ->whereHas('status', fn($q) => $q->where('slug', 'open'))
                ->count(),
            'in_progress' => Ticket::where('assigned_to', $userId)
                ->whereHas('status', fn($q) => $q->where('slug', 'in_progress'))
                ->count(),
            'completed' => Ticket::where('assigned_to', $userId)
                ->whereHas('status', fn($q) => $q->where('slug', 'closed'))
                ->count(),
        ];

        return view('agent.dashboard', compact('stats', 'tickets'));
    }
}
