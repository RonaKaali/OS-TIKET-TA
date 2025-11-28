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
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
}
