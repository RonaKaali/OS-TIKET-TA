<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Ticket::count(),
            'open' => Ticket::whereHas('status', function ($query) {
                $query->where('slug', 'open');
            })->count(),
            'in_progress' => Ticket::whereHas('status', function ($query) {
                $query->whereIn('slug', ['answered', 'in-progress']);
            })->count(),
            'resolved' => Ticket::whereHas('status', function ($query) {
                $query->whereIn('slug', ['closed', 'resolved']);
            })->count(),
        ];

        // Laporan per departemen
        $departments = \App\Models\Department::withCount('tickets')->get();

        return view('admin.dashboard.index', compact('stats', 'departments'));
    }

    /**
     * Fitur Laporan Akhir untuk Super Admin
     */
    public function reports()
    {
        $stats = [
            'total' => Ticket::count(),
            'completed' => Ticket::whereHas('status', function ($query) {
                $query->whereIn('slug', ['closed', 'resolved']);
            })->count(),
            'ongoing' => Ticket::whereHas('status', function ($query) {
                $query->whereIn('slug', ['open', 'answered', 'in-progress']);
            })->count(),
            'critical' => Ticket::whereHas('priority', function ($query) {
                $query->where('slug', 'emergency');
            })->count(),
        ];

        // Statistik Bulanan
        $monthlyStats = Ticket::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Persentase Penyelesaian
        $completionRate = $stats['total'] > 0 ? ($stats['completed'] / $stats['total']) * 100 : 0;

        return view('admin.dashboard.reports', compact('stats', 'monthlyStats', 'completionRate'));
    }
}
