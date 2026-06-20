<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\{Ticket, TicketThread};
use App\Support\RoleUi;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:tickets.update']);
    }

    public function __invoke(Request $r, Ticket $ticket)
    {
        if (!RoleUi::canManageAllTickets($r->user())) {
            abort_unless($ticket->assigned_to === $r->user()?->id, 403, 'Anda tidak memiliki akses ke tiket ini.');
        }

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
