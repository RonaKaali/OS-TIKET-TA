<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Support\AssignmentAcknowledgment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mengambil tiket-tiket terbaru untuk notifikasi lonceng (admin & agent).
 * Admin melihat semua tiket baru, agent hanya yang di-assign ke mereka.
 */
class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:admin.panel']);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $map = AssignmentAcknowledgment::map($request);

        // Admin/Super Admin: semua tiket terbaru (belum di-assign atau baru masuk)
        // Agent: tiket yang di-assign ke mereka
        $query = Ticket::query()->with(['status', 'priority']);

        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            // Admin lihat semua tiket terbaru
            $query->latest('created_at');
        } else {
            // Agent hanya lihat tiket yang di-assign ke mereka
            $query->where('assigned_to', $user->id)
                  ->latest('assigned_at');
        }

        $tickets = $query->take(15)->get()->map(fn (Ticket $t) => [
            'id' => $t->id,
            'ticket_number' => $t->ticket_number,
            'subject' => $t->subject,
            'status' => $t->status?->name ?? 'Open',
            'priority' => $t->priority?->name,
            'created_at' => $t->created_at->diffForHumans(),
            'url' => route('agent.tickets.show', $t),
            'acknowledged' => AssignmentAcknowledgment::isAcknowledged($t, $map) || !is_null($t->acknowledged_at),
        ])->values();

        $unacknowledgedCount = $tickets->where('acknowledged', false)->count();

        return response()->json([
            'notifications' => $tickets,
            'unacknowledged_count' => $unacknowledgedCount,
        ]);
    }

    /**
     * Tandai notifikasi sudah dibaca.
     */
    public function markRead(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ticket_ids' => ['required', 'array'],
            'ticket_ids.*' => ['integer', 'exists:tiket,id'],
        ]);

        $user = $request->user();
        AssignmentAcknowledgment::acknowledge($request, $user, $data['ticket_ids']);

        return response()->json(['status' => 'ok']);
    }
}
