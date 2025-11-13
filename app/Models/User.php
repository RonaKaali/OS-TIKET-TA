<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

// Tipe relasi (opsional tapi rapi untuk type-hint)
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Atribut yang boleh diisi mass-assignment.
     *
     * @var list<string>
     */
    protected $table = 'pengguna';

    protected $fillable = [
        'nama',
        'email',
        'password', // akan di-mapping ke kata_sandi via mutator
        'kata_sandi', // untuk backward compatibility
        // tambahkan ini hanya jika kolomnya ada di tabel pengguna
        'id_organisasi',
        'telepon',
        'nama_pengguna_telegram',
        'id_chat_telegram',
        'remember_token',
    ];

    /**
     * Get the name of the password attribute for authentication.
     */
    public function getAuthPasswordName()
    {
        return 'kata_sandi';
    }

    /**
     * Atribut yang disembunyikan saat serialisasi.
     *
     * @var list<string>
     */
    protected $hidden = [
        'kata_sandi',
        'password',
        'remember_token',
    ];

    /**
     * Mutator untuk password - map 'password' ke 'kata_sandi'
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['kata_sandi'] = $value;
    }

    /**
     * Accessor untuk password - map 'kata_sandi' ke 'password'
     */
    public function getPasswordAttribute()
    {
        return $this->attributes['kata_sandi'] ?? null;
    }

    /**
     * Casting atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_terverifikasi_pada' => 'datetime',
            'kata_sandi' => 'hashed',
            'password' => 'hashed', // untuk compatibility
        ];
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
     * Tiket yang ditugaskan ke user ini (tiket.ditugaskan_ke -> pengguna.id).
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(\App\Models\Ticket::class, 'ditugaskan_ke');
    }

    /**
     * Tiket yang dibuat oleh user ini sebagai requester (tiket.id_pengguna -> pengguna.id).
     */
    public function requestedTickets(): HasMany
    {
        return $this->hasMany(\App\Models\Ticket::class, 'id_pengguna');
    }

    /**
     * Tiket yang sedang dikunci oleh user ini (tiket.dikunci_oleh -> pengguna.id).
     */
    public function lockedTickets(): HasMany
    {
        return $this->hasMany(\App\Models\Ticket::class, 'dikunci_oleh');
    }

    /**
     * Thread yang dibuat oleh user ini (utas_tiket.id_pengguna -> pengguna.id).
     */
    public function ticketThreads(): HasMany
    {
        return $this->hasMany(\App\Models\TicketThread::class, 'id_pengguna');
    }
}
