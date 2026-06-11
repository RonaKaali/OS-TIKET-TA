<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke()
    {
        // Hitung overdue menggunakan query SQL langsung (efisien, tidak load semua tiket ke memori)
        // Kriteria overdue: belum tutup, punya due_at, dan sudah lewat lebih dari 7 hari kalender
        // (7 hari kalender selalu mencakup minimal 5 hari kerja Senin-Jumat)
        $overdueCount = Ticket::whereNull('closed_at')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now()->subDays(7))
            ->count();

        $stats = [
            'open'     => Ticket::whereHas('status', fn($q) => $q->where('slug', 'open'))->count(),
            'answered' => Ticket::whereHas('status', fn($q) => $q->where('slug', 'answered'))->count(),
            'overdue'  => $overdueCount,
            'closed'   => Ticket::whereHas('status', fn($q) => $q->where('slug', 'closed'))->count(),
        ];

        return view('agent.dashboard', compact('stats'));
    }
}
