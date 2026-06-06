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

        // Portal pelaporan: jangan paksa MFA ulang agar alur buat tiket tidak terputus
        if ($request->is('portal/ticket') || $request->is('portal/ticket/*')) {
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

        // Jika user tidak punya MFA enabled, lanjutkan
        if (!$mfaEnabled) {
            return $next($request);
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
}

