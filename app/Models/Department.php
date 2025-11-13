<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departemen';

    protected $fillable = ['nama', 'email', 'publik'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_departemen');
    }

    public function helpTopics()
    {
        return $this->hasMany(HelpTopic::class, 'id_departemen');
    }
}
