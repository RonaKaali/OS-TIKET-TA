<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SlaPlan extends Model
{
    use HasFactory;

    protected $table = 'rencana_sla';

    protected $fillable = ['name', 'grace_hours'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'sla_plan_id');
    }
}
