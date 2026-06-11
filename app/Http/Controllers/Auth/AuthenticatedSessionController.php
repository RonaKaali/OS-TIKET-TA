<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AccessRevocationService;
use App\Services\MfaService;
use App\Services\SecurityEventLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        protected MfaService $mfaService,
        protected SecurityEventLogService $securityLog,
        protected AccessRevocationService $revocationService
    ) {}

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        $user->refresh();

        $mfaEnabled = $user->hasMfaEnabled();

        \Log::info('Login MFA check', [
            'user_id'              => $user->id,
            'email'                => $user->email,
            'mfa_enabled_db'       => $user->mfa_enabled ?? false,
            'mfa_enabled_service'  => $this->mfaService->isMfaEnabled($user),
            'mfa_secret_exists'    => !empty($user->mfa_secret),
        ]);

        if ($mfaEnabled) {
            // Bersihkan flag revokasi lama SEBELUM redirect ke MFA
            // agar EnforceAccessRevocation tidak memblokir di halaman MFA verify
            $this->revocationService->clearRevocationFlag($user);

            \Log::info('MFA enabled for user, redirecting to MFA verification', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            return redirect()->route('mfa.verify');
        }

        // Tidak ada MFA — selesaikan setup session langsung
        $this->completeLogin($request, $user);

        $this->securityLog->logAuthentication('login', $user->id, true, "Login berhasil: {$user->email}");

        // Role-based redirect
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        if ($user->hasAnyRole(['Agent', 'Agent 1', 'Agent 2', 'Support Agent'])) {
            return redirect()->intended(route('agent.dashboard', absolute: false));
        }

        return redirect()->intended(route('welcome', absolute: false))
            ->with('status', 'Selamat datang! Anda dapat menggunakan fitur di bawah untuk melaporkan insiden siber.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            try {
                $user->tokens()->delete();
            } catch (\Exception $e) {
                // Abaikan jika tabel personal_access_tokens tidak ada
            }
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Complete login setup (session, tokens, etc)
     */
    protected function completeLogin(Request $request, $user): void
    {
        try {
            $tokenResult = $user->createToken('web-session-token', ['*'], now()->addMinutes(3));
            $request->session()->put('auth_token_id', $tokenResult->accessToken->id);
        } catch (\Exception $e) {
            // Abaikan jika tabel personal_access_tokens tidak ada
        }

        $request->session()->put('last_activity', now()->toDateTimeString());

        $this->revocationService->clearRevocationFlag($user);
        $this->revocationService->stampSession($request);
    }
}
