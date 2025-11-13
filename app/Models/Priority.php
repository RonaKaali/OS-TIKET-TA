<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Priority extends Model
{
    use HasFactory;

    protected $table = 'prioritas';

    protected $fillable = ['nama', 'bobot'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_prioritas');
    }
}
