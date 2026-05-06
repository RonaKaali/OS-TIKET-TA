<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight transition-colors">
                    Dasbor <span class="text-blue-600 dark:text-blue-500 transition-colors">Laporan Akhir</span>
                </h2>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest transition-colors">Periode: {{ date('F Y') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <button id="exportExcelMain" class="px-6 py-3 bg-emerald-600 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-emerald-500 transition-all shadow-[0_0_15px_rgba(16,185,129,0.2)] flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Ekspor Excel
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Laporan -->
            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</span>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['total'] }}</div>
                <div class="text-xs font-bold text-slate-500 mt-1 uppercase">Keseluruhan Tiket</div>
            </div>

            <!-- Sedang Dijalankan -->
            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aktif</span>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['in_progress'] }}</div>
                <div class="text-xs font-bold text-yellow-600 dark:text-yellow-400 mt-1 uppercase">Sedang Dikerjakan</div>
            </div>

            <!-- Laporan Baru (Open) -->
            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Baru</span>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['open'] }}</div>
                <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-1 uppercase">Menunggu Respon</div>
            </div>

            <!-- Telah Dikerjakan -->
            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Selesai</span>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['resolved'] }}</div>
                <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mt-1 uppercase">Telah Ditangani</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Distribution by Department -->
            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <h3 class="text-lg font-black text-slate-800 dark:text-white mb-6 uppercase tracking-tight">Distribusi Departemen</h3>
                <div class="space-y-4">
                    @foreach($departments as $dept)
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-slate-600 dark:text-slate-400 uppercase">{{ $dept->name }}</span>
                                <span class="text-slate-900 dark:text-white">{{ $dept->tickets_count }}</span>
                            </div>
                            <div class="h-2 w-full bg-slate-100 dark:bg-slate-900 rounded-full overflow-hidden">
                                @php
                                    $percent = $stats['total'] > 0 ? ($dept->tickets_count / $stats['total']) * 100 : 0;
                                @endphp
                                <div class="h-full bg-blue-500" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Performance Trend (Placeholder Chart) -->
            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <h3 class="text-lg font-black text-slate-800 dark:text-white mb-6 uppercase tracking-tight">Tren Penanganan</h3>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="performanceTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('performanceTrendChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Laporan Selesai',
                        data: [12, 19, 3, 5, 2, 3],
                        backgroundColor: '#3b82f6',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { font: { size: 10 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.getElementById('exportExcelMain').addEventListener('click', function() {
            // Generate simple CSV from stats
            const data = [
                ['Kategori', 'Jumlah'],
                ['Total Tiket', '{{ $stats["total"] }}'],
                ['Sedang Dikerjakan', '{{ $stats["in_progress"] }}'],
                ['Menunggu Respon', '{{ $stats["open"] }}'],
                ['Telah Ditangani', '{{ $stats["resolved"] }}']
            ];
            
            const csvContent = data.map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'Laporan_Ringkasan_CSIRT_{{ date("Y-m-d") }}.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
</x-admin-layout>
