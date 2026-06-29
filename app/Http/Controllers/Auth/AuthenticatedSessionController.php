<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AccessRevocationService;
use App\Services\MfaService;
use App\Services\SecurityEventLogService;
use App\Services\VpnDetectionService;
use App\Services\WorkingHoursAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        protected MfaService $mfaService,
        protected SecurityEventLogService $securityLog,
        protected AccessRevocationService $revocationService,
        protected VpnDetectionService $vpnDetection,
        protected WorkingHoursAccessService $workingHours
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

        $user = Auth::user();
        $user->refresh();

        $workingHoursDecision = $this->workingHours->evaluate($user);
        if (!$workingHoursDecision['allowed']) {
            try {
                $this->securityLog->logEvent([
                    'user_id' => $user->id,
                    'event_type' => 'after_hours_login_blocked',
                    'severity' => 'high',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'risk_score' => (int) config('zero_trust.risk_score_threshold_high', 70),
                    'message' => 'Login ditolak karena dilakukan di luar jam kerja.',
                    'context' => [
                        'reason' => $workingHoursDecision['reason'],
                        'attempted_at' => $workingHoursDecision['current_date'] . ' ' . $workingHoursDecision['current_time'],
                        'day' => $workingHoursDecision['current_day'],
                        'schedule' => $workingHoursDecision['schedule_label'],
                        'timezone' => $workingHoursDecision['timezone'],
                    ],
                ]);
            } catch (\Throwable $e) {
                Log::error('Gagal mencatat pemblokiran login di luar jam kerja.', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login.work-hours-blocked')->with([
                'working_hours_access_time' => str_replace(':', '.', $workingHoursDecision['current_time']),
                'working_hours_access_date' => $workingHoursDecision['current_date'],
                'working_hours_access_day' => $workingHoursDecision['current_day'],
                'working_hours_schedule' => $workingHoursDecision['schedule_label'],
                'working_hours_timezone' => $workingHoursDecision['timezone_label'],
            ]);
        }

        // ============================================================
        // VPN / Proxy Detection — Block login if detected
        // Simple: hanya IP Indonesia yang diizinkan
        // ============================================================
        if (config('zero_trust.vpn_block_enabled', false)) {
            $clientIp = $request->ip();

            try {
                // Cek negara IP via ip-api.com
                $response = Http::timeout(3)
                    ->get("http://ip-api.com/json/{$clientIp}?fields=status,country,countryCode,proxy,hosting,mobile,isp,query");

                $isVpn = false;
                $provider = null;
                $decision = 'allowed';

                if ($response->successful()) {
                    $data = $response->json();
                    $country = $data['country'] ?? null;
                    $countryCode = $data['countryCode'] ?? null;
                    $isIndonesia = ($country === 'Indonesia' || $countryCode === 'ID');

                    Log::info('VPN Check', [
                        'ip' => $clientIp,
                        'country' => $country,
                        'countryCode' => $countryCode,
                        'isp' => $data['isp'] ?? null,
                        'proxy' => $data['proxy'] ?? false,
                        'hosting' => $data['hosting'] ?? false,
                        'mobile' => $data['mobile'] ?? false,
                    ]);

                    if ($isIndonesia) {
                        // IP Indonesia → allow
                        $decision = 'allowed_indonesia';
                    } else {
                        // Bukan Indonesia → block
                        $isVpn = true;
                        $provider = $data['isp'] ?? 'Non-Indonesia IP';
                        $decision = 'blocked_non_indonesia';
                    }
                } else {
                    // API gagal → allow (fail open)
                    $decision = 'allowed_api_failed';
                    Log::warning('VPN Check: ip-api gagal', ['ip' => $clientIp, 'status' => $response->status()]);
                }

                Log::info('VPN Decision', [
                    'email' => $request->input('email'),
                    'ip' => $clientIp,
                    'is_vpn' => $isVpn,
                    'decision' => $decision,
                ]);

                if ($isVpn) {
                    Auth::guard('web')->logout();

                    $this->securityLog->logAnomaly(
                        null,
                        'vpn_detected',
                        [
                            'ip_address' => $clientIp,
                            'email' => $request->input('email'),
                            'vpn_provider' => $provider,
                            'user_agent' => $request->userAgent(),
                            'decision' => $decision,
                        ]
                    );

                    Log::warning('VPN login blocked', [
                        'email' => $request->input('email'),
                        'ip' => $clientIp,
                        'provider' => $provider,
                    ]);

                    return redirect()->route('login.vpn-blocked')->with([
                        'vpn_ip' => $clientIp,
                        'vpn_provider' => $provider,
                        'vpn_confidence' => 100,
                    ]);
                }
            } catch (\Throwable $e) {
                // Error → allow (fail open)
                Log::warning('VPN Check: error', ['ip' => $clientIp, 'error' => $e->getMessage()]);
            }
        }

        $request->session()->regenerate();

        $mfaEnabled = $user->hasMfaEnabled();

        Log::info('Login MFA check', [
            'user_id' => $user->id,
            'email' => $user->email,
            'mfa_enabled_db' => $user->mfa_enabled ?? false,
            'mfa_enabled_service' => $this->mfaService->isMfaEnabled($user),
            'mfa_secret_exists' => !empty($user->mfa_secret),
        ]);

        if ($mfaEnabled) {
            Log::info('MFA enabled for user, redirecting to MFA verification', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return redirect()->route('mfa.verify');
        }

        $this->completeLogin($request, $user);

        $this->securityLog->logAuthentication('login', $user->id, true, "Login berhasil: {$user->email}");

        if ($user->hasRole('Super Admin') || $user->hasRole('Admin')) {
            return redirect()->route('admin.index');
        } elseif ($user->hasRole('Agent 2') || $user->hasRole('Kepala Bidang') || $user->can('admin.panel')) {
            return redirect()->route('agent.dashboard');
        } else {
            return redirect()->intended(route('welcome', absolute: false))->with('status', 'Selamat datang! Anda dapat menggunakan fitur di bawah untuk melaporkan insiden siber.');
        }
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
     * Display the working-hours access denial page.
     */
    public function workHoursBlocked(Request $request): View
    {
        $timezone = (string) config('zero_trust.working_hours.timezone', 'Asia/Makassar');
        $now = now($timezone);

        return view('auth.work-hours-blocked', [
            'accessTime' => $request->session()->get('working_hours_access_time', $now->format('H.i')),
            'accessDate' => $request->session()->get('working_hours_access_date', $now->format('d-m-Y')),
            'accessDay' => $request->session()->get('working_hours_access_day', ''),
            'schedule' => $request->session()->get(
                'working_hours_schedule',
                'Senin-Jumat, 08.00-17.00'
            ),
            'timezone' => $request->session()->get('working_hours_timezone', 'WITA (Asia/Makassar)'),
        ]);
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
