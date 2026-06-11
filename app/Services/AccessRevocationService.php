<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccessRevocationService
{
    public const SESSION_AUTH_KEY = 'session_authenticated_at';

    /**
     * Tandai waktu login sukses di session (setelah MFA jika ada).
     */
    public function stampSession(Request $request): void
    {
        $request->session()->put(self::SESSION_AUTH_KEY, now()->toIso8601String());
    }

    /**
     * Cabut semua akses user: flag DB + hapus token/sesi server.
     */
    public function revoke(User $user, ?int $revokedByUserId = null): void
    {
        DB::table('pengguna')
            ->where('id', $user->id)
            ->update(['access_revoked_at' => now()]);

        try {
            $user->tokens()->delete();
        } catch (\Throwable) {
            // personal_access_tokens mungkin belum ada
        }

        try {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        } catch (\Throwable) {
            // driver cookie — tabel sessions mungkin kosong/tidak ada
        }
    }

    /**
     * Bersihkan flag pencabutan setelah login sukses baru.
     */
    public function clearRevocationFlag(User $user): void
    {
        if (!$user->access_revoked_at) {
            return;
        }

        DB::table('pengguna')
            ->where('id', $user->id)
            ->update(['access_revoked_at' => null]);

        $user->access_revoked_at = null;
    }

    /**
     * Apakah session saat ini sudah dicabut oleh admin?
     */
    public function isSessionRevoked(User $user, Request $request): bool
    {
        $revokedAt = $this->getRevokedAt($user);

        if (!$revokedAt) {
            return false;
        }

        $sessionAuthAt = $request->session()->get(self::SESSION_AUTH_KEY);

        // Session lama (sebelum fitur ini) atau belum selesai MFA → anggap dicabut
        if (!$sessionAuthAt) {
            return true;
        }

        try {
            return Carbon::parse($sessionAuthAt)->lt($revokedAt);
        } catch (\Throwable) {
            return true;
        }
    }

    public function getRevokedAt(User $user): ?Carbon
    {
        try {
            $raw = DB::table('pengguna')
                ->where('id', $user->id)
                ->value('access_revoked_at');

            return $raw ? Carbon::parse($raw) : null;
        } catch (\Throwable) {
            return $user->access_revoked_at;
        }
    }

    /**
     * Logout paksa & invalidasi session.
     */
    public function forceLogout(Request $request): void
    {
        $user = Auth::user();

        if ($user) {
            try {
                $user->tokens()->delete();
            } catch (\Throwable) {
            }
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
