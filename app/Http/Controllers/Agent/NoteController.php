<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\{Ticket, TicketThread};
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:tickets.update']);
    }

    public function __invoke(Request $r, Ticket $ticket)
    {
        $data = $r->validate([
            'note' => ['required', 'string', 'max:20000'],
        ]);

        TicketThread::create([
            'ticket_id' => $ticket->id,
            'type' => 'note',
            'user_id' => $r->user()->id,
            'body' => $data['note'],
        ]);

        return back()->with('ok', 'Catatan internal ditambahkan.');
    }
}
