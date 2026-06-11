<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Support\AssignmentAcknowledgment;
use App\Support\RoleUi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:admin.panel']);
    }

    public function __invoke(Request $request)
    {
        $user = $request->user();
        $view = RoleUi::dashboardView($user);

        if (RoleUi::canManageAllTickets($user)) {
            $overdueCount = Ticket::whereNull('closed_at')
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->get()
                ->filter(fn ($ticket) => $ticket->isOverdue())
                ->count();

            $stats = [
                'open' => Ticket::whereHas('status', fn ($q) => $q->where('slug', 'open'))->count(),
                'answered' => Ticket::whereHas('status', fn ($q) => $q->where('slug', 'answered'))->count(),
                'overdue' => $overdueCount,
                'closed' => Ticket::whereHas('status', fn ($q) => $q->where('slug', 'closed'))->count(),
                'unassigned' => Ticket::whereNull('assigned_to')
                    ->whereHas('status', fn ($q) => $q->whereNotIn('slug', ['closed']))
                    ->count(),
            ];

            $unassignedTickets = Ticket::with(['status', 'priority', 'department'])
                ->whereNull('assigned_to')
                ->whereHas('status', fn ($q) => $q->whereNotIn('slug', ['closed']))
                ->latest()
                ->limit(8)
                ->get();

            return view($view, compact('stats', 'unassignedTickets'));
        }

        $myQuery = Ticket::query()->where('assigned_to', $user->id);

        $stats = [
            'assigned' => (clone $myQuery)->whereHas('status', fn ($q) => $q->where('slug', 'assigned'))->count(),
            'in_progress' => (clone $myQuery)->whereHas('status', fn ($q) => $q->whereIn('slug', ['in_progress', 'in-progress', 'dalam-proses']))->count(),
            'closed' => (clone $myQuery)->whereHas('status', fn ($q) => $q->where('slug', 'closed'))->count(),
            'pending_ack' => AssignmentAcknowledgment::pendingFor($user, AssignmentAcknowledgment::map($request))->count(),
        ];

        $myTickets = Ticket::with(['status', 'priority', 'department'])
            ->where('assigned_to', $user->id)
            ->whereHas('status', fn ($q) => $q->whereNotIn('slug', ['closed']))
            ->orderByDesc('assigned_at')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        return view($view, compact('stats', 'myTickets'));
    }
}
