<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'lampiran';

    protected $fillable = ['ticket_thread_id', 'filename', 'original_filename', 'mime', 'size', 'path', 'is_encrypted', 'file_data'];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    public function thread()
    {
        return $this->belongsTo(TicketThread::class, 'ticket_thread_id');
    }
}
