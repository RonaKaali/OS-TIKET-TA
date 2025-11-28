<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departemen';

    protected $fillable = ['name', 'email', 'is_public'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'department_id');
    }

    public function helpTopics()
    {
        return $this->hasMany(HelpTopic::class, 'department_id');
    }
}
