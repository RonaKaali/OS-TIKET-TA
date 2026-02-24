<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class ContextAwareAccessService
{
    /**
     * Analisis konteks request untuk menentukan level akses
     */
    public function analyzeContext(Request $request, User $user): array
    {
        // Gunakan timezone aplikasi untuk konsistensi
        $now = now();

        $context = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => $now->toDateTimeString(),
            'location' => $this->getLocationFromIp($request->ip()),
            'time_of_day' => $now->format('H:i'),
            'day_of_week' => $now->format('l'),
            'is_weekend' => $now->isWeekend(),
            'timezone' => config('app.timezone', 'UTC'),
        ];

        return $context;
    }

    /**
     * Evaluasi apakah akses diizinkan berdasarkan konteks
     */
    public function evaluateAccess(User $user, array $context, string $permission): bool
    {
        // Cek time-based access control
        if (!$this->checkTimeBasedAccess($user, $context)) {
            return false;
        }

        // Cek location-based access control
        if (!$this->checkLocationBasedAccess($user, $context)) {
            return false;
        }

        // Cek IP whitelist/blacklist
        if (!$this->checkIpAccess($user, $context['ip'])) {
            return false;
        }

        // Cek behavioral patterns
        if (!$this->checkBehavioralPattern($user, $context)) {
            return false;
        }

        return true;
    }

    /**
     * Cek time-based access control
     */
    protected function checkTimeBasedAccess(User $user, array $context): bool
    {
        // Admin dan Agent hanya bisa akses pada jam kerja (08:00 - 17:00)
        if ($user->can('admin.panel')) {
            // Parse hour dari time_of_day (format H:i)
            $timeOfDay = $context['time_of_day'] ?? null;

            // Pastikan time_of_day adalah string
            if (!is_string($timeOfDay)) {
                // Jika bukan string, gunakan waktu sekarang
                $timeOfDay = now()->format('H:i');
            }

            // Parse hour dari format H:i dengan validasi
            $parts = explode(':', $timeOfDay);
            $hour = isset($parts[0]) && is_numeric($parts[0]) ? (int) $parts[0] : (int) now()->format('H');

            // Jika di luar jam kerja, cek apakah ada exception
            if ($hour < 8 || $hour >= 17) {
                // Cek apakah user memiliki permission untuk akses di luar jam kerja
                if (!$user->can('admin.after_hours_access')) {
                    // Log sebagai anomaly
                    $this->logAnomaly($user, 'after_hours_access_attempt', $context);
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Cek location-based access control
     */
    protected function checkLocationBasedAccess(User $user, array $context): bool
    {
        $allowedCountries = config('zero_trust.allowed_countries', ['ID']);

        // Jika geolocation enabled, cek country
        if (config('zero_trust.geo_location_enabled', false)) {
            $country = $context['location']['country'] ?? null;

            if ($country && !in_array($country, $allowedCountries)) {
                $this->logAnomaly($user, 'blocked_country_access', $context);
                return false;
            }
        }

        return true;
    }

    /**
     * Cek IP access (whitelist/blacklist)
     */
    protected function checkIpAccess(User $user, string $ip): bool
    {
        // Cek blocked IPs
        // blocked_ips sudah berupa array dari config (sudah di-explode di config/zero_trust.php)
        $blockedIps = config('zero_trust.blocked_ips', []);

        // Pastikan blocked_ips adalah array
        if (!is_array($blockedIps)) {
            // Jika bukan array, convert dari string
            $blockedIps = is_string($blockedIps) ? explode(',', $blockedIps) : [];
        }

        $blockedIps = array_map('trim', $blockedIps);

        if (in_array($ip, $blockedIps)) {
            return false;
        }

        // Untuk admin, bisa set IP whitelist
        if ($user->can('admin.panel')) {
            $whitelistKey = "ip_whitelist:{$user->id}";
            $whitelist = Cache::get($whitelistKey, []);

            if (!empty($whitelist) && !in_array($ip, $whitelist)) {
                $this->logAnomaly($user, 'ip_not_whitelisted', ['ip' => $ip]);
                return false;
            }
        }

        return true;
    }

    /**
     * Cek behavioral pattern (anomaly detection)
     */
    protected function checkBehavioralPattern(User $user, array $context): bool
    {
        $key = "user_access_pattern:{$user->id}";
        $patterns = Cache::get($key, []);

        // Simpan pattern saat ini
        $currentPattern = [
            'ip' => $context['ip'],
            'user_agent' => $context['user_agent'],
            'time' => $context['time_of_day'],
        ];

        // Cek apakah ada perubahan signifikan
        if (!empty($patterns)) {
            $lastPattern = end($patterns);

            // Jika IP berubah drastis (bisa jadi VPN atau proxy)
            if ($lastPattern['ip'] !== $currentPattern['ip']) {
                // Bisa tambahkan logic untuk cek apakah IP change wajar
            }

            // Jika user agent berubah
            if ($lastPattern['user_agent'] !== $currentPattern['user_agent']) {
                $this->logAnomaly($user, 'user_agent_change', $context);
            }
        }

        // Simpan pattern (keep last 10)
        $patterns[] = $currentPattern;
        if (count($patterns) > 10) {
            $patterns = array_slice($patterns, -10);
        }
        Cache::put($key, $patterns, now()->addDays(7));

        return true;
    }

    /**
     * Dapatkan lokasi dari IP (simplified, bisa gunakan service seperti MaxMind)
     */
    protected function getLocationFromIp(string $ip): array
    {
        // Untuk production, gunakan service seperti MaxMind GeoIP2
        // Ini adalah implementasi simplified
        return [
            'ip' => $ip,
            'country' => null, // Akan diisi jika menggunakan GeoIP service
            'city' => null,
        ];
    }

    /**
     * Log anomaly untuk monitoring
     */
    protected function logAnomaly(User $user, string $type, array $context): void
    {
        $logService = app(SecurityEventLogService::class);
        $logService->logEvent([
            'user_id' => $user->id,
            'event_type' => 'anomaly_detected',
            'anomaly_type' => $type,
            'context' => $context,
            'severity' => 'medium',
        ]);
    }

    /**
     * Hitung risk score berdasarkan konteks
     */
    public function calculateRiskScore(User $user, array $context): int
    {
        $riskScore = 0;

        // Risk dari location
        $allowedCountries = config('zero_trust.allowed_countries', ['ID']);
        if (
            isset($context['location']['country']) &&
            !in_array($context['location']['country'], $allowedCountries)
        ) {
            $riskScore += 30;
        }

        // Risk dari time
        $hour = (int) date('H', strtotime($context['time_of_day']));
        if ($hour < 8 || $hour >= 17) {
            $riskScore += 10;
        }

        // Risk dari weekend access
        if ($context['is_weekend']) {
            $riskScore += 5;
        }

        // Risk dari IP change
        $patterns = Cache::get("user_access_pattern:{$user->id}", []);
        if (!empty($patterns)) {
            $lastPattern = end($patterns);
            if ($lastPattern['ip'] !== $context['ip']) {
                $riskScore += 15;
            }
        }

        return min(100, $riskScore);
    }
}

