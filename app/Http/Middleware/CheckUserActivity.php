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
            $lastActivity = session('last_activity');
            $inactiveTimeout = 5; // 5 menit dalam menit

            // Jika ada last_activity dan sudah lebih dari 5 menit, logout
            if ($lastActivity) {
                $lastActivityTime = \Carbon\Carbon::parse($lastActivity);
                $minutesSinceLastActivity = now()->diffInMinutes($lastActivityTime);
                
                if ($minutesSinceLastActivity >= $inactiveTimeout) {
                    // Revoke semua token user
                    $user->tokens()->delete();
                    
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->with('status', 'Session Anda telah berakhir karena tidak ada aktivitas selama 5 menit. Silakan login kembali.');
                }
            }

            // Update last activity time untuk setiap request
            $request->session()->put('last_activity', now()->toDateTimeString());
        }

        return $next($request);
    }
}
