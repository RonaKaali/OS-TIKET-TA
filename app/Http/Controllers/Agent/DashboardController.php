<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:admin.panel']);
    }

    public function __invoke()
    {
        // Hitung overdue berdasarkan hari kerja (lebih dari 5 hari kerja)
        $overdueCount = Ticket::whereNull('closed_at')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->get()
            ->filter(function ($ticket) {
                return $ticket->isOverdue();
            })
            ->count();

        $stats = [
            'open' => Ticket::whereHas('status', fn($q) => $q->where('slug', 'open'))->count(),
            'answered' => Ticket::whereHas('status', fn($q) => $q->where('slug', 'answered'))->count(),
            'overdue' => $overdueCount,
            'closed' => Ticket::whereHas('status', fn($q) => $q->where('slug', 'closed'))->count(),
        ];

        return view('agent.dashboard', compact('stats'));
    }
}
