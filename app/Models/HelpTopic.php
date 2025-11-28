<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HelpTopic extends Model
{
    use HasFactory;

    protected $table = 'topik_bantuan';

    protected $fillable = ['name', 'department_id', 'form_schema'];
    protected $casts = ['form_schema' => 'array'];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'help_topic_id');
    }
}
