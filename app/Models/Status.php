<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';

    protected $fillable = ['nama', 'slug', 'menutup'];
    protected $casts = ['menutup' => 'boolean'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_status');
    }
}
