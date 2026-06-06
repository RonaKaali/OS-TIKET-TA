<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class MfaService
{
    protected $google2fa = null;

    public function __construct()
    {
        // Check if Google2FA package is installed
        if (class_exists(\PragmaRX\Google2FA\Google2FA::class)) {
            $this->google2fa = new \PragmaRX\Google2FA\Google2FA();
        }
    }

    /**
     * Check if MFA is available
     */
    protected function isAvailable(): bool
    {
        return $this->google2fa !== null;
    }

    /**
     * Generate secret untuk TOTP
     */
    public function generateSecret(User $user): string
    {
        if (!$this->isAvailable()) {
            throw new \Exception('Google2FA package is not installed. Run: composer require pragmarx/google2fa');
        }

        $secret = $this->google2fa->generateSecretKey();
        $this->storeTempSecret($user, $secret);

        return $secret;
    }

    /**
     * Simpan secret sementara untuk proses setup MFA.
     * Session + form token dipakai agar tetap jalan di serverless (Vercel) yang cache-nya tidak persisten.
     */
    public function storeTempSecret(User $user, string $secret): void
    {
        Cache::put("mfa_secret_temp:{$user->id}", $secret, now()->addMinutes(10));
        session()->put("mfa_secret_temp:{$user->id}", $secret);
    }

    /**
     * Ambil secret sementara dari form token, session, atau cache.
     */
    public function getTempSecret(User $user, ?string $encryptedSecret = null): ?string
    {
        if ($encryptedSecret) {
            try {
                return decrypt($encryptedSecret);
            } catch (\Exception $e) {
                \Log::warning('Failed to decrypt MFA temp secret from form', [
                    'user_id' => $user->id,
                ]);
            }
        }

        $secret = session("mfa_secret_temp:{$user->id}");
        if ($secret) {
            return $secret;
        }

        return Cache::get("mfa_secret_temp:{$user->id}");
    }

    /**
     * Hapus secret sementara setelah setup selesai atau dibatalkan.
     */
    public function forgetTempSecret(User $user): void
    {
        Cache::forget("mfa_secret_temp:{$user->id}");
        session()->forget("mfa_secret_temp:{$user->id}");
    }

    /**
     * Generate QR code URL untuk TOTP setup
     */
    public function getQrCodeUrl(User $user, string $secret): string
    {
        if (!$this->isAvailable()) {
            throw new \Exception('Google2FA package is not installed. Run: composer require pragmarx/google2fa');
        }

        $companyName = config('app.name', 'OS-Tiket');
        $companyEmail = $user->email;
        
        return $this->google2fa->getQRCodeUrl(
            $companyName,
            $companyEmail,
            $secret
        );
    }

    /**
     * Verifikasi TOTP code
     */
    public function verifyTotp(User $user, string $code): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $secret = $this->getUserSecret($user);
        
        if (!$secret) {
            return false;
        }

        // Verifikasi dengan window 2 (allow 60 detik sebelum/sesudah untuk toleransi waktu)
        // Window 2 = ±1 time step (30 detik) = total 90 detik window
        $valid = $this->google2fa->verifyKey($secret, $code, 2);
        
        if ($valid) {
            // Log successful verification
            $logService = app(SecurityEventLogService::class);
            $logService->logAuthentication('mfa_totp', $user->id, true, 'TOTP verification successful');
        } else {
            // Log failed verification
            $logService = app(SecurityEventLogService::class);
            $logService->logAuthentication('mfa_totp', $user->id, false, 'TOTP verification failed');

            // Log failed verification untuk debugging
            \Log::warning('MFA verification failed', [
                'user_id' => $user->id,
                'code_length' => strlen($code),
                'code_format' => is_numeric($code) ? 'numeric' : 'non-numeric',
                'secret_exists' => !empty($secret),
            ]);
        }
        
        return $valid;
    }

    /**
     * Aktifkan MFA untuk user
     */
    public function enableMfa(User $user, string $secret, string $verificationCode): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        // Verifikasi code terlebih dahulu dengan window 2 (lebih toleran)
        if (!$this->google2fa->verifyKey($secret, $verificationCode, 2)) {
            \Log::warning('MFA enable verification failed', [
                'secret_length' => strlen($secret),
                'code_length' => strlen($verificationCode),
            ]);
            return false;
        }

        // Simpan secret ke cache
        Cache::put("mfa_secret:{$user->id}", $secret, now()->addYears(10));
        
        // Simpan secret ke database jika model tersedia (encrypted)
        if (method_exists($user, 'update')) {
            try {
                // Encrypt secret sebelum disimpan (gunakan Laravel encryption)
                $encryptedSecret = encrypt($secret);
                $user->mfa_secret = $encryptedSecret;
                $user->mfa_enabled = true;
                $user->mfa_enabled_at = now();
                $user->save();
            } catch (\Exception $e) {
                \Log::error('Failed to save MFA secret to database: ' . $e->getMessage());
                // Continue dengan cache saja jika database save gagal
            }
        }
        
        $this->forgetTempSecret($user);
        
        // Generate backup codes
        $backupCodes = $this->generateBackupCodes($user);
        
        return true;
    }

    /**
     * Nonaktifkan MFA untuk user
     */
    public function disableMfa(User $user): void
    {
        $this->forgetTempSecret($user);
        Cache::forget("mfa_secret:{$user->id}");
        Cache::forget("mfa_backup_codes:{$user->id}");
        
        // Update database jika model tersedia
        if (method_exists($user, 'update')) {
            try {
                $user->mfa_secret = null;
                $user->mfa_enabled = false;
                $user->mfa_enabled_at = null;
                $user->save();
            } catch (\Exception $e) {
                \Log::error('Failed to update MFA in database: ' . $e->getMessage());
            }
        }
    }

    /**
     * Cek apakah MFA aktif untuk user
     */
    public function isMfaEnabled(User $user): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        // Cek cache dulu (lebih cepat)
        if (Cache::has("mfa_secret:{$user->id}")) {
            return true;
        }

        // Jika tidak ada di cache, cek database
        if (method_exists($user, 'mfa_enabled')) {
            // Refresh user untuk mendapatkan data terbaru
            $user->refresh();
            
            if ($user->mfa_enabled && $user->mfa_secret) {
                // Jika MFA enabled di database, load secret ke cache
                try {
                    $secret = decrypt($user->mfa_secret);
                    Cache::put("mfa_secret:{$user->id}", $secret, now()->addYears(10));
                    return true;
                } catch (\Exception $e) {
                    \Log::error('Failed to decrypt MFA secret from database: ' . $e->getMessage());
                }
            }
        }

        return false;
    }

    /**
     * Dapatkan secret user
     */
    protected function getUserSecret(User $user): ?string
    {
        // Cek cache dulu
        $secret = Cache::get("mfa_secret:{$user->id}");
        
        if ($secret) {
            return $secret;
        }
        
        // Jika tidak ada di cache, cek database
        if (method_exists($user, 'mfa_secret') && $user->mfa_secret) {
            try {
                // Decrypt secret dari database
                $secret = decrypt($user->mfa_secret);
                // Simpan ke cache untuk akses cepat
                Cache::put("mfa_secret:{$user->id}", $secret, now()->addYears(10));
                return $secret;
            } catch (\Exception $e) {
                \Log::error('Failed to decrypt MFA secret from database: ' . $e->getMessage());
            }
        }
        
        return null;
    }

    /**
     * Generate backup codes
     */
    public function generateBackupCodes(User $user, int $count = 10): array
    {
        $codes = [];
        
        for ($i = 0; $i < $count; $i++) {
            $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            $codes[] = $code;
        }
        
        // Hash codes sebelum disimpan
        $hashedCodes = array_map(fn($code) => Hash::make($code), $codes);
        
        Cache::put("mfa_backup_codes:{$user->id}", $hashedCodes, now()->addYears(10));
        
        return $codes;
    }

    /**
     * Verifikasi backup code
     */
    public function verifyBackupCode(User $user, string $code): bool
    {
        $backupCodes = Cache::get("mfa_backup_codes:{$user->id}", []);
        
        foreach ($backupCodes as $index => $hashedCode) {
            if (Hash::check($code, $hashedCode)) {
                // Hapus code yang sudah digunakan
                unset($backupCodes[$index]);
                Cache::put("mfa_backup_codes:{$user->id}", array_values($backupCodes), now()->addYears(10));
                
                return true;
            }
        }
        
        return false;
    }

    /**
     * Require MFA untuk akses tertentu
     */
    public function requireMfa(User $user, string $action = 'access'): bool
    {
        // Cek apakah MFA sudah diverifikasi dalam session
        $mfaVerified = session()->get("mfa_verified_{$action}");
        
        if ($mfaVerified && now()->diffInMinutes($mfaVerified) < 30) {
            return true; // MFA sudah diverifikasi dalam 30 menit terakhir
        }
        
        return false; // Perlu verifikasi MFA
    }

    /**
     * Set MFA sebagai verified dalam session
     */
    public function setMfaVerified(User $user, string $action = 'access'): void
    {
        session()->put("mfa_verified_{$action}", now());
    }
}

