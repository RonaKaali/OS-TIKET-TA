<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $table = 'tim';

    protected $fillable = ['nama'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'tim_pengguna', 'id_tim', 'id_pengguna')->withTimestamps();
    }
}
