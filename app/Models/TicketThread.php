<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketThread extends Model
{
    use HasFactory;

    protected $table = 'utas_tiket';

    protected $fillable = ['id_tiket', 'tipe', 'id_pengguna', 'isi'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'id_tiket');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'id_utas_tiket');
    }
}
