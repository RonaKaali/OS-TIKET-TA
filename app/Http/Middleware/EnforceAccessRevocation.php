<?php

namespace App\Http\Middleware;

use App\Services\AccessRevocationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceAccessRevocation
{
    public function __construct(
        protected AccessRevocationService $revocationService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        // Skip cek revokasi di halaman MFA verify — stampSession belum dipanggil
        // saat user baru login dan diarahkan ke MFA, session belum di-stamp
        if ($request->is('mfa/verify') || $request->is('mfa/verify-backup') || $request->is('mfa/verify/*')) {
            return $next($request);
        }

        $user = Auth::user();
        $user->refresh();

        if (!$this->revocationService->isSessionRevoked($user, $request)) {
            return $next($request);
        }

        $this->revocationService->forceLogout($request);

        $message = 'Akses Anda telah dicabut oleh administrator. Silakan login kembali.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'revoked' => true,
            ], 401);
        }

        return redirect()
            ->route('login')
            ->with('status', $message);
    }
}
