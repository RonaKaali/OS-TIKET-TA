<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';

    protected $fillable = ['name', 'slug', 'is_closed'];
    protected $casts = ['is_closed' => 'boolean'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'status_id');
    }
}
