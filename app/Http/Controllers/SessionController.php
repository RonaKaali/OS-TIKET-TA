<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SessionController extends Controller
{
    /**
     * Check session status for auto logout
     */
    public function check(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'authenticated' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        $lastActivity = $request->session()->get('last_activity');
        $inactiveTimeout = 3; // 3 menit dalam menit
        $isExpired = false;

        if ($lastActivity) {
            try {
                $lastActivityTime = Carbon::parse($lastActivity);
                $minutesSinceLastActivity = now()->diffInMinutes($lastActivityTime);

                if ($minutesSinceLastActivity >= $inactiveTimeout) {
                    $isExpired = true;
                }
            } catch (\Exception $e) {
                // Jika parsing gagal, anggap expired
                $isExpired = true;
            }
        }

        if ($isExpired) {
            $user = Auth::user();

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

            return response()->json([
                'authenticated' => false,
                'expired' => true,
                'message' => 'Session expired',
                'redirect' => route('login')
            ], 401);
        }

        return response()->json([
            'authenticated' => true,
            'expired' => false,
            'last_activity' => $lastActivity,
            'minutes_remaining' => $inactiveTimeout - ($lastActivity ? now()->diffInMinutes(Carbon::parse($lastActivity)) : 0)
        ]);
    }
}

