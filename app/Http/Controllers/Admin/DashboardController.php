<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        // Cache statistik dashboard selama 60 detik
        $stats = Cache::remember('admin.dashboard.stats', 60, function () {
            return [
                'total' => Ticket::count(),
                'open' => Ticket::whereHas('status', fn($q) => $q->where('slug', 'open'))
                    ->count(),
                'in_progress' => Ticket::whereHas('status', fn($q) => $q->whereIn('slug', ['answered', 'in-progress']))
                    ->count(),
                'resolved' => Ticket::whereHas('status', fn($q) => $q->whereIn('slug', ['closed', 'resolved']))
                    ->count(),
            ];
        });

        // Cache laporan per departemen selama 60 detik
        $departments = Cache::remember('admin.dashboard.departments', 60, function () {
            return \App\Models\Department::withCount('tickets')->get();
        });

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
