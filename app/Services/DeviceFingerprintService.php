<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\DeviceFingerprint;
use App\Models\User;
use App\Notifications\DeviceVerificationRequired;

class DeviceFingerprintService
{
    /**
     * Generate device fingerprint dari request
     */
    public function generateFingerprint(Request $request): string
    {
        $clientIp = $this->getClientIp($request);

        $components = [
            $request->userAgent(),
            $clientIp,
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
            $request->header('Accept'),
            $request->header('DNT'),
            $request->header('Connection'),
        ];

        // Tambahkan screen resolution jika tersedia (dari JavaScript)
        if ($request->has('screen_resolution')) {
            $components[] = $request->input('screen_resolution');
        }

        // Tambahkan timezone jika tersedia
        if ($request->has('timezone')) {
            $components[] = $request->input('timezone');
        }

        $fingerprintString = implode('|', array_filter($components));
        
        return hash('sha256', $fingerprintString);
    }

    /**
     * Ambil IP klien sebenarnya (X-Forwarded-For jika ada, fallback ke request->ip()).
     */
    protected function getClientIp(Request $request): string
    {
        $forwarded = $request->header('X-Forwarded-For');
        if ($forwarded) {
            $parts = explode(',', $forwarded);
            $ip = trim($parts[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        return $request->ip();
    }

    /**
     * Cek apakah device sudah terdaftar untuk user
     */
    public function isDeviceRegistered(User $user, string $fingerprint): bool
    {
        if ($this->findDevice($user, $fingerprint)) {
            return true;
        }

        $devices = Cache::get("user_devices:{$user->id}", []);
        
        return in_array($fingerprint, $devices);
    }

    /**
     * Register device baru untuk user
     */
    public function registerDevice(User $user, string $fingerprint, array $metadata = []): void
    {
        $devices = Cache::get("user_devices:{$user->id}", []);
        
        if (!in_array($fingerprint, $devices)) {
            $devices[] = $fingerprint;
            Cache::put("user_devices:{$user->id}", $devices, now()->addDays(30));
        }

        $deviceMetadata = [
            'user_agent' => $metadata['user_agent'] ?? null,
            'ip' => $metadata['ip'] ?? null,
            'registered_at' => now()->toDateTimeString(),
            'last_seen' => now()->toDateTimeString(),
            'trust_score' => 30,
            'is_verified' => false,
        ];
        Cache::put("device_metadata:{$user->id}:{$fingerprint}", $deviceMetadata, now()->addDays(30));

        try {
            $device = DeviceFingerprint::firstOrNew([
                'user_id' => $user->id,
                'fingerprint' => $fingerprint,
            ]);

            if (!$device->exists) {
                $device->registered_at = now();
                $device->is_verified = false;
                $device->trust_score = 30;
            }

            $device->fill([
                'user_agent' => $metadata['user_agent'] ?? null,
                'ip_address' => $metadata['ip'] ?? null,
                'screen_resolution' => $metadata['screen_resolution'] ?? null,
                'timezone' => $metadata['timezone'] ?? null,
                'last_seen_at' => now(),
                'metadata' => $metadata,
            ]);
            $device->save();
        } catch (\Throwable $e) {
            \Log::error('Failed to save device fingerprint to database: ' . $e->getMessage());
        }
    }

    /**
     * Update last seen untuk device
     */
    public function updateDeviceLastSeen(User $user, string $fingerprint): void
    {
        $metadata = Cache::get("device_metadata:{$user->id}:{$fingerprint}", []);
        
        if (!empty($metadata)) {
            $metadata['last_seen'] = now()->toDateTimeString();
            Cache::put("device_metadata:{$user->id}:{$fingerprint}", $metadata, now()->addDays(30));
        }

        try {
            DeviceFingerprint::where('user_id', $user->id)
                ->where('fingerprint', $fingerprint)
                ->update(['last_seen_at' => now()]);
        } catch (\Throwable $e) {
            \Log::warning('Failed to update device fingerprint last seen: ' . $e->getMessage());
        }
    }

    /**
     * Hitung device trust score
     */
    public function calculateTrustScore(User $user, string $fingerprint, Request $request): int
    {
        $clientIp = $this->getClientIp($request);
        $device = $this->findDevice($user, $fingerprint);

        if (!$device) {
            return 30;
        }

        $score = $device->is_verified ? 75 : 35;
        $metadata = Cache::get("device_metadata:{$user->id}:{$fingerprint}", []);
        
        $registeredAt = $device->registered_at ?? ($metadata['registered_at'] ?? null);
        if ($registeredAt && $device->is_verified) {
            $daysSinceRegistration = now()->diffInDays($registeredAt);
            if ($daysSinceRegistration > 7) {
                $score += 10;
            }
            if ($daysSinceRegistration > 30) {
                $score += 10;
            }
        }

        $lastSeen = $device->last_seen_at ?? ($metadata['last_seen'] ?? null);
        if ($lastSeen && $device->is_verified) {
            $hoursSinceLastSeen = now()->diffInHours($lastSeen);
            if ($hoursSinceLastSeen < 24) {
                $score += 10;
            }
        }
        
        // Kurangi score jika IP berbeda dari yang terdaftar
        $registeredIp = $device->ip_address ?? ($metadata['ip'] ?? null);
        if (!empty($registeredIp) && $registeredIp !== $clientIp) {
            $score -= 20;
        }
        
        // Kurangi score jika user agent berbeda
        if (!empty($metadata['user_agent']) && $metadata['user_agent'] !== $request->userAgent()) {
            $score -= 15;
        }
        
        // Pastikan score dalam range 0-100
        return max(0, min(100, $score));
    }

    /**
     * Dapatkan semua device yang terdaftar untuk user
     */
    public function getUserDevices(User $user): array
    {
        try {
            return DeviceFingerprint::where('user_id', $user->id)
                ->orderByDesc('last_seen_at')
                ->get()
                ->map(fn (DeviceFingerprint $device) => [
                    'fingerprint' => $device->fingerprint,
                    'metadata' => [
                        'user_agent' => $device->user_agent,
                        'ip' => $device->ip_address,
                        'registered_at' => $device->registered_at?->toDateTimeString(),
                        'last_seen' => $device->last_seen_at?->toDateTimeString(),
                        'trust_score' => $device->trust_score,
                        'is_verified' => $device->is_verified,
                    ],
                ])
                ->all();
        } catch (\Throwable) {
            // Fallback untuk instalasi yang belum menjalankan migrasi device_fingerprints.
        }

        $devices = Cache::get("user_devices:{$user->id}", []);
        $devicesWithMetadata = [];
        
        foreach ($devices as $fingerprint) {
            $metadata = Cache::get("device_metadata:{$user->id}:{$fingerprint}", []);
            $devicesWithMetadata[] = [
                'fingerprint' => $fingerprint,
                'metadata' => $metadata,
            ];
        }
        
        return $devicesWithMetadata;
    }

    /**
     * Hapus device dari daftar user
     */
    public function removeDevice(User $user, string $fingerprint): void
    {
        $devices = Cache::get("user_devices:{$user->id}", []);
        $devices = array_values(array_filter($devices, fn($d) => $d !== $fingerprint));
        
        Cache::put("user_devices:{$user->id}", $devices, now()->addDays(30));
        Cache::forget("device_metadata:{$user->id}:{$fingerprint}");

        try {
            DeviceFingerprint::where('user_id', $user->id)
                ->where('fingerprint', $fingerprint)
                ->delete();
        } catch (\Throwable $e) {
            \Log::warning('Failed to remove device fingerprint from database: ' . $e->getMessage());
        }
    }

    /**
     * Cek apakah device memerlukan verifikasi tambahan
     */
    public function requiresVerification(User $user, string $fingerprint, int $trustScore): bool
    {
        $threshold = (int) config('zero_trust.device_trust_score_threshold', 70);
        
        return !$this->isDeviceVerified($user, $fingerprint) || $trustScore < $threshold;
    }

    public function isDeviceVerified(User $user, string $fingerprint): bool
    {
        $device = $this->findDevice($user, $fingerprint);

        return (bool) ($device?->is_verified);
    }

    public function markDeviceVerified(User $user, string $fingerprint, ?Request $request = null): void
    {
        $metadata = [
            'verified_at' => now()->toIso8601String(),
            'verified_by' => 'mfa_or_signed_email',
        ];

        if ($request) {
            $metadata['user_agent'] = $request->userAgent();
            $metadata['ip'] = $this->getClientIp($request);
        }

        try {
            $device = DeviceFingerprint::firstOrNew([
                'user_id' => $user->id,
                'fingerprint' => $fingerprint,
            ]);

            if (!$device->exists) {
                $device->registered_at = now();
            }

            $device->fill([
                'user_agent' => $metadata['user_agent'] ?? $device->user_agent,
                'ip_address' => $metadata['ip'] ?? $device->ip_address,
                'trust_score' => max((int) $device->trust_score, (int) config('zero_trust.device_trust_score_threshold', 70)),
                'last_seen_at' => now(),
                'is_verified' => true,
                'metadata' => array_merge((array) ($device->metadata ?? []), $metadata),
            ]);
            $device->save();
        } catch (\Throwable $e) {
            \Log::error('Failed to mark device as verified: ' . $e->getMessage());
            throw $e;
        }

        $cached = Cache::get("device_metadata:{$user->id}:{$fingerprint}", []);
        $cached['is_verified'] = true;
        $cached['trust_score'] = max((int) ($cached['trust_score'] ?? 0), (int) config('zero_trust.device_trust_score_threshold', 70));
        $cached['last_seen'] = now()->toDateTimeString();
        Cache::put("device_metadata:{$user->id}:{$fingerprint}", $cached, now()->addDays(30));
    }

    public function sendVerificationChallenge(User $user, string $fingerprint, Request $request): void
    {
        $cacheKey = "device_verification_email_sent:{$user->id}:{$fingerprint}";
        if (Cache::has($cacheKey)) {
            return;
        }

        try {
            $user->notify(new DeviceVerificationRequired(
                fingerprint: $fingerprint,
                ipAddress: $this->getClientIp($request),
                userAgent: (string) $request->userAgent()
            ));
            Cache::put($cacheKey, true, now()->addMinutes(10));
        } catch (\Throwable $e) {
            \Log::warning('Failed to send device verification challenge email: ' . $e->getMessage());
        }
    }

    protected function findDevice(User $user, string $fingerprint): ?DeviceFingerprint
    {
        try {
            return DeviceFingerprint::where('user_id', $user->id)
                ->where('fingerprint', $fingerprint)
                ->first();
        } catch (\Throwable) {
            return null;
        }
    }
}
