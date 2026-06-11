<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Support\MfaSchema;

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
     * Aktifkan MFA untuk user — database wajib berhasil.
     * Mengembalikan null jika sukses, atau pesan error jika gagal.
     */
    public function enableMfa(User $user, string $secret, ?string $verificationCode = null, bool $codeAlreadyVerified = false): ?string
    {
        if (!$this->isAvailable()) {
            return 'Google2FA tidak tersedia di server.';
        }

        if (!$codeAlreadyVerified) {
            if (!$verificationCode || !$this->google2fa->verifyKey($secret, trim($verificationCode), 2)) {
                return 'Kode verifikasi tidak valid.';
            }
        }

        if (!MfaSchema::columnsExist()) {
            try {
                MfaSchema::ensureColumns();
            } catch (\Throwable $e) {
                \Log::error('MFA ensureColumns failed', ['message' => $e->getMessage()]);
                return 'Gagal menyiapkan kolom MFA di database.';
            }
        }

        if (!MfaSchema::columnsExist()) {
            return 'Kolom MFA belum tersedia. Buka /deploy-db sekali, lalu coba lagi.';
        }

        try {
            $encryptedSecret = encrypt($secret);

            if (!MfaSchema::persistMfaForUser($user->id, $encryptedSecret)) {
                return 'Gagal menyimpan pengaturan MFA ke database.';
            }

            $user->refresh();

            if (!MfaSchema::userHasMfa($user->id)) {
                \Log::error('MFA enable: data tidak tersimpan setelah update', [
                    'user_id' => $user->id,
                ]);

                return 'MFA gagal tersimpan. Coba buka /deploy-db lalu aktifkan ulang.';
            }
        } catch (\Throwable $e) {
            \Log::error('Failed to save MFA to database', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);

            return 'Gagal menyimpan MFA: ' . $e->getMessage();
        }

        $this->forgetTempSecret($user);

        return null;
    }

    /**
     * Cek apakah kolom MFA sudah ada di tabel pengguna.
     */
    protected function mfaColumnsExist(): bool
    {
        return MfaSchema::columnsExist();
    }

    /**
     * Cek MFA tersimpan di database (toleran tipe boolean PostgreSQL/MySQL).
     */
    protected function userHasPersistedMfa(User $user): bool
    {
        $attrs = $user->getAttributes();

        return filter_var($attrs['mfa_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN)
            && !empty($attrs['mfa_secret']);
    }

    /**
     * Nonaktifkan MFA untuk user
     */
    public function disableMfa(User $user): void
    {
        $this->forgetTempSecret($user);
        Cache::forget("mfa_secret:{$user->id}");
        Cache::forget("mfa_backup_codes:{$user->id}");

        MfaSchema::clearMfaForUser($user->id);
        $user->refresh();
    }

    /**
     * Cek apakah MFA aktif — database sebagai sumber kebenaran utama.
     */
    public function isMfaEnabled(User $user): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $user->refresh();

        return MfaSchema::userHasMfa($user->id);
    }

    /**
     * Dapatkan secret user dari database.
     */
    protected function getUserSecret(User $user): ?string
    {
        $user->refresh();

        if (empty($user->mfa_secret)) {
            return null;
        }

        try {
            return decrypt($user->mfa_secret);
        } catch (\Exception $e) {
            \Log::error('Failed to decrypt MFA secret from database', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Generate backup codes dan simpan ke database.
     * Menggunakan random_bytes() untuk kriptografi yang aman.
     */
    public function generateBackupCodes(User $user, int $count = 10): array
    {
        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            // random_bytes() adalah CSPRNG — aman untuk backup codes
            $code = strtoupper(bin2hex(random_bytes(4))); // 8 karakter hex
            $codes[] = $code;
        }

        $hashedCodes = array_map(fn ($code) => Hash::make($code), $codes);

        MfaSchema::saveBackupCodes($user->id, $hashedCodes);
        Cache::put("mfa_backup_codes:{$user->id}", $hashedCodes, now()->addYears(10));

        return $codes;
    }

    /**
     * Verifikasi backup code dari database.
     */
    public function verifyBackupCode(User $user, string $code): bool
    {
        $backupCodes = MfaSchema::getBackupCodes($user->id);

        if (empty($backupCodes)) {
            $backupCodes = Cache::get("mfa_backup_codes:{$user->id}", []);
        }

        foreach ($backupCodes as $index => $hashedCode) {
            if (Hash::check($code, $hashedCode)) {
                unset($backupCodes[$index]);
                MfaSchema::saveBackupCodes($user->id, array_values($backupCodes));

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

