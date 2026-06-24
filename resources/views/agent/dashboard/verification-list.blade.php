<x-agent-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight flex items-center">
                    <svg class="w-8 h-8 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Verifikasi Surat Tugas
                </h2>
                <p class="mt-2 text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                    Daftar penugasan yang menunggu persetujuan Kepala Bidang
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if($tickets->isEmpty())
            <div class="bg-white/50 dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-12 text-center shadow-lg">
                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-wider mb-2">Semua Tuntas</h3>
                <p class="text-slate-500 dark:text-slate-400">Tidak ada surat tugas yang perlu diverifikasi saat ini.</p>
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                                <th class="py-4 px-6 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">No. Tiket</th>
                                <th class="py-4 px-6 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Subjek</th>
                                <th class="py-4 px-6 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Agen Tujuan</th>
                                <th class="py-4 px-6 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Waktu Tugas</th>
                                <th class="py-4 px-6 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($tickets as $ticket)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/25 transition-colors group">
                                    <td class="py-4 px-6">
                                        <a href="{{ route('agent.tickets.show', $ticket) }}" class="text-sm font-black text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                                            #{{ $ticket->ticket_number }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $ticket->subject }}</div>
                                        <div class="text-xs text-slate-500 mt-1">{{ $ticket->department->name ?? '-' }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm font-bold text-slate-900 dark:text-white">
                                            {{ \App\Models\User::find($ticket->assigned_to)?->name ?? 'Unknown' }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm text-slate-600 dark:text-slate-400">
                                            {{ $ticket->assigned_at?->diffForHumans() ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <form action="{{ route('agent.verification.verify', $ticket) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menyetujui surat tugas ini? Agen akan segera menerima notifikasi.');">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                Verifikasi & Teruskan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($tickets->hasPages())
                    <div class="p-6 border-t border-slate-200 dark:border-slate-800">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-agent-layout>
