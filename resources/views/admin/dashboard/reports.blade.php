<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight transition-colors">
                    Dasbor <span class="text-emerald-600 dark:text-emerald-400 transition-colors">Laporan Akhir</span>
                </h2>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest transition-colors">Performance & Incident Summary</p>
            </div>
            <div class="flex items-center space-x-3">
                <button id="exportExcel" class="px-6 py-3 bg-emerald-600 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-emerald-500 transition-all shadow-[0_0_15px_rgba(16,185,129,0.2)] flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Ekspor Excel
                </button>
                <button onclick="window.print()" class="px-6 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak Laporan
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Total Insiden</span>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['total'] }}</div>
                <div class="text-[10px] font-bold text-slate-500 mt-1">Laporan Masuk</div>
            </div>

            <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Selesai</span>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['completed'] }}</div>
                <div class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 mt-1 uppercase">Resolusi Berhasil</div>
            </div>

            <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Dalam Proses</span>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['ongoing'] }}</div>
                <div class="text-[10px] font-bold text-amber-600 dark:text-amber-400 mt-1 uppercase">Sedang Ditangani</div>
            </div>

            <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-500/10 rounded-2xl flex items-center justify-center text-red-600 dark:text-red-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Kritis</span>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['critical'] }}</div>
                <div class="text-[10px] font-bold text-red-600 dark:text-red-400 mt-1 uppercase">Butuh Atensi Segera</div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Completion Rate -->
            <div class="lg:col-span-1 bg-white dark:bg-slate-900/50 backdrop-blur-xl p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-8">Resolution Rate</h3>
                <div class="flex flex-col items-center justify-center py-10">
                    <div class="relative w-48 h-48">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="96" cy="96" r="80" stroke="currentColor" stroke-width="12" fill="transparent" class="text-slate-100 dark:text-slate-800" />
                            <circle cx="96" cy="96" r="80" stroke="currentColor" stroke-width="12" fill="transparent" stroke-dasharray="502.6" stroke-dashoffset="{{ 502.6 - (502.6 * $completionRate / 100) }}" class="text-emerald-500 transition-all duration-1000 ease-out" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-4xl font-black text-slate-900 dark:text-white">{{ number_format($completionRate, 1) }}%</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Resolved</span>
                        </div>
                    </div>
                </div>
                <div class="mt-8 space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500 font-bold uppercase">Selesai</span>
                        <span class="text-slate-900 dark:text-white font-black">{{ $stats['completed'] }}</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5">
                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $completionRate }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Trend Chart -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-900/50 backdrop-blur-xl p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Tren Insiden Tahunan ({{ date('Y') }})</h3>
                    <span class="text-[10px] font-bold px-2 py-1 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg uppercase">Live Data</span>
                </div>
                <div class="h-64 flex items-end justify-between space-x-2">
                    @php
                        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                        $max = $monthlyStats->max('count') ?: 10;
                    @endphp
                    @foreach($months as $idx => $m)
                        @php
                            $count = $monthlyStats->firstWhere('month', $idx + 1)->count ?? 0;
                            $height = ($count / $max) * 100;
                        @endphp
                        <div class="flex-1 flex flex-col items-center group">
                            <div class="w-full bg-blue-500/10 group-hover:bg-blue-500/20 rounded-t-lg transition-all relative" style="height: {{ $height }}%">
                                @if($count > 0)
                                    <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 text-[10px] font-black text-blue-600 dark:text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity">{{ $count }}</div>
                                @endif
                            </div>
                            <span class="text-[10px] font-bold text-slate-500 mt-4 uppercase">{{ $m }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Summary Table -->
        <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Rincian Status Strategis</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950/50 text-[10px] font-black text-slate-500 uppercase tracking-widest">
                            <th class="px-6 py-4">Kategori Laporan</th>
                            <th class="px-6 py-4">Volume</th>
                            <th class="px-6 py-4">Prioritas</th>
                            <th class="px-6 py-4">Status Target</th>
                            <th class="px-6 py-4">Progres</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-bold text-sm text-slate-900 dark:text-white">Laporan Selesai</div>
                                <div class="text-[10px] text-slate-500 uppercase">Resolved Cases</div>
                            </td>
                            <td class="px-6 py-4 text-sm font-black text-slate-900 dark:text-white">{{ $stats['completed'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold rounded-lg uppercase">Optimal</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-500">100% Resolved</td>
                            <td class="px-6 py-4">
                                <div class="w-24 bg-slate-100 dark:bg-slate-800 h-1 rounded-full">
                                    <div class="bg-emerald-500 h-1 rounded-full w-full"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-bold text-sm text-slate-900 dark:text-white">Laporan Berjalan</div>
                                <div class="text-[10px] text-slate-500 uppercase">Active Investigations</div>
                            </td>
                            <td class="px-6 py-4 text-sm font-black text-slate-900 dark:text-white">{{ $stats['ongoing'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-bold rounded-lg uppercase">High Priority</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-500">SLA Active</td>
                            <td class="px-6 py-4">
                                <div class="w-24 bg-slate-100 dark:bg-slate-800 h-1 rounded-full">
                                    <div class="bg-amber-500 h-1 rounded-full" style="width: 65%"></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('exportExcel').addEventListener('click', function() {
            const table = document.querySelector('table');
            const rows = Array.from(table.rows);
            const csvContent = rows.map(row => {
                const cells = Array.from(row.cells).map(cell => {
                    // Clean up cell text for CSV
                    let text = cell.innerText.replace(/\n/g, ' ').replace(/"/g, '""').trim();
                    return `"${text}"`;
                });
                return cells.join(',');
            }).join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'Laporan_Akhir_CSIRT_{{ date("Y-m-d") }}.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
</x-admin-layout>
