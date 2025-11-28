<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{
    use HasFactory;

    protected $table = 'organisasi';

    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class, 'id_organisasi');
    }
}
