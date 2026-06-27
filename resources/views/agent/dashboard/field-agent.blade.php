<x-agent-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 flex items-center">
                    <svg class="w-7 h-7 mr-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    {{ \App\Support\RoleUi::portalLabel(auth()->user()) }}
                </h2>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 mt-1">{{ auth()->user()->hasRole(\App\Support\RoleUi::SUPPORT_AGENT) ? 'Persetujuan dan verifikasi surat tugas agen' : 'Surat tugas dan tiket yang ditugaskan kepada Anda' }}</p>
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

    @php
        $isKepalaBidang = auth()->user()->hasRole(\App\Support\RoleUi::SUPPORT_AGENT);
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8" id="tour-agent-stats">
        <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">{{ $isKepalaBidang ? 'Menunggu Verifikasi' : 'Ditugaskan' }}</p>
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
            <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">{{ $isKepalaBidang ? 'Perlu Verifikasi' : 'Surat Tugas Baru' }}</p>
            <p class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ $stats['pending_ack'] }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)]" id="tour-agent-tasks">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-emerald-500/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">{{ $isKepalaBidang ? 'Surat Tugas Menunggu Verifikasi' : 'Tugas Aktif Saya' }}</h3>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold mt-0.5 tracking-wide">{{ $myTickets->count() }} {{ $isKepalaBidang ? 'tiket menunggu verifikasi' : 'tiket ditugaskan' }}</p>
                </div>
            </div>
            @if($myTickets->isNotEmpty())
                <a href="{{ $isKepalaBidang ? route('agent.verification.index') : route('agent.tickets.index') }}" class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 uppercase tracking-widest transition-colors flex items-center">
                    Lihat Semua
                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            @endif
        </div>
        @if($myTickets->isEmpty())
            <div class="p-16 text-center">
                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-900/50 rounded-3xl flex items-center justify-center mx-auto mb-5 border border-slate-200 dark:border-slate-700/50">
                    <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <p class="text-sm font-bold text-slate-400 dark:text-slate-500 mb-2">{{ $isKepalaBidang ? 'Tidak ada tiket menunggu verifikasi' : 'Belum ada tiket ditugaskan' }}</p>
                <p class="text-[10px] text-slate-400 dark:text-slate-600 font-bold tracking-wide">{{ $isKepalaBidang ? 'Semua penugasan surat tugas telah diverifikasi' : 'Admin akan mengirim surat tugas melalui sistem penugasan' }}</p>
            </div>
        @else
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="tour-agent-task-cards">
                    @foreach($myTickets as $ticket)
                        @php
                            $isAck = \App\Support\AssignmentAcknowledgment::isAcknowledged($ticket, \App\Support\AssignmentAcknowledgment::map(request()));
                            $priority = $ticket->priority?->name ?? '—';
                            $priorityColors = match($priority) {
                                'Kritis' => ['bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20', 'bg-red-500/10', 'ring-red-500/20'],
                                'Tinggi' => ['bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-500/20', 'bg-orange-500/10', 'ring-orange-500/20'],
                                'Sedang' => ['bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 border-yellow-500/20', 'bg-yellow-500/10', 'ring-yellow-500/20'],
                                default => ['bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20', 'bg-slate-500/10', 'ring-slate-500/20'],
                            };
                            $statusColors = match($ticket->status?->name) {
                                'Ditugaskan' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
                                'Dikerjakan' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
                                'Selesai' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                                default => 'bg-slate-500/10 text-slate-600 dark:text-slate-400',
                            };
                            $accentColor = match($priority) {
                                'Kritis' => 'from-red-500 to-red-400',
                                'Tinggi' => 'from-orange-500 to-orange-400',
                                'Sedang' => 'from-yellow-500 to-yellow-400',
                                default => 'from-slate-400 to-slate-300',
                            };
                        @endphp
                        <a href="{{ $isKepalaBidang ? route('agent.verification.show', $ticket) : route('agent.tickets.show', $ticket) }}"
                           class="group relative bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl p-5 hover:border-emerald-500/30 dark:hover:border-emerald-500/30 transition-all duration-300 hover:shadow-lg dark:hover:shadow-[0_0_20px_rgba(16,185,129,0.1)] hover:-translate-y-1 overflow-hidden cursor-pointer block no-underline">
                            <!-- Hover glow effect -->
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <!-- Top accent line -->
                            <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r {{ $accentColor }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <div class="relative z-10">
                                <!-- Header: ticket number + badges -->
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md">{{ $ticket->ticket_number }}</span>
                                    @if(!$isAck)
                                        <span class="px-2 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 rounded text-[8px] font-black uppercase tracking-wider animate-pulse flex items-center">
                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1 animate-ping"></span>
                                            BARU
                                        </span>
                                    @endif
                                </div>

                                <!-- Subject -->
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm leading-snug mb-3 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors line-clamp-2">{{ $ticket->subject }}</h4>

                                <!-- Meta tags -->
                                <div class="flex items-center flex-wrap gap-1.5 mb-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold {{ $statusColors }} border border-transparent">
                                        {{ $ticket->status?->name }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black {{ $priorityColors[0] }} border {{ $priorityColors[1] }}">
                                        {{ $priority }}
                                    </span>
                                </div>

                                <!-- Action -->
                                <div class="flex items-center text-[10px] font-black text-slate-500 dark:text-slate-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 uppercase tracking-widest transition-colors">
                                    @if($isKepalaBidang)
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        Verifikasi Surat Tugas
                                    @elseif(!$isAck)
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        Lihat Surat Tugas
                                    @else
                                        Buka Tiket
                                    @endif
                                    <svg class="w-3 h-3 ml-auto group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-agent-layout>
