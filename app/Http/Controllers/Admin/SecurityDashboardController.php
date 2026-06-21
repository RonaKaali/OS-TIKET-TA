<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityEvent;
use App\Models\User;
use App\Services\AccessRevocationService;
use App\Services\GpsLocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecurityDashboardController extends Controller
{
    public function __construct(
        protected GpsLocationService $gpsService,
        protected AccessRevocationService $revocationService
    ) {}
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
            ->map(fn ($event) => $this->formatEvent($event));

        return response()->json($events);
    }

    /**
     * Unduh log keamanan (CSV) per periode: day, week, month.
     */
    public function exportLogs(Request $request): StreamedResponse
    {
        $period = $request->query('period', 'day');
        if (!in_array($period, ['day', 'week', 'month'], true)) {
            abort(422, 'Periode tidak valid.');
        }

        $filename = match ($period) {
            'week' => 'security-log-mingguan-' . now()->format('Y-m-d') . '.csv',
            'month' => 'security-log-bulanan-' . now()->format('Y-m') . '.csv',
            default => 'security-log-harian-' . now()->format('Y-m-d') . '.csv',
        };

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache',
        ];

        return response()->stream(function () use ($period) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'ID',
                'Waktu',
                'Nama User',
                'Email',
                'Tipe Event',
                'Severity',
                'IP Address',
                'Pesan',
                'Method',
                'Path',
                'Risk Score',
                'GPS',
                'Device Fingerprint',
                'Device Trust Score',
            ]);

            SecurityEvent::with('user:id,name,email')
                ->excludeNoise()
                ->forPeriod($period)
                ->orderBy('created_at', 'desc')
                ->chunk(200, function ($events) use ($handle) {
                    foreach ($events as $event) {
                        $row = $this->formatEvent($event);
                        fputcsv($handle, [
                            $row['id'],
                            $row['created_at'],
                            $row['user_name'],
                            $row['user_email'] ?? '',
                            $row['event_type'],
                            $row['severity_label'],
                            $row['ip_address'],
                            $row['message'],
                            $row['method'] ?? '',
                            $row['path'] ?? '',
                            $row['risk_score'] ?? '',
                            $row['gps_label'] ?? '',
                            $row['device_fingerprint'] ?? '',
                            $row['device_trust_score'] ?? '',
                        ]);
                    }
                });

            fclose($handle);
        }, 200, $headers);
    }

    protected function formatEvent(SecurityEvent $event): array
    {
        $context = is_array($event->context) ? $event->context : [];
        $metadata = is_array($event->metadata) ? $event->metadata : [];

        $riskScore = $event->risk_score ?? ($context['risk_score'] ?? null);
        $deviceFingerprint = $event->device_fingerprint ?? ($metadata['device_fingerprint'] ?? null);
        $deviceTrustScore = $metadata['device_trust_score'] ?? null;
        $gps = $context['gps'] ?? null;

        if (empty($gps) && $event->user_id) {
            $gps = $this->gpsService->getStoredForUser($event->user_id);
        }

        return [
            'id' => $event->id,
            'user_id' => $event->user_id,
            'user_name' => $event->user ? $event->user->name : 'Guest/System',
            'user_email' => $event->user?->email,
            'user_is_revoked' => $event->user ? !is_null($event->user->access_revoked_at) : false,
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
        $user = User::findOrFail($userId);

        if ((int) $userId === (int) $request->user()?->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak dapat mencabut akses akun Anda sendiri.',
            ], 422);
        }

        $this->revocationService->revoke($user, $request->user()?->id);

        SecurityEvent::create([
            'user_id' => $user->id,
            'event_type' => 'admin_force_logout',
            'severity' => 'medium',
            'ip_address' => $request->ip(),
            'message' => sprintf(
                'Super Admin mencabut akses paksa untuk %s (%s). User akan logout di semua perangkat pada request berikutnya.',
                $user->name,
                $user->email
            ),
            'context' => [
                'revoked_by' => $request->user()?->id,
                'revoked_at' => now()->toIso8601String(),
            ],
            'risk_score' => 50,
            'created_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Akses {$user->name} telah dicabut. User akan logout otomatis di semua perangkat.",
        ]);
    }

    /**
     * Restore access for a user (Clear Revocation Flag).
     */
    public function restoreAccess(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $this->revocationService->clearRevocationFlag($user);

        SecurityEvent::create([
            'user_id' => $user->id,
            'event_type' => 'admin_restore_access',
            'severity' => 'low',
            'ip_address' => $request->ip(),
            'message' => sprintf(
                'Super Admin memulihkan akses untuk %s (%s). User dapat login kembali.',
                $user->name,
                $user->email
            ),
            'context' => [
                'restored_by' => $request->user()?->id,
                'restored_at' => now()->toIso8601String(),
            ],
            'risk_score' => 0,
            'created_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Akses {$user->name} telah dipulihkan. User dapat login kembali.",
        ]);
    }
}
