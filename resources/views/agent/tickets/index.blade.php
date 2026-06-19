<x-agent-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center transition-colors">
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight transition-colors">DAFTAR <span class="text-emerald-600 dark:text-emerald-500 transition-colors">LAPORAN INSIDEN</span></h2>
            <a href="{{ route('agent.dashboard') }}" class="text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors uppercase tracking-widest flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-2xl rounded-2xl mb-8 p-6 relative overflow-hidden group transition-colors" id="tour-ticket-filters">
        <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500/50 group-hover:bg-emerald-500 transition-colors"></div>
        <form method="GET" action="{{ route('agent.tickets.index') }}"
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 ml-1">Filter Status</label>
                <select name="status" id="tour-filter-status"
                    class="block w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none cursor-pointer">
                    <option value="" class="bg-white dark:bg-slate-900">Semua Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }} class="bg-white dark:bg-slate-900">Terbuka</option>
                    <option value="answered" {{ request('status') == 'answered' ? 'selected' : '' }} class="bg-white dark:bg-slate-900">Menunggu Pelapor</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }} class="bg-white dark:bg-slate-900">Ditugaskan</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }} class="bg-white dark:bg-slate-900">Tertutup</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 ml-1">Pencarian Cepat</label>
                <div class="relative group/input">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 group-focus-within/input:text-emerald-600 dark:group-focus-within/input:text-emerald-500 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" id="tour-filter-search"
                        placeholder="No. laporan, subjek, email..."
                        class="block w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none">
                </div>
            </div>

            <div class="flex items-end space-x-3">
                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-emerald-600 to-blue-600 hover:from-emerald-500 hover:to-blue-500 text-white text-xs font-black uppercase tracking-widest px-4 py-3 rounded-xl shadow-[0_0_15px_rgba(16,185,129,0.2)] hover:shadow-[0_0_25px_rgba(16,185,129,0.4)] transition-all transform hover:-translate-y-0.5">
                    Saring
                </button>
                <a href="{{ route('agent.tickets.index') }}"
                    class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:border-slate-400 dark:hover:border-slate-500 px-4 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-2xl rounded-2xl overflow-hidden relative transition-colors" id="tour-ticket-table">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 transition-colors">
                <thead class="bg-slate-50 dark:bg-slate-950/50 transition-colors">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">No. Laporan</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Subjek & Departemen</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Pelapor</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Status Kontrol</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Prioritas</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Timestamp</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 transition-colors">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm font-black text-slate-900 dark:text-white font-mono tracking-tighter transition-colors">{{ $ticket->ticket_number }}</div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ Str::limit($ticket->subject, 50) }}</div>
                                <div class="text-[10px] font-bold text-slate-500 dark:text-slate-500 uppercase tracking-widest mt-1 transition-colors">{{ $ticket->department->name }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm font-bold text-slate-700 dark:text-slate-300 transition-colors">
                                    {{ $ticket->requester->name ?? $ticket->reporter_name ?? $ticket->reporter_email ?? 'N/A' }}
                                </div>
                                <div class="text-[10px] font-medium text-slate-400 dark:text-slate-500 lowercase transition-colors">{{ $ticket->reporter_email }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex flex-col space-y-1.5">
                                    @php
                                        $statusColors = [
                                            'open' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                                            'answered' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                                            'closed' => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700',
                                            'overdue' => 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20'
                                        ];
                                        $colorClass = $statusColors[$ticket->status->slug] ?? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $colorClass }}">
                                        {{ $ticket->status->name }}
                                    </span>
                                    @if($ticket->isOverdue())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20 animate-pulse">
                                            ⚠️ Terlambat ({{ \App\Models\Ticket::countWorkingDays($ticket->due_at) }}h)
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="text-xs font-bold text-slate-400">{{ $ticket->priority?->name ?? 'Normal' }}</span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-xs font-bold text-slate-500">{{ $ticket->created_at->format('d/m/Y') }}</div>
                                <div class="text-[10px] font-medium text-slate-600">{{ $ticket->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right">
                                <a href="{{ route('agent.tickets.show', $ticket) }}"
                                    class="inline-flex items-center px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-black text-slate-500 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/50 transition-all">
                                    LIHAT
                                    <svg class="w-3 h-3 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                    <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">Tidak ada laporan ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tickets->hasPages())
            <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 border-t border-slate-200 dark:border-slate-800 transition-colors">
                <div class="custom-pagination" id="tour-pagination">
                    {{ $tickets->links() }}
                </div>
            </div>
        @endif
    </div>
</x-agent-layout>