<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SecurityEvent;
use App\Models\User;
use Carbon\Carbon;

class SecurityEventSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin@gmail.com')->first() ?? User::first();
        
        if (!$user) return;

        $events = [
            [
                'user_id' => $user->id,
                'event_type' => 'auth_login',
                'severity' => 'low',
                'ip_address' => '114.125.10.45',
                'message' => 'User logged in successfully from trusted device.',
                'risk_score' => 10,
                'metadata' => ['browser' => 'Chrome', 'os' => 'Windows', 'city' => 'Banjarmasin'],
                'created_at' => Carbon::now()->subMinutes(45),
            ],
            [
                'user_id' => $user->id,
                'event_type' => 'mfa_verified',
                'severity' => 'low',
                'ip_address' => '114.125.10.45',
                'message' => 'MFA TOTP verification successful.',
                'risk_score' => 5,
                'metadata' => ['method' => 'TOTP'],
                'created_at' => Carbon::now()->subMinutes(44),
            ],
            [
                'user_id' => $user->id,
                'event_type' => 'device_registered',
                'severity' => 'medium',
                'ip_address' => '182.1.45.12',
                'message' => 'New device fingerprint registered for user.',
                'risk_score' => 45,
                'metadata' => ['browser' => 'Firefox', 'os' => 'Linux'],
                'created_at' => Carbon::now()->subMinutes(30),
            ],
            [
                'user_id' => $user->id,
                'event_type' => 'anomaly_detected',
                'severity' => 'high',
                'ip_address' => '103.145.2.1',
                'message' => 'Suspicious login attempt: Unusual location detected (Outside Kalimantan).',
                'risk_score' => 85,
                'metadata' => ['browser' => 'Safari', 'os' => 'macOS', 'city' => 'Jakarta'],
                'created_at' => Carbon::now()->subMinutes(15),
            ],
            [
                'user_id' => $user->id,
                'event_type' => 'auth_failed',
                'severity' => 'critical',
                'ip_address' => '103.145.2.1',
                'message' => 'Brute force protection triggered: Multiple failed login attempts.',
                'risk_score' => 95,
                'metadata' => ['attempts' => 5],
                'created_at' => Carbon::now()->subMinutes(5),
            ],
        ];

        foreach ($events as $event) {
            SecurityEvent::create($event);
        }
    }
}
