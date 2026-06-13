<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Support\AssignmentAcknowledgment;
use App\Support\RoleUi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:admin.panel']);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!RoleUi::isFieldAgent($user)) {
            return response()->json(['assignments' => [], 'count' => 0]);
        }

        $map = AssignmentAcknowledgment::map($request);

        // Ambil SEMUA tiket yang ditugaskan ke agent ini (tidak hanya pending)
        $assignments = Ticket::query()
            ->with(['status', 'priority'])
            ->where('assigned_to', $user->id)
            ->latest('assigned_at')
            ->latest('updated_at')
            ->take(20)
            ->get()
            ->map(fn (Ticket $ticket) => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'priority' => $ticket->priority?->name,
                'status' => $ticket->status?->name,
                'assigned_at' => $ticket->assigned_at?->diffForHumans() ?? $ticket->updated_at?->diffForHumans(),
                'url' => route('agent.tickets.show', $ticket),
                'acknowledged' => AssignmentAcknowledgment::isAcknowledged($ticket, $map) || !is_null($ticket->acknowledged_at),
                'acknowledged_at' => $ticket->acknowledged_at?->diffForHumans(),
            ])
            ->values();

        // Hitung yang benar-benar belum di-acknowledge
        $unacknowledgedCount = $assignments->where('acknowledged', false)->count();

        return response()->json([
            'assignments' => $assignments,
            'count' => $assignments->count(),
            'unacknowledged_count' => $unacknowledgedCount,
        ]);
    }

    /**
     * Tandai tugas sudah dilihat (tutup popup).
     */
    public function acknowledge(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ticket_ids' => ['required', 'array'],
            'ticket_ids.*' => ['integer', 'exists:tiket,id'],
        ]);

        $user = $request->user();
        AssignmentAcknowledgment::acknowledge($request, $user, $data['ticket_ids']);

        return response()->json([
            'status' => 'ok',
            'acknowledged' => count(AssignmentAcknowledgment::map($request)),
        ]);
    }
}
