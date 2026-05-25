<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SecurityDashboardController extends Controller
{
    /**
     * Display the Zero Trust Security Dashboard.
     */
    public function index()
    {
        // Get initial stats for the header
        $stats = [
            'total_events' => SecurityEvent::count(),
            'today_events' => SecurityEvent::whereDate('created_at', today())->count(),
            'high_risk_count' => SecurityEvent::whereIn('severity', ['high', 'critical'])->count(),
            'avg_trust_score' => round(DB::table('device_fingerprints')->avg('trust_score') ?? 0, 1),
        ];

        return view('admin.security.dashboard', compact('stats'));
    }

    /**
     * Get latest security events for the live feed (API).
     */
    public function getLatestEvents()
    {
        $events = SecurityEvent::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'user_name' => $event->user ? $event->user->name : 'Guest/System',
                    'event_type' => $event->event_type,
                    'severity' => $event->severity,
                    'ip_address' => $event->ip_address,
                    'message' => $event->message,
                    'risk_score' => $event->risk_score,
                    'metadata' => $event->metadata,
                    'time_diff' => $event->created_at->diffForHumans(),
                    'created_at' => $event->created_at->format('H:i:s'),
                ];
            });

        return response()->json($events);
    }
}
