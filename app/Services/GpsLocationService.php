<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GpsLocationService
{
    public function store(Request $request, int $userId, array $gps): array
    {
        $payload = [
            'latitude' => (float) $gps['latitude'],
            'longitude' => (float) $gps['longitude'],
            'accuracy' => isset($gps['accuracy']) ? (float) $gps['accuracy'] : null,
            'updated_at' => $gps['updated_at'] ?? now()->toIso8601String(),
        ];

        $request->session()->put('zero_trust_gps', $payload);
        $this->persistToDatabase($userId, $payload);

        return $payload;
    }

    public function resolve(?Request $request, ?int $userId): ?array
    {
        if ($request) {
            $sessionGps = $request->session()->get('zero_trust_gps');
            if ($this->isValid($sessionGps)) {
                return $sessionGps;
            }
        }

        return $userId ? $this->getStoredForUser($userId) : null;
    }

    public function getStoredForUser(int $userId): ?array
    {
        try {
            $row = DB::table('pengguna')
                ->where('id', $userId)
                ->select('last_gps')
                ->first();

            if (!$row || $row->last_gps === null) {
                return null;
            }

            $gps = is_string($row->last_gps)
                ? json_decode($row->last_gps, true)
                : (array) $row->last_gps;

            return $this->isValid($gps) ? $gps : null;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function persistToDatabase(int $userId, array $gps): void
    {
        try {
            DB::table('pengguna')->where('id', $userId)->update([
                'last_gps' => json_encode($gps),
                'last_gps_at' => now(),
            ]);
        } catch (\Throwable) {
            // Kolom belum ada — diabaikan sampai /deploy-db dijalankan
        }
    }

    protected function isValid(mixed $gps): bool
    {
        return is_array($gps)
            && isset($gps['latitude'], $gps['longitude'])
            && is_numeric($gps['latitude'])
            && is_numeric($gps['longitude']);
    }
}
