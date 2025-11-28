<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketThread extends Model
{
    use HasFactory;

    protected $table = 'utas_tiket';

    protected $fillable = ['ticket_id', 'type', 'user_id', 'body'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'ticket_thread_id');
    }
}
