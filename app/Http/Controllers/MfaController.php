<?php

namespace App\Http\Controllers;

use App\Services\MfaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class MfaController extends Controller
{
    protected $google2fa = null;

    public function __construct(
        protected MfaService $mfaService
    ) {
        $this->middleware('auth');
        
        // Check if Google2FA package is installed
        if (class_exists(\PragmaRX\Google2FA\Google2FA::class)) {
            $this->google2fa = new \PragmaRX\Google2FA\Google2FA();
        }
    }

    /**
     * Tampilkan halaman setup MFA
     */
    public function showSetup(): View
    {
        $user = Auth::user();

        $secret = $this->mfaService->getTempSecret($user);

        if (!$secret) {
            $secret = $this->mfaService->generateSecret($user);
        }

        // Generate QR code URL
        $qrUrl = $this->mfaService->getQrCodeUrl($user, $secret);

        return view('mfa.setup', [
            'secret' => $secret,
            'qrUrl' => $qrUrl,
        ]);
    }

    /**
     * Aktifkan MFA untuk user
     */
    public function enable(Request $request): View|RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        $user = Auth::user();
        $secret = $this->mfaService->getTempSecret($user, $request->input('secret_token'));

        if (!$secret) {
            return back()->withErrors(['code' => 'Secret tidak ditemukan. Silakan setup ulang.']);
        }

        // Verifikasi code menggunakan secret temporary (bukan dari database)
        // Saat setup, kita perlu verify dengan secret temporary yang sama dengan yang di QR code
        $code = trim($request->code);
        
        // Pastikan code adalah 6 digit numeric
        if (!preg_match('/^\d{6}$/', $code)) {
            $this->mfaService->storeTempSecret($user, $secret);

            return back()->withErrors(['code' => 'Kode harus berupa 6 digit angka.']);
        }
        
        // Verify dengan secret temporary menggunakan Google2FA langsung
        if (!$this->google2fa) {
            $this->mfaService->storeTempSecret($user, $secret);

            return back()->withErrors(['code' => 'Google2FA package tidak tersedia.']);
        }
        
        // Verify dengan window 2 (lebih toleran untuk perbedaan waktu)
        // Window 2 = ±1 time step (30 detik) = total 90 detik window
        $valid = $this->google2fa->verifyKey($secret, $code, 2);
        
        if (!$valid) {
            \Log::warning('MFA setup verification failed', [
                'user_id' => $user->id,
                'code' => $code,
                'code_length' => strlen($code),
                'secret_length' => strlen($secret),
                'secret_preview' => substr($secret, 0, 4) . '...',
            ]);

            $this->mfaService->storeTempSecret($user, $secret);
            
            return back()->withErrors(['code' => 'Kode verifikasi tidak valid. Pastikan: 1) Waktu di smartphone sudah benar (sinkronisasi waktu), 2) Menggunakan kode terbaru (berubah setiap 30 detik), 3) Secret key sudah benar di aplikasi authenticator.']);
        }

        // Enable MFA (service sudah handle update database)
        if ($this->mfaService->enableMfa($user, $secret, $request->code)) {
            $user->refresh();
            Auth::setUser($user);

            $backupCodes = $this->mfaService->generateBackupCodes($user);

            session()->now('status', 'MFA berhasil diaktifkan! Simpan backup codes Anda dengan aman.');
            session()->now('backup_codes', $backupCodes);

            return view('mfa.backup-codes', [
                'backupCodes' => $backupCodes,
            ]);
        }

        $this->mfaService->storeTempSecret($user, $secret);

        return back()->withErrors(['code' => 'Gagal mengaktifkan MFA. Silakan coba lagi.']);
    }

    /**
     * Tampilkan backup codes
     */
    public function showBackupCodes(): View|RedirectResponse
    {
        $user = Auth::user();
        
        // Cek backup codes dari session (saat baru enable)
        $backupCodes = session('backup_codes', []);

        // Jika tidak ada di session, cek apakah user punya backup codes di cache
        if (empty($backupCodes)) {
            $backupCodesHashed = Cache::get("mfa_backup_codes:{$user->id}", []);
            
            if (empty($backupCodesHashed)) {
                // Jika tidak ada backup codes, redirect ke profile
                return redirect()->route('profile.edit')
                    ->with('error', 'Backup codes tidak ditemukan. Silakan nonaktifkan dan aktifkan ulang 2FA untuk mendapatkan backup codes baru.');
            }
            
            // Jika ada backup codes di cache, tampilkan pesan bahwa backup codes sudah digunakan
            return redirect()->route('profile.edit')
                ->with('info', 'Backup codes hanya ditampilkan sekali saat pertama kali mengaktifkan 2FA. Jika Anda kehilangan backup codes, silakan nonaktifkan dan aktifkan ulang 2FA.');
        }

        return view('mfa.backup-codes', [
            'backupCodes' => $backupCodes,
        ]);
    }

    /**
     * Nonaktifkan MFA
     */
    public function disable(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        
        // Service sudah handle update database
        $this->mfaService->disableMfa($user);
        
        // Refresh user untuk mendapatkan data terbaru
        $user->refresh();

        return redirect()->route('profile.edit')
            ->with('status', 'MFA berhasil dinonaktifkan.');
    }
}

