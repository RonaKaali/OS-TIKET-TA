<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AccessRevocationService;
use App\Services\MfaService;
use App\Services\SecurityEventLogService;
use App\Services\VpnDetectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        protected MfaService $mfaService,
        protected SecurityEventLogService $securityLog,
        protected AccessRevocationService $revocationService,
        protected VpnDetectionService $vpnDetection
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

        // ============================================================
        // VPN / Proxy Detection — Block login if VPN is detected
        // ============================================================
        if (config('zero_trust.vpn_block_enabled', false)) {
            $clientIp = $request->ip();
            $vpnResult = $this->vpnDetection->isVpn($clientIp);

            // Debug: log hasil deteksi untuk setiap login
            Log::info('VPN Detection result', [
                'email' => $request->input('email'),
                'ip' => $clientIp,
                'is_vpn' => $vpnResult['is_vpn'],
                'confidence' => $vpnResult['confidence'],
                'provider' => $vpnResult['provider'],
                'details' => $vpnResult['details'],
            ]);

            if ($vpnResult['is_vpn']) {
                // Logout user yang baru saja authenticated
                Auth::guard('web')->logout();

                // Log ke security monitoring
                $this->securityLog->logAnomaly(
                    null,
                    'vpn_detected',
                    [
                        'ip_address' => $clientIp,
                        'email' => $request->input('email'),
                        'vpn_provider' => $vpnResult['provider'],
                        'confidence' => $vpnResult['confidence'],
                        'details' => $vpnResult['details'],
                        'user_agent' => $request->userAgent(),
                    ]
                );

                Log::warning('VPN login blocked', [
                    'email' => $request->input('email'),
                    'ip' => $clientIp,
                    'provider' => $vpnResult['provider'],
                    'confidence' => $vpnResult['confidence'],
                ]);

                return redirect()->route('login.vpn-blocked')->with([
                    'vpn_ip' => $clientIp,
                    'vpn_provider' => $vpnResult['provider'],
                    'vpn_confidence' => $vpnResult['confidence'],
                ]);
            }
        }

        $request->session()->regenerate();

        $user = Auth::user();
        $user->refresh();

        $mfaEnabled = $user->hasMfaEnabled();

        // Log untuk debugging
        \Log::info('Login MFA check', [
            'user_id' => $user->id,
            'email' => $user->email,
            'mfa_enabled_db' => $user->mfa_enabled ?? false,
            'mfa_enabled_service' => $this->mfaService->isMfaEnabled($user),
            'mfa_secret_exists' => !empty($user->mfa_secret),
        ]);

        if ($mfaEnabled) {
            // Jika MFA enabled, redirect ke halaman verifikasi MFA
            // Jangan set session lengkap dulu, tunggu MFA verified
            \Log::info('MFA enabled for user, redirecting to MFA verification', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return redirect()->route('mfa.verify');
        }

        // Jika tidak ada MFA, lanjutkan dengan setup session normal
        $this->completeLogin($request, $user);

        $this->securityLog->logAuthentication('login', $user->id, true, "Login berhasil: {$user->email}");

        // Redirect sesuai permission
        if ($user->hasRole('Super Admin') || $user->hasRole('Admin')) {
            return redirect()->route('admin.index');
        } elseif ($user->hasRole('Agent 2') || $user->hasRole('Kepala Bidang') || $user->can('admin.panel')) {
            return redirect()->route('agent.dashboard');
        } else {
            // User biasa di-redirect ke welcome page
            return redirect()->intended(route('welcome', absolute: false))->with('status', 'Selamat datang! Anda dapat menggunakan fitur di bawah untuk melaporkan insiden siber.');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Revoke semua token user saat logout
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
            // Generate bearer token untuk keamanan tambahan (disimpan di session, tidak ditampilkan)
            // Token akan digunakan untuk validasi request internal
            $tokenResult = $user->createToken('web-session-token', ['*'], now()->addMinutes(3));
            $request->session()->put('auth_token_id', $tokenResult->accessToken->id);
        } catch (\Exception $e) {
            // Abaikan jika tabel personal_access_tokens tidak ada
        }

        // Set last activity time untuk auto logout
        $request->session()->put('last_activity', now()->toDateTimeString());

        $this->revocationService->clearRevocationFlag($user);
        $this->revocationService->stampSession($request);
    }
}
