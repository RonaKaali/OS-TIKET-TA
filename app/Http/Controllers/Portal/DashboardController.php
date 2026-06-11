<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Construct - require auth untuk portal dashboard
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan dashboard dengan list semua tiket user
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Query tiket user dengan pagination
        $query = Ticket::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('reporter_email', $user->email);
            })
            ->with(['status', 'priority', 'department'])
            ->withCount('threads');

        // Sort parameter (default: created_at DESC)
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['created_at', 'updated_at', 'priority_id', 'status_id'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderByDesc('created_at');
        }

        // Filter by status (optional)
        if ($status = $request->get('status')) {
            $query->whereHas('status', function ($q) use ($status) {
                $q->where('slug', $status);
            });
        }

        // Search (optional)
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Pagination (15 per page)
        $tickets = $query->paginate(15);

        // Stats untuk dashboard
        $stats = [
            'total' => Ticket::where('user_id', $user->id)
                ->orWhere('reporter_email', $user->email)
                ->count(),
            'open' => Ticket::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('reporter_email', $user->email);
            })
                ->whereHas('status', fn ($q) => $q->whereNotIn('slug', ['closed', 'resolved']))
                ->count(),
            'closed' => Ticket::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('reporter_email', $user->email);
            })
                ->whereHas('status', fn ($q) => $q->whereIn('slug', ['closed', 'resolved']))
                ->count(),
        ];

        return view('portal.dashboard.index', compact('tickets', 'stats', 'user'));
    }
}
