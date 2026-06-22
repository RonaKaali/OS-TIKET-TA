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

        // Admin bell dismiss tracking (session-based)
        $adminDismissed = $request->session()->get('admin_notifications_read', []);

        $tickets = $query->take(15)->get()->map(function (Ticket $t) use ($map, $adminDismissed) {
            $acknowledged = AssignmentAcknowledgment::isAcknowledged($t, $map)
                || !is_null($t->acknowledged_at)
                || in_array($t->id, $adminDismissed);

            return [
                'id' => $t->id,
                'ticket_number' => $t->ticket_number,
                'subject' => $t->subject,
                'status' => $t->status?->name ?? 'Open',
                'priority' => $t->priority?->name,
                'created_at' => $t->created_at->diffForHumans(),
                'url' => route('agent.tickets.show', $t),
                'acknowledged' => $acknowledged,
            ];
        })->values();

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

        // Agent: acknowledge via assignment system (hanya tiket yang di-assign)
        AssignmentAcknowledgment::acknowledge($request, $user, $data['ticket_ids']);

        // Admin/Super Admin: juga simpan ke session agar tiket yang tidak
        // di-assign ke admin tetap bisa ditandai sudah dibaca
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            $adminDismissed = $request->session()->get('admin_notifications_read', []);
            $adminDismissed = array_unique(array_merge($adminDismissed, $data['ticket_ids']));

            // Batasi 500 ID terakhir agar session tidak membengkak
            if (count($adminDismissed) > 500) {
                $adminDismissed = array_slice($adminDismissed, -500);
            }

            $request->session()->put('admin_notifications_read', $adminDismissed);
        }

        return response()->json(['status' => 'ok']);
    }
}
