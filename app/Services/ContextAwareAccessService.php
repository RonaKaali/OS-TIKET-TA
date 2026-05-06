<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use GeoIp2\Database\Reader;

class ContextAwareAccessService
{
    /**
     * Analisis konteks request untuk menentukan level akses
     */
    public function analyzeContext(Request $request, User $user): array
    {
        // Gunakan timezone aplikasi untuk konsistensi
        $now = now();

        // Ambil IP klien asli (menghormati proxy / ngrok jika ada)
        $clientIp = $this->getClientIp($request);
        $gps = $request->session()->get('zero_trust_gps');

        $context = [
            'ip' => $clientIp,
            'user_agent' => $request->userAgent(),
            'timestamp' => $now->toDateTimeString(),
            'location' => $this->getLocationFromIp($clientIp),
            'time_of_day' => $now->format('H:i'),
            'day_of_week' => $now->format('l'),
            'is_weekend' => $now->isWeekend(),
            'timezone' => config('app.timezone', 'UTC'),
            'gps' => $gps,
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
            'timestamp' => $context['timestamp'],
            'location' => $context['location'],
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
        $location = [
            'ip' => $ip,
            'country' => null,
            'country_name' => null,
            'city' => null,
            'latitude' => null,
            'longitude' => null,
            'timezone' => null,
            'source' => 'none',
        ];

        // Skip lookup untuk localhost / private range (biar cepat dan tidak noisy)
        if (
            $ip === '127.0.0.1' ||
            $ip === '::1' ||
            filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false
        ) {
            return $location;
        }

        if (!config('zero_trust.geo_location_enabled', false)) {
            return $location;
        }

        $dbPath = (string) config('zero_trust.geoip_db_path', '');
        if ($dbPath === '' || !is_file($dbPath)) {
            \Log::warning('GeoIP database not found', [
                'db_path' => $dbPath,
            ]);
            return $location;
        }

        try {
            $reader = new Reader($dbPath);
            $record = $reader->city($ip);

            $location['country'] = $record->country->isoCode ?: null;
            $location['country_name'] = $record->country->name ?: null;
            $location['city'] = $record->city->name ?: null;
            $location['latitude'] = $record->location->latitude;
            $location['longitude'] = $record->location->longitude;
            $location['timezone'] = $record->location->timeZone ?: null;
            $location['source'] = 'maxmind_mmdb';

            return $location;
        } catch (\Throwable $e) {
            \Log::warning('GeoIP lookup failed', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
            return $location;
        }
    }

    /**
     * Dapatkan IP klien sebenarnya dengan mempertimbangkan header X-Forwarded-For.
     */
    protected function getClientIp(Request $request): string
    {
        $forwarded = $request->header('X-Forwarded-For');
        if ($forwarded) {
            // Ambil IP pertama (asal klien) dari daftar yang dipisah koma.
            $parts = explode(',', $forwarded);
            $ip = trim($parts[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        return $request->ip();
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
            
            // 1. Basic IP Change Risk
            if ($lastPattern['ip'] !== $context['ip']) {
                $riskScore += 15;
            }

            // 2. Impossible Travel Detection
            // Jika lokasi tersedia untuk sesi sebelumnya dan sesi sekarang
            $lastLocation = $lastPattern['location'] ?? null;
            $currentLocation = $context['location'] ?? null;

            if (
                isset($lastLocation['latitude'], $lastLocation['longitude'], $currentLocation['latitude'], $currentLocation['longitude']) &&
                isset($lastPattern['timestamp'])
            ) {
                $distanceKm = $this->calculateDistanceKm(
                    (float) $lastLocation['latitude'],
                    (float) $lastLocation['longitude'],
                    (float) $currentLocation['latitude'],
                    (float) $currentLocation['longitude']
                );

                $timeDiffSeconds = now()->diffInSeconds(\Carbon\Carbon::parse($lastPattern['timestamp']));
                
                // Jika jarak > 100km dan waktu < 1 jam (impossible travel simulation)
                if ($distanceKm > 100 && $timeDiffSeconds < 3600) {
                    $requiredSpeed = ($distanceKm / ($timeDiffSeconds / 3600)); // km/h
                    
                    if ($requiredSpeed > 800) { // Lebih cepat dari pesawat komersial
                        $riskScore += 40;
                        $this->logAnomaly($user, 'impossible_travel_detected', array_merge($context, [
                            'prev_location' => $lastLocation,
                            'distance_km' => $distanceKm,
                            'time_diff_sec' => $timeDiffSeconds,
                            'required_speed_kmh' => $requiredSpeed
                        ]));
                    }
                }
            }
        }

        // Risk dari perbedaan signifikan antara GeoIP dan GPS (jika GPS tersedia)
        if (!empty($context['gps']) && isset($context['location']['latitude'], $context['location']['longitude'])) {
            $gps = $context['gps'];
            if (isset($gps['latitude'], $gps['longitude'])) {
                $distanceKm = $this->calculateDistanceKm(
                    (float) $gps['latitude'],
                    (float) $gps['longitude'],
                    (float) $context['location']['latitude'],
                    (float) $context['location']['longitude']
                );

                if ($distanceKm > 1000) {
                    $riskScore += 20;
                    $this->logAnomaly($user, 'geoip_gps_mismatch_large', array_merge($context, [
                        'distance_km' => $distanceKm,
                    ]));
                } elseif ($distanceKm > 300) {
                    $riskScore += 10;
                }
            }
        }

        return min(100, $riskScore);
    }

    /**
     * Hitung jarak dua koordinat (km) dengan rumus haversine sederhana.
     */
    protected function calculateDistanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}

