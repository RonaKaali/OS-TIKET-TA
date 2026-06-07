<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Services\DeviceFingerprintService;
use App\Services\ContextAwareAccessService;
use App\Services\GpsLocationService;
use App\Services\SecurityEventLogService;

class ZeroTrustVerification
{
    protected DeviceFingerprintService $deviceService;
    protected ContextAwareAccessService $contextService;
    protected SecurityEventLogService $logService;
    protected GpsLocationService $gpsService;

    public function __construct(
        DeviceFingerprintService $deviceService,
        ContextAwareAccessService $contextService,
        SecurityEventLogService $logService,
        GpsLocationService $gpsService
    ) {
        $this->deviceService = $deviceService;
        $this->contextService = $contextService;
        $this->logService = $logService;
        $this->gpsService = $gpsService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip untuk route tertentu (public routes, API tanpa auth)
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        // Cek apakah Zero Trust enabled
        if (!config('zero_trust.enabled', false)) {
            return $next($request);
        }

        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        try {
            $riskHigh = (int) config('zero_trust.risk_score_threshold_high', 70);
            $riskCritical = (int) config('zero_trust.risk_score_threshold_critical', 85);

            // IP klien yang sebenarnya (memperhitungkan proxy / ngrok)
            $clientIp = $this->getClientIp($request);

            // 1. Device Fingerprinting
            if (config('zero_trust.device_fingerprinting', true)) {
                $fingerprint = $this->deviceService->generateFingerprint($request);
                $trustScore = $this->deviceService->calculateTrustScore($user, $fingerprint, $request);

                // Simpan fingerprint di request untuk digunakan di controller
                $request->merge(['device_fingerprint' => $fingerprint]);
                $request->merge(['device_trust_score' => $trustScore]);

                // Jika device belum terdaftar, register
                if (!$this->deviceService->isDeviceRegistered($user, $fingerprint)) {
                    $this->deviceService->registerDevice($user, $fingerprint, [
                        'user_agent' => $request->userAgent(),
                        'ip' => $clientIp,
                    ]);

                    $this->logService->logDeviceEvent($user->id, 'registered', [
                        'fingerprint' => $fingerprint,
                        'ip' => $clientIp,
                        'user_agent' => $request->userAgent(),
                    ]);
                } else {
                    // Update last seen
                    $this->deviceService->updateDeviceLastSeen($user, $fingerprint);
                }

                // Cek apakah device memerlukan verifikasi tambahan
                if ($this->deviceService->requiresVerification($user, $fingerprint, $trustScore)) {
                    // Redirect ke device verification jika diperlukan
                    if (!$request->session()->get('device_verified_' . $fingerprint)) {
                        if (\Route::has('device.verify')) {
                            return redirect()->route('device.verify')
                                ->with('fingerprint', $fingerprint)
                                ->with('trust_score', $trustScore);
                        }

                        // Jika route belum tersedia, jangan block request (fail open),
                        // tapi tetap log anomaly agar dapat ditindaklanjuti.
                        $this->logService->logAnomaly($user->id, 'device_verification_route_missing', [
                            'fingerprint' => $fingerprint,
                            'trust_score' => $trustScore,
                            'path' => $request->path(),
                        ]);
                    }
                }
            }

            // 2. Context-Aware Access Control
            if (config('zero_trust.context_aware', true)) {
                $context = $this->contextService->analyzeContext($request, $user);
                $riskScore = $this->contextService->calculateRiskScore($user, $context);

                // Simpan context di request
                $request->merge(['access_context' => $context]);
                $request->merge(['risk_score' => $riskScore]);

                // Jika risk score tinggi, require additional verification
                if ($riskScore >= $riskHigh) {
                    $this->logService->logAnomaly($user->id, 'high_risk_access', $context);

                    // Step-up MFA untuk akses berisiko tinggi (tanpa memutus session login normal).
                    // Jika sudah pernah step-up dalam 10 menit terakhir, lanjutkan.
                    $stepUpVerified = $request->session()->get('mfa_verified_high_risk');
                    if (!$stepUpVerified) {
                        $request->session()->put('mfa_step_up_action', 'high_risk');
                        $request->session()->put('url.intended', $request->fullUrl());
                        return redirect()->route('mfa.verify');
                    }

                    $verifiedAt = \Carbon\Carbon::parse($stepUpVerified);
                    if (now()->diffInMinutes($verifiedAt) > 10) {
                        $request->session()->forget('mfa_verified_high_risk');
                        $request->session()->put('mfa_step_up_action', 'high_risk');
                        $request->session()->put('url.intended', $request->fullUrl());
                        return redirect()->route('mfa.verify');
                    }

                    // Untuk risiko sangat tinggi, blok akses.
                    if ($riskScore >= $riskCritical) {
                        $this->logService->logAuthorization($user->id, 'high_risk_access_block', false, $request->path());
                        if ($request->expectsJson()) {
                            return response()->json([
                                'error' => 'Access denied due to high risk score',
                                'risk_score' => $riskScore,
                            ], 403);
                        }
                        abort(403, 'Akses ditolak karena skor risiko tinggi.');
                    }
                }

                // Cek context-aware access untuk permission tertentu
                $requiredPermission = $this->getRequiredPermission($request);
                if ($requiredPermission) {
                    if (!$this->contextService->evaluateAccess($user, $context, $requiredPermission)) {
                        $this->logService->logAuthorization($user->id, $requiredPermission, false, $request->path());

                        if ($request->expectsJson()) {
                            return response()->json([
                                'error' => 'Access denied based on context',
                            ], 403);
                        }

                        abort(403, 'Akses ditolak berdasarkan konteks keamanan.');
                    }
                }
            }

            // 3. Session Validation
            $this->validateSession($request, $user);

            // 4. Log access (untuk monitoring)
            if ($this->shouldSkipLogging($request)) {
                return $next($request);
            }

            $accessContext = $request->get('access_context', []);
            $riskScore = $request->get('risk_score');
            $deviceFingerprint = $request->get('device_fingerprint');
            $deviceTrustScore = $request->get('device_trust_score');
            $gps = $this->gpsService->resolve(
                $request,
                $user->id
            ) ?? ($accessContext['gps'] ?? null);

            $this->logService->logEvent([
                'user_id' => $user->id,
                'event_type' => 'access',
                'severity' => 'low',
                'message' => "Access to {$request->path()}",
                'risk_score' => is_numeric($riskScore) ? (int) $riskScore : null,
                'device_fingerprint' => $deviceFingerprint,
                'context' => [
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'ip' => $clientIp,
                    'location' => $accessContext['location'] ?? null,
                    'gps' => $gps,
                    'risk_score' => $riskScore,
                ],
                'metadata' => [
                    'device_fingerprint' => $deviceFingerprint,
                    'device_trust_score' => $deviceTrustScore,
                ],
            ]);

        } catch (\Exception $e) {
            // Log error tapi jangan block request (fail open untuk availability)
            \Log::error('Zero Trust verification error: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'path' => $request->path(),
            ]);
        }

