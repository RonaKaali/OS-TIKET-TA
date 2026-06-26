<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityEvent extends Model
{
    use HasFactory;

    protected $table = 'security_events';

    public $timestamps = false; // Menggunakan created_at manual

    protected $fillable = [
        'user_id',
        'event_type',
        'severity',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'context',
        'message',
        'metadata',
        'risk_score',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'metadata' => 'array',
            'risk_score' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope untuk filter berdasarkan severity.
     */
    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope untuk filter berdasarkan event type.
     */
    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope untuk filter berdasarkan user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter high risk events.
     */
    public function scopeHighRisk($query, int $threshold = 70)
    {
        return $query->where('risk_score', '>=', $threshold);
    }

    /**
     * Scope untuk recent events.
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Sembunyikan event polling/heartbeat yang membanjiri feed monitoring.
     * Juga filter event device_registered agar tidak spam.
     */
    public function scopeExcludeNoise($query)
    {
        return $query->where(function ($q) {
            $q->where('message', 'not like', '%security-events/latest%')
                ->where('message', 'not like', '%session/check%')
                ->where('message', 'not like', '%zero-trust/gps%')
                ->where('event_type', 'not like', '%device_registered%')
                ->where('event_type', 'not like', '%device_registration%');
        });
    }

    /**
     * Filter event berdasarkan periode unduhan log.
     */
    public function scopeForPeriod($query, string $period)
    {
        $start = match ($period) {
            'week' => now()->subDays(6)->startOfDay(),
            'month' => now()->startOfMonth(),
            default => now()->startOfDay(),
        };

        return $query->where('created_at', '>=', $start);
    }
}

