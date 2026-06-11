<x-agent-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 flex items-center transition-colors">
                    <svg class="w-7 h-7 mr-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    Ruang Kerja Analis
                </h2>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 mt-1 transition-colors">Kelola Tugas dan Tiket Anda</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="flex h-3 w-3 relative">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                </span>
                <span class="text-blue-600 dark:text-blue-400 text-xs font-black tracking-widest transition-colors uppercase">Status Aktif</span>
            </div>
        </div>
    </x-slot>

    <!-- Agent Stats Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Ditugaskan -->
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] hover:shadow-md dark:hover:shadow-[0_0_20px_rgba(59,130,246,0.3)] transition-all transform hover:-translate-y-1 border border-slate-200 dark:border-slate-700 overflow-hidden relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 dark:from-blue-600/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-widest transition-colors">Ditugaskan</p>
                        <p class="text-3xl font-black text-slate-900 dark:text-white transition-colors">{{ $stats['assigned'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-500/20 border border-blue-200 dark:border-blue-500/50 rounded-xl flex items-center justify-center shadow-inner transition-all">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dalam Proses -->
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] hover:shadow-md dark:hover:shadow-[0_0_20px_rgba(234,179,8,0.3)] transition-all transform hover:-translate-y-1 border border-slate-200 dark:border-slate-700 overflow-hidden relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/5 dark:from-yellow-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-widest transition-colors">Dalam Proses</p>
                        <p class="text-3xl font-black text-slate-900 dark:text-white transition-colors">{{ $stats['in_progress'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-50 dark:bg-yellow-500/20 border border-yellow-200 dark:border-yellow-500/50 rounded-xl flex items-center justify-center shadow-inner transition-all">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selesai -->
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] hover:shadow-md dark:hover:shadow-[0_0_20px_rgba(16,185,129,0.3)] transition-all transform hover:-translate-y-1 border border-slate-200 dark:border-slate-700 overflow-hidden relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 dark:from-emerald-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-widest transition-colors">Selesai</p>
                        <p class="text-3xl font-black text-slate-900 dark:text-white transition-colors">{{ $stats['completed'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/50 rounded-xl flex items-center justify-center shadow-inner transition-all">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Surat Tugas Baru -->
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] hover:shadow-md dark:hover:shadow-[0_0_20px_rgba(239,68,68,0.3)] transition-all transform hover:-translate-y-1 border border-slate-200 dark:border-slate-700 overflow-hidden relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 dark:from-red-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-widest transition-colors">Tugas Baru</p>
                        <p class="text-3xl font-black text-slate-900 dark:text-white transition-colors" id="new-assignments-count">0</p>
                    </div>
                    <div class="w-12 h-12 bg-red-50 dark:bg-red-500/20 border border-red-200 dark:border-red-500/50 rounded-xl flex items-center justify-center shadow-inner transition-all animate-pulse">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content: Assigned Tickets -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-slate-700 overflow-hidden transition-colors">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900/50 flex justify-between items-center transition-colors">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center tracking-widest transition-colors uppercase">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        Tiket Tertugaskan
                    </h3>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase">{{ $tickets->count() }} item</span>
                </div>
                <div id="tickets-container" class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @forelse($tickets as $ticket)
                    <div class="p-6 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors cursor-pointer group" onclick="window.location.href='{{ route('agent.tickets.show', $ticket->ticket_number) }}'">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="text-sm font-black text-slate-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $ticket->subject }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Tiket #{{ $ticket->ticket_number }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                                {{ $ticket->status->slug === 'open' ? 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400' : '' }}
                                {{ $ticket->status->slug === 'in_progress' ? 'bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400' : '' }}
                                {{ $ticket->status->slug === 'closed' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400' : '' }}
                            ">
                                @switch($ticket->status->slug)
                                    @case('open')
                                        Terbuka
                                        @break
                                    @case('in_progress')
                                        Proses
                                        @break
                                    @case('closed')
                                        Selesai
                                        @break
                                    @default
                                        {{ $ticket->status->name }}
                                @endswitch
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mb-3 line-clamp-2">{{ $ticket->subject }}</p>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500">
                            <span>Dibuat: {{ $ticket->created_at->format('d M Y') }}</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center">
                        <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-sm font-black text-slate-600 dark:text-slate-400">Belum ada tiket tertugaskan</p>
                        <p class="text-xs text-slate-500 dark:text-slate-500 mt-1">Tunggu tugas baru dari admin</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar: Quick Info -->
        <div class="space-y-6">
            <!-- Activity Summary -->
            <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-slate-700 p-6 transition-colors">
                <h3 class="text-sm font-black text-slate-900 dark:text-white mb-4 uppercase tracking-widest">Ringkasan Aktivitas</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-900/50 transition-colors">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Efisiensi Penyelesaian</span>
                        <span class="text-sm font-black text-blue-600 dark:text-blue-400">{{ isset($stats['completed']) && isset($stats['assigned']) && $stats['assigned'] > 0 ? round(($stats['completed'] / $stats['assigned']) * 100) : 0 }}%</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-900/50 transition-colors">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Perlu Ditindak</span>
                        <span class="text-sm font-black text-yellow-600 dark:text-yellow-400">{{ $stats['in_progress'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-900/50 transition-colors">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Total Selesai</span>
                        <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ $stats['completed'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30 rounded-2xl shadow-sm border border-blue-200 dark:border-blue-500/30 p-6 transition-colors">
                <h3 class="text-sm font-black text-blue-900 dark:text-blue-300 mb-3 flex items-center uppercase tracking-widest">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Panduan Cepat
                </h3>
                <ul class="text-xs text-blue-800 dark:text-blue-200 space-y-2">
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Klik tiket untuk membuka detail dan mengerjakan</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Perbarui status saat Anda bekerja pada tiket</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2">•</span>
                        <span>Notifikasi akan muncul untuk tugas baru</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        async function checkPendingAssignments() {
            try {
                const response = await fetch('{{ route("agent.assignments.pending") }}');
                const data = await response.json();
                if (data.length > 0) {
                    document.getElementById('new-assignments-count').textContent = data.length;
                }
            } catch (error) {
                console.log('Error checking assignments:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', checkPendingAssignments);
        setInterval(checkPendingAssignments, 20000);
    </script>
</x-agent-layout>