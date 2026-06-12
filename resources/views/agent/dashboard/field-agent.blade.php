<x-agent-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 flex items-center">
                    <svg class="w-7 h-7 mr-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    {{ \App\Support\RoleUi::portalLabel(auth()->user()) }}
                </h2>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 mt-1">Surat tugas dan tiket yang ditugaskan kepada Anda</p>
            </div>
            @if($stats['pending_ack'] > 0)
                <div class="px-4 py-2 bg-amber-500/10 border border-amber-500/30 text-amber-700 dark:text-amber-400 text-xs font-black rounded-xl uppercase tracking-widest">
                    {{ $stats['pending_ack'] }} surat tugas baru — konfirmasi popup
                </div>
            @elseif($myTickets->isNotEmpty())
                <a href="{{ route('agent.tickets.index') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-xl uppercase tracking-widest shadow-lg transition-all">
                    Buka Tiket Saya
                </a>
            @endif
        </div>
    </x-slot>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Ditugaskan</p>
            <p class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ $stats['assigned'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Dalam Proses</p>
            <p class="text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Selesai</p>
            <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['closed'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-amber-200 dark:border-amber-800/50 p-5">
            <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">Surat Tugas Baru</p>
            <p class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ $stats['pending_ack'] }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Tugas Aktif Saya</h3>
        </div>
        @if($myTickets->isEmpty())
            <div class="p-10 text-center text-slate-500 dark:text-slate-400 font-bold text-sm">
                Belum ada tiket ditugaskan. Admin akan mengirim surat tugas melalui sistem penugasan.
            </div>
        @else
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach($myTickets as $ticket)
                    @php
                        $isAck = \App\Support\AssignmentAcknowledgment::isAcknowledged($ticket, \App\Support\AssignmentAcknowledgment::map(request()));
                    @endphp
                    <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:bg-slate-50 dark:hover:bg-slate-900/30 transition-all duration-200">
                        <div>
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-xs font-mono font-black text-emerald-600 dark:text-emerald-400">{{ $ticket->ticket_number }}</span>
                                @if(!$isAck)
                                    <span class="px-2 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 rounded text-[9px] font-black uppercase tracking-wider animate-pulse">Baru</span>
                                @endif
                            </div>
                            <div class="font-bold text-slate-900 dark:text-white text-sm tracking-tight">{{ $ticket->subject }}</div>
                            <div class="text-[10px] text-slate-500 mt-1 uppercase tracking-widest flex items-center space-x-2">
                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-900/50 rounded font-bold">{{ $ticket->status?->name }}</span>
                                <span>·</span>
                                <span class="font-bold {{ $ticket->priority?->name === 'Tinggi' ? 'text-red-500' : 'text-slate-400' }}">{{ $ticket->priority?->name ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 shrink-0">
                            @if(!$isAck)
                                <a href="{{ route('agent.tickets.show', $ticket) }}"
                                   class="inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-[10px] font-black rounded-xl uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(245,158,11,0.3)] hover:shadow-[0_0_20px_rgba(245,158,11,0.5)] transform hover:-translate-y-0.5">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    Lihat Surat Tugas
                                </a>
                            @else
                                <a href="{{ route('agent.tickets.show', $ticket) }}"
                                   class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-[10px] font-black rounded-xl uppercase tracking-widest transition-all border border-slate-200 dark:border-slate-700">
                                    Buka Tiket
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-agent-layout>
