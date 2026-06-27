<x-agent-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Verifikasi Surat Tugas
                </h2>
                <p class="mt-1 text-sm font-bold text-slate-500 dark:text-slate-400">
                    Daftar penugasan yang menunggu persetujuan Kepala Bidang
                </p>
            </div>
            @if($tickets->isNotEmpty())
                <div class="px-4 py-2 bg-amber-500/10 border border-amber-500/30 text-amber-700 dark:text-amber-400 text-xs font-black rounded-xl uppercase tracking-widest">
                    {{ $tickets->total() }} surat tugas perlu diverifikasi
                </div>
            @endif
        </div>
    </x-slot>

    @if($tickets->isEmpty())
        <div class="bg-white/50 dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-16 text-center shadow-lg">
            <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-wider mb-2">Semua Tuntas</h3>
            <p class="text-slate-500 dark:text-slate-400">Tidak ada surat tugas yang perlu diverifikasi saat ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="tour-verification-cards">
            @foreach($tickets as $ticket)
                @php
                    $priority = $ticket->priority?->name ?? '—';
                    $priorityColors = match($priority) {
                        'Kritis' => ['bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20', 'from-red-500 to-red-400'],
                        'Tinggi' => ['bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-500/20', 'from-orange-500 to-orange-400'],
                        'Sedang' => ['bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 border-yellow-500/20', 'from-yellow-500 to-yellow-400'],
                        default => ['bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20', 'from-slate-400 to-slate-300'],
                    };
                    $agent = \App\Models\User::find($ticket->assigned_to);
                @endphp
                <a href="{{ route('agent.verification.show', $ticket) }}"
                   class="group relative bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl p-5 hover:border-emerald-500/30 dark:hover:border-emerald-500/30 transition-all duration-300 hover:shadow-lg dark:hover:shadow-[0_0_20px_rgba(16,185,129,0.1)] hover:-translate-y-1 overflow-hidden cursor-pointer block no-underline">
                    <!-- Hover glow effect -->
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <!-- Top accent line -->
                    <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r {{ $priorityColors[1] }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div class="relative z-10">
                        <!-- Header: ticket number + status badge -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md">{{ $ticket->ticket_number }}</span>
                            <span class="px-2 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 rounded text-[8px] font-black uppercase tracking-wider animate-pulse flex items-center">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1 animate-ping"></span>
                                MENUNGGU VERIFIKASI
                            </span>
                        </div>

                        <!-- Subject -->
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm leading-snug mb-3 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors line-clamp-2">{{ $ticket->subject }}</h4>

                        <!-- Meta: Priority & Department -->
                        <div class="flex items-center flex-wrap gap-1.5 mb-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black {{ $priorityColors[0] }}">
                                {{ $priority }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20">
                                {{ $ticket->department->name ?? '-' }}
                            </span>
                        </div>

                        <!-- Agent Tujuan -->
                        <div class="flex items-center gap-2 mb-4 py-2.5 px-3 bg-slate-50 dark:bg-slate-900/80 rounded-xl border border-slate-200 dark:border-slate-700/50">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-black text-xs shadow-lg shrink-0">
                                {{ $agent ? strtoupper(substr($agent->name, 0, 1)) : '?' }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Tujuan Agen</p>
                                <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $agent?->name ?? 'Tidak diketahui' }}</p>
                            </div>
                        </div>

                        <!-- Time -->
                        <div class="flex items-center text-[10px] text-slate-500 dark:text-slate-400 font-bold mb-4">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Ditugaskan {{ $ticket->assigned_at?->diffForHumans() ?? '-' }}
                        </div>

                        <!-- Action -->
                        <div class="flex items-center text-[10px] font-black text-slate-500 dark:text-slate-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 uppercase tracking-widest transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Lihat & Verifikasi Surat Tugas
                            <svg class="w-3 h-3 ml-auto group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($tickets->hasPages())
            <div class="mt-8">
                {{ $tickets->links() }}
            </div>
        @endif
    @endif
</x-agent-layout>
