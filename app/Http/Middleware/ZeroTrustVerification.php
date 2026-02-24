<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Services\DeviceFingerprintService;
use App\Services\ContextAwareAccessService;
use App\Services\SecurityEventLogService;

class ZeroTrustVerification
{
    protected DeviceFingerprintService $deviceService;
    protected ContextAwareAccessService $contextService;
    protected SecurityEventLogService $logService;

    public function __construct(
        DeviceFingerprintService $deviceService,
        ContextAwareAccessService $contextService,
        SecurityEventLogService $logService
    ) {
        $this->deviceService = $deviceService;
        $this->contextService = $contextService;
        $this->logService = $logService;
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
                        'ip' => $request->ip(),
                    ]);

                    $this->logService->logDeviceEvent($user->id, 'registered', [
                        'fingerprint' => $fingerprint,
                        'ip' => $request->ip(),
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
                        return redirect()->route('device.verify')
                            ->with('fingerprint', $fingerprint)
                            ->with('trust_score', $trustScore);
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
                if ($riskScore > 70) {
                    $this->logService->logAnomaly($user->id, 'high_risk_access', $context);

                    // Untuk akses dengan risk tinggi, bisa require MFA atau block
                    if ($riskScore > 85) {
                        return response()->json([
                            'error' => 'Access denied due to high risk score',
                            'risk_score' => $riskScore,
                        ], 403);
                    }
                }

                // Cek context-aware access untuk permission tertentu
                $requiredPermission = $this->getRequiredPermission($request);
                if ($requiredPermission) {
                    if (!$this->contextService->evaluateAccess($user, $context, $requiredPermission)) {
                        $this->logService->logAuthorization($user->id, $requiredPermission, false, $request->path());

                        return response()->json([
                            'error' => 'Access denied based on context',
                        ], 403);
                    }
                }
            }

            // 3. Session Validation
            $this->validateSession($request, $user);

            // 4. Log access (untuk monitoring)
            $this->logService->logEvent([
                'user_id' => $user->id,
                'event_type' => 'access',
                'severity' => 'low',
                'message' => "Access to {$request->path()}",
                'context' => [
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'ip' => $request->ip(),
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
}

