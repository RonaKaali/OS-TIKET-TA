<x-agent-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 flex items-center">
                    <svg class="w-7 h-7 mr-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Pusat Penugasan Admin
                </h2>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 mt-1">Kelola tiket dan tentukan agen yang mengerjakan laporan</p>
            </div>
            <a href="{{ route('agent.tickets.index') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl uppercase tracking-widest shadow-lg transition-all">
                Semua Tiket
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Aktif</p>
            <p class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ $stats['open'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Menunggu Info</p>
            <p class="text-3xl font-black text-yellow-600 dark:text-yellow-400">{{ $stats['answered'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-red-200 dark:border-red-800/50 p-5 ring-1 ring-red-500/20">
            <p class="text-[10px] font-black text-red-500 uppercase tracking-widest mb-1">Belum Ditugaskan</p>
            <p class="text-3xl font-black text-red-600 dark:text-red-400">{{ $stats['unassigned'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Kritis</p>
            <p class="text-3xl font-black text-orange-600 dark:text-orange-400">{{ $stats['overdue'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Selesai</p>
            <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['closed'] }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Tiket Perlu Penugasan</h3>
            <span class="text-[10px] font-bold text-slate-500">{{ $unassignedTickets->count() }} tiket</span>
        </div>
        @if($unassignedTickets->isEmpty())
            <div class="p-10 text-center text-slate-500 dark:text-slate-400 font-bold text-sm">
                Semua tiket aktif sudah memiliki penanggung jawab.
            </div>
        @else
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach($unassignedTickets as $ticket)
                    <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors">
                        <div>
                            <div class="text-xs font-black text-emerald-600 dark:text-emerald-400 mb-1">{{ $ticket->ticket_number }}</div>
                            <div class="font-bold text-slate-900 dark:text-white">{{ $ticket->subject }}</div>
                            <div class="text-[10px] text-slate-500 mt-1 uppercase tracking-widest">
                                {{ $ticket->priority?->name ?? '—' }} · {{ $ticket->department?->name ?? 'Umum' }}
                            </div>
                        </div>
                        <a href="{{ route('agent.tickets.show', $ticket) }}"
                           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black rounded-lg uppercase tracking-widest transition-colors shrink-0">
                            Tugaskan Agen
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-agent-layout>
