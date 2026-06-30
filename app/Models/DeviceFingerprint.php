<?php

namespace App\Models;

use App\Support\DatabaseBoolean;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceFingerprint extends Model
{
    use HasFactory;

    protected $table = 'device_fingerprints';

    protected $fillable = [
        'user_id',
        'fingerprint',
        'user_agent',
        'ip_address',
        'screen_resolution',
        'timezone',
        'trust_score',
        'registered_at',
        'last_seen_at',
        'is_verified',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'is_verified' => 'boolean',
            'trust_score' => 'integer',
            'metadata' => 'array',
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
     * Update last seen timestamp.
     */
    public function updateLastSeen(): void
    {
        $this->last_seen_at = now();
        $this->save();
    }

    /**
     * Mark device as verified.
     */
    public function markAsVerified(): void
    {
        $this->is_verified = DatabaseBoolean::value(true);
        $this->save();
    }

    /**
     * Update trust score.
     */
    public function updateTrustScore(int $score): void
    {
        $this->trust_score = max(0, min(100, $score));
        $this->save();
    }
}
