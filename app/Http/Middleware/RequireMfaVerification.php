<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Services\MfaService;

class RequireMfaVerification
{
    public function __construct(
        protected MfaService $mfaService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip jika user belum login
        if (!Auth::check()) {
            return $next($request);
        }

        if (!config('zero_trust.mfa_enabled', true)) {
            return $next($request);
        }

        if ($this->isAlwaysAllowed($request)) {
            return $next($request);
        }

        $user = Auth::user();

        // Refresh user untuk mendapatkan data terbaru
        $user->refresh();

        // Cek apakah user punya MFA enabled
        $mfaEnabled = $user->mfa_enabled ?? false;
        
        if (!$mfaEnabled) {
            $mfaEnabled = $this->mfaService->isMfaEnabled($user);
        }

        if (!$mfaEnabled) {
            if ($this->isMfaSetupRoute($request)) {
                return $next($request);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'MFA wajib diaktifkan sebelum mengakses fitur ini.',
                    'mfa_setup_required' => true,
                ], 403);
            }

            return redirect()
                ->route('mfa.setup')
                ->with('status', 'Zero Trust aktif: aktifkan MFA terlebih dahulu sebelum mengakses sistem.');
        }

        // Cek apakah MFA sudah diverifikasi untuk login
        $mfaVerified = session()->get('mfa_verified_login');
        
        if (!$mfaVerified) {
            // Jika belum verified, redirect ke halaman MFA verification
            // Kecuali jika sedang di halaman MFA verification itu sendiri
            // atau halaman verifikasi menggunakan backup code
            if (
                !$request->is('mfa/verify') &&
                !$request->is('mfa/verify/*') &&
                !$request->is('mfa/verify-backup') &&
                !$request->is('mfa/verify-backup/*')
            ) {
                \Log::info('User dengan MFA enabled mencoba akses tanpa verifikasi, redirecting to MFA verify', [
                    'user_id' => $user->id,
                    'path' => $request->path(),
                ]);
                return redirect()->route('mfa.verify');
            }
        } else {
            // Cek apakah verifikasi masih valid (30 menit)
            $verifiedAt = \Carbon\Carbon::parse($mfaVerified);
            if (now()->diffInMinutes($verifiedAt) > 30) {
                // Verifikasi sudah expired, perlu verify lagi
                session()->forget('mfa_verified_login');
                if (
                    !$request->is('mfa/verify') &&
                    !$request->is('mfa/verify/*') &&
                    !$request->is('mfa/verify-backup') &&
                    !$request->is('mfa/verify-backup/*')
                ) {
                    \Log::info('MFA verification expired, redirecting to MFA verify', [
                        'user_id' => $user->id,
                        'path' => $request->path(),
                    ]);
                    return redirect()->route('mfa.verify');
                }
            }
        }

        return $next($request);
    }

    protected function isAlwaysAllowed(Request $request): bool
    {
        $paths = [
            'mfa/verify',
            'mfa/verify-backup',
            'logout',
            'zero-trust/gps',
            'session/check',
            'device/verify',
            'up',
        ];

        foreach ($paths as $path) {
            if ($request->is($path) || $request->is($path . '/*')) {
                return true;
            }
        }

        return false;
    }

    protected function isMfaSetupRoute(Request $request): bool
    {
        return $request->is('mfa/setup')
            || $request->is('mfa/setup/*')
            || $request->is('mfa/enable')
            || $request->is('mfa/enable/*');
    }
}
