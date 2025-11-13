<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'lampiran';

    protected $fillable = ['id_utas_tiket', 'nama_file', 'mime', 'ukuran', 'path'];

    public function thread()
    {
        return $this->belongsTo(TicketThread::class, 'id_utas_tiket');
    }
}
