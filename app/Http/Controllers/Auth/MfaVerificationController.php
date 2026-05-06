<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\MfaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class MfaVerificationController extends Controller
{
    public function __construct(
        protected MfaService $mfaService
    ) {}

    /**
     * Tampilkan form verifikasi MFA (kode dari aplikasi authenticator)
     */
    public function create(): View|RedirectResponse
    {
        // Pastikan user sudah login tapi belum verifikasi MFA
        if (!Auth::check()) {
            \Log::warning('MFA verify accessed without authentication');
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Refresh user untuk mendapatkan data terbaru dari database
        $user->refresh();

        // Cek MFA enabled dari database langsung
        $mfaEnabled = $user->mfa_enabled ?? false;
        
        // Juga cek via service
        if (!$mfaEnabled) {
            $mfaEnabled = $this->mfaService->isMfaEnabled($user);
        }

        // Log untuk debugging
        \Log::info('MFA verify page accessed', [
            'user_id' => $user->id,
            'email' => $user->email,
            'mfa_enabled_db' => $user->mfa_enabled ?? false,
            'mfa_enabled_service' => $this->mfaService->isMfaEnabled($user),
            'mfa_secret_exists' => !empty($user->mfa_secret),
        ]);

        // Jika user tidak punya MFA enabled, redirect ke dashboard
        if (!$mfaEnabled) {
            \Log::info('User tidak punya MFA enabled, redirecting to dashboard', [
                'user_id' => $user->id,
            ]);
            // Complete login karena tidak ada MFA
            $this->completeLogin(request(), $user);
            
            // Redirect sesuai permission
            if ($user->can('admin.panel')) {
                return redirect()->intended(route('dashboard', absolute: false));
            } else {
                return redirect()->intended(route('welcome', absolute: false))->with('status', 'Selamat datang!');
            }
        }

        return view('auth.mfa-verify');
    }

    /**
     * Handle verifikasi MFA dengan kode dari aplikasi authenticator (TOTP 6 digit)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Verifikasi TOTP code (6 digit dari aplikasi authenticator)
        if (!$this->mfaService->verifyTotp($user, $request->code)) {
            $logService = app(\App\Services\SecurityEventLogService::class);
            $logService->logAuthentication('mfa_totp', $user->id, false, 'MFA verification failed');

            throw ValidationException::withMessages([
                'code' => ['Kode verifikasi tidak valid. Pastikan Anda menggunakan kode terbaru dari aplikasi authenticator Anda.'],
            ]);
        }

        // Set MFA sebagai verified dalam session
        $this->mfaService->setMfaVerified($user, 'login');

        // Jika ini step-up MFA (mis. high risk access), tandai juga action tersebut.
        $stepUpAction = $request->session()->get('mfa_step_up_action');
        if ($stepUpAction === 'high_risk') {
            $this->mfaService->setMfaVerified($user, 'high_risk');
            $request->session()->forget('mfa_step_up_action');
        }

        // Complete login setup (session, tokens, etc)
        $this->completeLogin($request, $user);

        // Log successful MFA verification
        $logService = app(\App\Services\SecurityEventLogService::class);
        $logService->logAuthentication('mfa_totp', $user->id, true, 'MFA verification successful');

        // Redirect sesuai permission
        if ($user->can('admin.panel')) {
            return redirect()->intended(route('dashboard', absolute: false));
        } else {
            return redirect()->intended(route('welcome', absolute: false))->with('status', 'Selamat datang! Anda dapat menggunakan fitur di bawah untuk melaporkan insiden siber.');
        }
    }

    /**
     * Tampilkan form verifikasi menggunakan backup code
     */
    public function createBackup(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return view('auth.mfa-backup');
    }

    /**
     * Handle verifikasi MFA menggunakan backup code
     */
    public function storeBackup(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:32'],
        ]);

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $code = trim($request->code);

        if (!$this->mfaService->verifyBackupCode($user, $code)) {
            $logService = app(\App\Services\SecurityEventLogService::class);
            $logService->logAuthentication('mfa_backup_code', $user->id, false, 'MFA backup code verification failed');

            throw ValidationException::withMessages([
                'code' => ['Backup code tidak valid atau sudah digunakan.'],
            ]);
        }

        // Set MFA sebagai verified dalam session
        $this->mfaService->setMfaVerified($user, 'login');

        $stepUpAction = $request->session()->get('mfa_step_up_action');
        if ($stepUpAction === 'high_risk') {
            $this->mfaService->setMfaVerified($user, 'high_risk');
            $request->session()->forget('mfa_step_up_action');
        }

        // Complete login setup (session, tokens, dll)
        $this->completeLogin($request, $user);

        // Log successful MFA verification dengan jenis backup code
        $logService = app(\App\Services\SecurityEventLogService::class);
        $logService->logAuthentication('mfa_backup_code', $user->id, true, 'MFA backup code verification successful');

        // Redirect sesuai permission
        if ($user->can('admin.panel')) {
            return redirect()->intended(route('dashboard', absolute: false));
        } else {
            return redirect()->intended(route('welcome', absolute: false))->with('status', 'Selamat datang! Anda dapat menggunakan fitur di bawah untuk melaporkan insiden siber.');
        }
    }

    /**
     * Complete login setup (session, tokens, etc)
     */
    protected function completeLogin(Request $request, $user): void
    {
        // Generate bearer token untuk keamanan tambahan
        $tokenResult = $user->createToken('web-session-token', ['*'], now()->addMinutes(3));

        // Simpan token ID di session
        $request->session()->put('auth_token_id', $tokenResult->accessToken->id);

        // Set last activity time untuk auto logout
        $request->session()->put('last_activity', now()->toDateTimeString());
    }
}

