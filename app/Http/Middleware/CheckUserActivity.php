<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $lastActivity = $request->session()->get('last_activity');
            $inactiveTimeout = config('auth.session_timeout', 3); // Mengambil dari config, default 3 menit

            // Jika ada last_activity, cek apakah sudah melewati timeout
            if ($lastActivity) {
                try {
                    $lastActivityTime = \Carbon\Carbon::parse($lastActivity);
                    $minutesSinceLastActivity = now()->diffInMinutes($lastActivityTime);

                    // Jika sudah lebih dari 3 menit tanpa aktivitas, logout
                    if ($minutesSinceLastActivity >= $inactiveTimeout) {
                        // Revoke semua token user
                        if (method_exists($user, 'tokens')) {
                            try {
                                $user->tokens()->delete();
                            } catch (\Exception $e) {
                                // Abaikan error jika tabel personal_access_tokens tidak ada
                            }
                        }

                        Auth::logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();

                        return redirect()->route('login')
                            ->with('status', "Session Anda telah berakhir karena tidak ada aktivitas selama {$inactiveTimeout} menit. Silakan login kembali.");
                    }
                } catch (\Exception $e) {
                    // Jika parsing gagal, reset last_activity
                    $lastActivity = null;
                }
            }

            // Update last activity time untuk setiap request
            $request->session()->put('last_activity', now()->toDateTimeString());
        }

        $response = $next($request);
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        return $response;
    }
}
