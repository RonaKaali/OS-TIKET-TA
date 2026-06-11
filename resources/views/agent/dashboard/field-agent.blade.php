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
            <a href="{{ route('agent.tickets.index') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-xl uppercase tracking-widest shadow-lg transition-all">
                Buka Tiket Saya
            </a>
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
                    <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="text-xs font-black text-emerald-600 dark:text-emerald-400 mb-1">{{ $ticket->ticket_number }}</div>
                            <div class="font-bold text-slate-900 dark:text-white">{{ $ticket->subject }}</div>
                            <div class="text-[10px] text-slate-500 mt-1 uppercase tracking-widest">
                                {{ $ticket->status?->name }} · {{ $ticket->priority?->name ?? '—' }}
                            </div>
                        </div>
                        <a href="{{ route('agent.tickets.show', $ticket) }}"
                           class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black rounded-lg uppercase tracking-widest transition-colors shrink-0">
                            Kerjakan
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-agent-layout>