        return $next($request);
    }

    /**
     * Cek apakah middleware harus di-skip
     */
    protected function shouldSkip(Request $request): bool
    {
        $skipPaths = [
            'login',
            'register',
            'password',
            'email/verify',
            'chatbot/message',
            'mfa/verify', // MFA verification page
            'mfa/verify-backup', // MFA verification (backup code)
            'mfa/setup',
            'mfa/enable',
            'mfa/backup-codes',
            'mfa/disable',
            'device/verify', // Device verification page
            'zero-trust/gps', // Endpoint penyimpanan GPS (hindari log berulang tanpa koordinat)
            'admin/api/security-events/latest', // Polling dashboard admin
            'session/check', // Heartbeat session
            'up', // Health check
        ];

        foreach ($skipPaths as $path) {
            if ($request->is($path) || $request->is($path . '/*')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Route yang tetap diverifikasi Zero Trust tetapi tidak dicatat ke security feed.
     */
    protected function shouldSkipLogging(Request $request): bool
    {
        $skipLoggingPaths = [
            'zero-trust/gps',
            'admin/api/security-events/latest',
            'session/check',
        ];

        foreach ($skipLoggingPaths as $path) {
            if ($request->is($path) || $request->is($path . '/*')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validasi session
     */
    protected function validateSession(Request $request, $user): void
    {
        $lastValidation = $request->session()->get('last_zero_trust_validation');
        $validationInterval = config('zero_trust.session_validation_interval', 30); // seconds

        if (!$lastValidation || now()->diffInSeconds($lastValidation) >= $validationInterval) {
            // Re-validate session
            $request->session()->put('last_zero_trust_validation', now());

            // Cek apakah user masih valid
            if (!$user || !$user->exists) {
                Auth::logout();
                $request->session()->invalidate();
            }
        }
    }

    /**
     * Dapatkan required permission dari route
     */
    protected function getRequiredPermission(Request $request): ?string
    {
        // Extract permission dari route middleware atau route name
        $route = $request->route();
        
        if ($route) {
            $middleware = $route->middleware();
            
            foreach ($middleware as $mw) {
                if (str_starts_with($mw, 'permission:')) {
                    return str_replace('permission:', '', $mw);
                }
            }
        }

        return null;
    }

    /**
     * Ambil IP klien sebenarnya (X-Forwarded-For jika ada, fallback ke request->ip()).
     */
    protected function getClientIp(Request $request): string
    {
        $forwarded = $request->header('X-Forwarded-For');
        if ($forwarded) {
            $parts = explode(',', $forwarded);
            $ip = trim($parts[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        return $request->ip();
    }
}

