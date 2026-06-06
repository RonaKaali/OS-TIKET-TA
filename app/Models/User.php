<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

// Tipe relasi (opsional tapi rapi untuk type-hint)
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Atribut yang boleh diisi mass-assignment.
     *
     * @var list<string>
     */
    protected $table = 'pengguna';

    protected $fillable = [
        'name',
        'email',
        'password',
        'id_organisasi',
        'telepon',
        'phone', // alias untuk telepon via mutator
        'nama_pengguna_telegram',
        'telegram_username', // alias untuk nama_pengguna_telegram via mutator
        'id_chat_telegram',
        'telegram_chat_id', // alias untuk id_chat_telegram via mutator
        'remember_token',
        // Zero Trust fields
        'mfa_enabled',
        'mfa_secret',
        'mfa_enabled_at',
        'mfa_backup_codes',
        'device_trust_threshold',
        'require_device_verification',
        'ip_whitelist',
        'allow_after_hours_access',
        'last_security_event_at',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret', // Jangan expose MFA secret
        'mfa_backup_codes',
    ];

    /**
     * Casting atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'mfa_enabled' => 'boolean',
            'mfa_enabled_at' => 'datetime',
            'mfa_backup_codes' => 'array',
            'require_device_verification' => 'boolean',
            'ip_whitelist' => 'array',
            'allow_after_hours_access' => 'boolean',
            'last_security_event_at' => 'datetime',
        ];
    }

    /**
     * Accessor untuk telegram_username (alias dari nama_pengguna_telegram)
     */
    public function getTelegramUsernameAttribute()
    {
        return $this->nama_pengguna_telegram;
    }

    /**
     * Mutator untuk telegram_username (alias dari nama_pengguna_telegram)
     */
    public function setTelegramUsernameAttribute($value)
    {
        $this->attributes['nama_pengguna_telegram'] = $value;
    }

    /**
     * Accessor untuk telegram_chat_id (alias dari id_chat_telegram)
     */
    public function getTelegramChatIdAttribute()
    {
        return $this->id_chat_telegram;
    }

    /**
     * Mutator untuk telegram_chat_id (alias dari id_chat_telegram)
     */
    public function setTelegramChatIdAttribute($value)
    {
        $this->attributes['id_chat_telegram'] = $value;
    }

    /**
     * Accessor untuk phone (alias dari telepon)
     */
    public function getPhoneAttribute()
    {
        return $this->telepon;
    }

    /**
     * Mutator untuk phone (alias dari telepon)
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['telepon'] = $value;
    }

    /**
     * Relasi ke Organization (pengguna.id_organisasi -> organisasi.id).
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Organization::class, 'id_organisasi');
    }

    /**
     * Relasi many-to-many ke Team (pivot: tim_pengguna).
     * Jika nama tabel pivot berbeda, tambahkan ->withTimestamps() sesuai kebutuhan.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Team::class, 'tim_pengguna', 'id_pengguna', 'id_tim')->withTimestamps();
    }

    /**
     * Tiket yang ditugaskan ke user ini (tiket.assigned_to -> pengguna.id).
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(\App\Models\Ticket::class, 'assigned_to');
    }

    /**
     * Tiket yang dibuat oleh user ini sebagai requester (tiket.user_id -> pengguna.id).
     */
    public function requestedTickets(): HasMany
    {
        return $this->hasMany(\App\Models\Ticket::class, 'user_id');
    }

    /**
     * Tiket yang sedang dikunci oleh user ini (tiket.locked_by -> pengguna.id).
     */
    public function lockedTickets(): HasMany
    {
        return $this->hasMany(\App\Models\Ticket::class, 'locked_by');
    }

    /**
     * Thread yang dibuat oleh user ini (utas_tiket.user_id -> pengguna.id).
     */
    public function ticketThreads(): HasMany
    {
        return $this->hasMany(\App\Models\TicketThread::class, 'user_id');
    }

    /**
     * Device fingerprints yang terdaftar untuk user ini.
     */
    public function deviceFingerprints(): HasMany
    {
        return $this->hasMany(\App\Models\DeviceFingerprint::class, 'user_id');
    }

    /**
     * Security events yang terkait dengan user ini.
     */
    public function securityEvents(): HasMany
    {
        return $this->hasMany(\App\Models\SecurityEvent::class, 'user_id');
    }

    /**
     * Cek apakah MFA sudah diaktifkan untuk user ini.
     */
    public function hasMfaEnabled(): bool
    {
        return filter_var($this->mfa_enabled, FILTER_VALIDATE_BOOLEAN)
            && !empty($this->mfa_secret);
    }

    /**
     * Cek apakah user memerlukan verifikasi device.
     */
    public function requiresDeviceVerification(): bool
    {
        return $this->require_device_verification === true;
    }

    /**
     * Cek apakah IP address diizinkan untuk user ini.
     */
    public function isIpAllowed(string $ip): bool
    {
        $whitelist = $this->ip_whitelist ?? [];
        
        if (empty($whitelist)) {
            return true; // Jika tidak ada whitelist, semua IP diizinkan
        }

        return in_array($ip, $whitelist);
    }

    /**
     * Tambahkan IP ke whitelist.
     */
    public function addIpToWhitelist(string $ip): void
    {
        $whitelist = $this->ip_whitelist ?? [];
        
        if (!in_array($ip, $whitelist)) {
            $whitelist[] = $ip;
            $this->ip_whitelist = $whitelist;
            $this->save();
        }
    }

    /**
     * Hapus IP dari whitelist.
     */
    public function removeIpFromWhitelist(string $ip): void
    {
        $whitelist = $this->ip_whitelist ?? [];
        $whitelist = array_values(array_filter($whitelist, fn($item) => $item !== $ip));
        
        $this->ip_whitelist = $whitelist;
        $this->save();
    }

    /**
     * Update last security event timestamp.
     */
    public function updateLastSecurityEvent(): void
    {
        $this->last_security_event_at = now();
        $this->save();
    }
}
