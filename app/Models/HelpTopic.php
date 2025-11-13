<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HelpTopic extends Model
{
    use HasFactory;

    protected $table = 'topik_bantuan';

    protected $fillable = ['nama', 'id_departemen', 'skema_formulir'];
    protected $casts = ['skema_formulir' => 'array'];

    public function department()
    {
        return $this->belongsTo(Department::class, 'id_departemen');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'id_topik_bantuan');
    }
}
