<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'lampiran';

    protected $fillable = ['ticket_thread_id', 'filename', 'mime', 'size', 'path'];

    public function thread()
    {
        return $this->belongsTo(TicketThread::class, 'ticket_thread_id');
    }
}
