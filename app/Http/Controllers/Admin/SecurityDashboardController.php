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
            'avg_trust_score' => round((function() { try { return DB::table('device_fingerprints')->avg('trust_score') ?? 0; } catch (\Exception $e) { return 0; } })(), 1),
        ];

        return view('admin.security.dashboard', compact('stats'));
    }

    /**
     * Get latest security events for the live feed (API).
     */
    public function getLatestEvents()
    {
        $events = SecurityEvent::with('user:id,name,email')
            ->excludeNoise()
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get()
            ->map(function ($event) {
                $context = is_array($event->context) ? $event->context : [];
                $metadata = is_array($event->metadata) ? $event->metadata : [];

                $riskScore = $event->risk_score ?? ($context['risk_score'] ?? null);
                $deviceFingerprint = $event->device_fingerprint ?? ($metadata['device_fingerprint'] ?? null);
                $deviceTrustScore = $metadata['device_trust_score'] ?? null;
                $gps = $context['gps'] ?? null;

                return [
                    'id' => $event->id,
                    'user_id' => $event->user_id,
                    'user_name' => $event->user ? $event->user->name : 'Guest/System',
                    'user_email' => $event->user?->email,
                    'event_type' => $event->event_type,
                    'severity' => $event->severity,
                    'severity_label' => $this->severityLabel($event->severity),
                    'ip_address' => $event->ip_address,
                    'message' => $event->message,
                    'method' => $context['method'] ?? null,
                    'path' => $context['path'] ?? null,
                    'risk_score' => is_numeric($riskScore) ? (int) $riskScore : null,
                    'device_fingerprint' => $deviceFingerprint,
                    'device_fingerprint_short' => $deviceFingerprint
                        ? substr($deviceFingerprint, 0, 12) . '...'
                        : null,
                    'device_trust_score' => is_numeric($deviceTrustScore) ? (int) $deviceTrustScore : null,
                    'gps' => $gps,
                    'gps_label' => $this->formatGps($gps),
                    'location' => $context['location'] ?? null,
                    'context' => $context,
                    'metadata' => $metadata,
                    'time_diff' => $event->created_at->diffForHumans(),
                    'created_at' => $event->created_at->format('d/m/Y H:i:s'),
                ];
            });

        return response()->json($events);
    }

    protected function severityLabel(?string $severity): string
    {
        return match (strtolower((string) $severity)) {
            'low' => 'RENDAH',
            'medium' => 'SEDANG',
            'high' => 'TINGGI',
            'critical' => 'KRITIS',
            default => strtoupper((string) $severity),
        };
    }

    protected function formatGps(?array $gps): ?string
    {
        if (empty($gps) || !isset($gps['latitude'], $gps['longitude'])) {
            return null;
        }

        $lat = round((float) $gps['latitude'], 6);
        $lng = round((float) $gps['longitude'], 6);
        $accuracy = isset($gps['accuracy']) ? round((float) $gps['accuracy']) . 'm' : null;

        return $accuracy
            ? "{$lat}, {$lng} (±{$accuracy})"
            : "{$lat}, {$lng}";
    }

    /**
     * Revoke access for a user (Force Logout).
     */
    public function revokeAccess(Request $request, $userId)
    {
        // Temukan semua sesi user ini dan hapus
        DB::table('sessions')->where('user_id', $userId)->delete();

        // Tambahkan event keamanan
        SecurityEvent::create([
            'user_id' => $userId,
            'event_type' => 'admin_force_logout',
            'severity' => 'medium',
            'ip_address' => $request->ip(),
            'message' => 'Super Admin forcibly revoked access for this user.',
            'risk_score' => 50,
            'created_at' => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Access successfully revoked.']);
    }
}
