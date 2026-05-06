<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

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
            
            // Simpan metadata device
            $deviceMetadata = [
                'user_agent' => $metadata['user_agent'] ?? null,
                'ip' => $metadata['ip'] ?? null,
                'registered_at' => now()->toDateTimeString(),
                'last_seen' => now()->toDateTimeString(),
                'trust_score' => 50, // Default trust score untuk device baru
            ];
            Cache::put("device_metadata:{$user->id}:{$fingerprint}", $deviceMetadata, now()->addDays(30));
            
            // Simpan ke database jika model tersedia
            if (class_exists(\App\Models\DeviceFingerprint::class)) {
                try {
                    \App\Models\DeviceFingerprint::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'fingerprint' => $fingerprint,
                        ],
                        [
                            'user_agent' => $metadata['user_agent'] ?? null,
                            'ip_address' => $metadata['ip'] ?? null,
                            'screen_resolution' => $metadata['screen_resolution'] ?? null,
                            'timezone' => $metadata['timezone'] ?? null,
                            'trust_score' => 50,
                            'registered_at' => now(),
                            'last_seen_at' => now(),
                            'is_verified' => false,
                            'metadata' => $metadata,
                        ]
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to save device fingerprint to database: ' . $e->getMessage());
                }
            }
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
    }

    /**
     * Hitung device trust score
     */
    public function calculateTrustScore(User $user, string $fingerprint, Request $request): int
    {
        $score = 50; // Base score
        $metadata = Cache::get("device_metadata:{$user->id}:{$fingerprint}", []);
        
        // Jika device sudah terdaftar, tambahkan score
        if (!empty($metadata)) {
            $score += 20;
            
            // Tambahkan score berdasarkan usia device
            $registeredAt = $metadata['registered_at'] ?? null;
            if ($registeredAt) {
                $daysSinceRegistration = now()->diffInDays($registeredAt);
                if ($daysSinceRegistration > 7) {
                    $score += 10; // Device sudah digunakan lebih dari seminggu
                }
                if ($daysSinceRegistration > 30) {
                    $score += 10; // Device sudah digunakan lebih dari sebulan
                }
            }
            
            // Tambahkan score berdasarkan frekuensi penggunaan
            $lastSeen = $metadata['last_seen'] ?? null;
            if ($lastSeen) {
                $hoursSinceLastSeen = now()->diffInHours($lastSeen);
                if ($hoursSinceLastSeen < 24) {
                    $score += 10; // Device digunakan dalam 24 jam terakhir
                }
            }
        }
        
        // Kurangi score jika IP berbeda dari yang terdaftar
        if (!empty($metadata['ip']) && $metadata['ip'] !== $request->ip()) {
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
    }

    /**
     * Cek apakah device memerlukan verifikasi tambahan
     */
    public function requiresVerification(User $user, string $fingerprint, int $trustScore): bool
    {
        $threshold = (int) config('zero_trust.device_trust_score_threshold', 70);
        
        return $trustScore < $threshold;
    }
}

