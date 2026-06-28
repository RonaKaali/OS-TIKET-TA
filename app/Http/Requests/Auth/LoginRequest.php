<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Hitung attempt + log
            $this->incrementFailedAttempt();
            $this->logFailedAttempt();

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Login sukses — reset counter
        $this->clearFailedAttempts();
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     * Uses SESSION (cookie) for persistence across serverless requests.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Cek dari session (cookie-based — work di Vercel serverless)
        $lockoutUntil = session()->get('login_lockout_' . $this->throttleKey());
        if ($lockoutUntil && now()->lt($lockoutUntil)) {
            $seconds = now()->diffInSeconds($lockoutUntil);
            $minutes = ceil($seconds / 60);

            // Log lockout event
            $this->logLockout($seconds);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => $minutes,
                ]),
            ]);
        }

        // Cek dari Laravel cache (fallback for local)
        if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            event(new Lockout($this));
            $seconds = RateLimiter::availableIn($this->throttleKey());
            $minutes = ceil($seconds / 60);

            $this->logLockout($seconds);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => $minutes,
                ]),
            ]);
        }
    }

    /**
     * Increment failed attempt counter using session (cookie-based).
     */
    protected function incrementFailedAttempt(): void
    {
        // Session-based counter (work di serverless)
        $attempts = (int) session()->get('login_attempts_' . $this->throttleKey(), 0);
        $attempts++;
        session()->put('login_attempts_' . $this->throttleKey(), $attempts);

        // Set lockout jika sudah 5 attempts
        if ($attempts >= 5) {
            session()->put('login_lockout_' . $this->throttleKey(), now()->addMinutes(1));
            // Reset counter untuk next cycle
            session()->put('login_attempts_' . $this->throttleKey(), 0);
        }
    }

    /**
     * Clear failed attempt counter.
     */
    protected function clearFailedAttempts(): void
    {
        session()->forget('login_attempts_' . $this->throttleKey());
        session()->forget('login_lockout_' . $this->throttleKey());
    }

    /**
     * Log failed login attempt ke Security Monitoring.
     */
    protected function logFailedAttempt(): void
    {
        try {
            $ip = $this->ip();
            $email = $this->input('email');
            $attempts = RateLimiter::attempts($this->throttleKey());

            $securityLog = app(\App\Services\SecurityEventLogService::class);

            $securityLog->logAnomaly(null, 'brute_force_attempt', [
                'ip_address' => $ip,
                'email' => $email,
                'attempts' => $attempts,
                'max_attempts' => 5,
                'user_agent' => $this->userAgent(),
            ]);

            Log::warning('Brute Force: Failed login attempt', [
                'email' => $email,
                'ip' => $ip,
                'attempts' => $attempts,
            ]);
        } catch (\Throwable $e) {
            // Jangan gagalkan login karena error logging
        }
    }

    /**
     * Log account lockout ke Security Monitoring.
     */
    protected function logLockout(int $seconds): void
    {
        try {
            $ip = $this->ip();
            $email = $this->input('email');
            $minutes = ceil($seconds / 60);

            $securityLog = app(\App\Services\SecurityEventLogService::class);

            $securityLog->logEvent([
                'user_id' => null,
                'event_type' => 'brute_force_lockout',
                'severity' => 'high',
                'ip_address' => $ip,
                'message' => "Akun {$email} terkunci selama {$minutes} menit karena 5 kali gagal login",
                'context' => [
                    'email' => $email,
                    'lockout_duration' => $seconds,
                    'lockout_duration_label' => $minutes . ' menit',
                    'failed_attempts' => RateLimiter::attempts($this->throttleKey()),
                    'max_attempts' => 5,
                ],
            ]);

            Log::warning('Brute Force: Account locked', [
                'email' => $email,
                'ip' => $ip,
                'duration' => $seconds . 's',
            ]);
        } catch (\Throwable $e) {
            // Jangan gagalkan login karena error logging
        }
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
