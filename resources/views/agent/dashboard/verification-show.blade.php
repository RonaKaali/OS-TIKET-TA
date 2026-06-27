<x-agent-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center transition-colors">
            <div>
                <div class="flex items-center space-x-3 mb-1">
                    <span class="px-2 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 rounded text-[10px] font-black uppercase tracking-widest transition-colors animate-pulse flex items-center">
                        <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1 animate-ping"></span>
                        Menunggu Verifikasi
                    </span>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter font-mono transition-colors">{{ $ticket->ticket_number }}</h2>
                </div>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 transition-colors">{{ $ticket->subject }}</p>
            </div>
            <a href="{{ route('agent.verification.index') }}" class="text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors uppercase tracking-widest flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Daftar Verifikasi
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Surat Tugas Resmi / Official Assignment Letter -->
            <div class="bg-white dark:bg-slate-900 border-2 border-slate-300 dark:border-slate-600 rounded-3xl shadow-2xl dark:shadow-[0_0_40px_rgba(0,0,0,0.7)] overflow-hidden transition-all duration-300">
                <!-- Subtle Top Accent Bar -->
                <div class="h-1.5 bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-400 dark:from-amber-600 dark:via-amber-500 dark:to-yellow-500"></div>

                <!-- Kop Surat (Official Letterhead) -->
                <div class="p-10 pb-6">
                    <div class="flex items-start justify-between gap-6">
                        <!-- Logo -->
                        <div class="w-20 h-20 flex-shrink-0">
                            <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Pemerintah Provinsi Kalimantan Selatan" class="w-full h-full object-contain drop-shadow-md dark:brightness-110">
                        </div>
                        <!-- Instansi Info -->
                        <div class="flex-1 text-center">
                            <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Pemerintah Provinsi Kalimantan Selatan</p>
                            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white uppercase tracking-tight mt-1">Dinas Komunikasi dan Informatika</h2>
                            <h3 class="text-lg font-black text-emerald-600 dark:text-emerald-400 tracking-[0.25em] uppercase mt-1">CSIRT Kalselprov</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 font-medium mt-1.5">Jl. Dharma Praja II, Kawasan Perkantoran Pemprov Kalsel, Banjarbaru</p>
                        </div>
                        <!-- Akreditasi Seal -->
                        <div class="w-20 h-20 flex items-center justify-center rounded-2xl bg-emerald-500/10 border border-emerald-500/30 shadow-lg flex-shrink-0">
                            <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Double Border Separator -->
                    <div class="mt-6 border-t-2 border-b-2 border-double border-slate-800 dark:border-slate-300 py-2">
                        <div class="border-t border-slate-300 dark:border-slate-600"></div>
                    </div>
                
                    <!-- Document Title -->
                    <div class="text-center my-8">
                        <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-wide underline decoration-2 decoration-slate-800 dark:decoration-slate-200 underline-offset-4">Surat Tugas Penanganan Insiden Siber</h3>
                        <p class="text-sm font-bold text-slate-500 dark:text-slate-400 font-mono mt-2 tracking-wider">Nomor: ST/{{ $ticket->ticket_number }}/CSIRT/{{ $ticket->created_at->format('Y') }}</p>
                        <div class="mt-4 text-center">
                            <a href="{{ route('agent.tickets.print', $ticket) }}" target="_blank" class="inline-block px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-500 transition shadow-md font-bold text-sm">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                Cetak / Download PDF
                            </a>
                        </div>
                    </div>

                    <!-- Preamble / Pembukaan -->
                    <div class="text-base text-slate-700 dark:text-slate-300 mb-8 leading-relaxed font-medium text-justify">
                        Menimbang urgensi keamanan siber dan perlu mitigasi insiden secara cepat pada infrastruktur teknologi informasi Pemerintah Provinsi Kalimantan Selatan, Kepala Dinas Komunikasi dan Informatika menginstruksikan kepada analis berikut untuk melaksanakan penanganan insiden:
                    </div>

                    <!-- Pihak Terlibat Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 mb-8">
                        <div>
                            <h4 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4">Penerima Tugas (Analis)</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-700 pb-2">
                                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Nama</span>
                                    <span class="text-base font-extrabold text-slate-900 dark:text-white">{{ $ticket->assignee->name ?? 'Belum Ditugaskan' }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-700 pb-2">
                                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Jabatan</span>
                                    <span class="text-base font-extrabold text-slate-900 dark:text-white">Analis Siber TI</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Unit Kerja</span>
                                    <span class="text-base font-extrabold text-slate-900 dark:text-white">CSIRT Kalselprov</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4">Rincian Insiden</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-700 pb-2">
                                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Nomor Laporan</span>
                                    <span class="text-base font-mono font-extrabold text-slate-900 dark:text-white">{{ $ticket->ticket_number }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-700 pb-2">
                                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Sektor / Instansi</span>
                                    <span class="text-base font-extrabold text-slate-900 dark:text-white">{{ $ticket->department->name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Kedaruratan</span>
                                    <span class="text-base font-extrabold text-red-600 dark:text-red-400">{{ $ticket->priority?->name ?? 'Normal' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Task Description Table -->
                    <div class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden mb-8">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-100 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Deskripsi Tugas Resmi</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">Batas Waktu (SLA)</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Status Tugas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-white dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                                    <td class="px-6 py-5">
                                        <div class="text-base font-extrabold text-slate-900 dark:text-white mb-2">{{ $ticket->subject }}</div>
                                        <div class="text-sm text-slate-500 dark:text-slate-300 leading-relaxed">
                                            Melaksanakan tindakan mitigasi, pemulihan, serta forensik digital pada infrastruktur {{ $ticket->department->name }} sehubungan dengan laporan siber ini. Laporan kronologi harus diunggah lengkap melalui log aktivitas sistem.
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center whitespace-nowrap">
                                        @if($ticket->due_at)
                                            <div class="inline-block font-mono font-bold text-sm {{ $ticket->due_at->isPast() ? 'text-red-600 dark:text-red-400' : 'text-slate-700 dark:text-slate-300' }}">
                                                {{ $ticket->due_at->format('d M Y, H:i') }}
                                            </div>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-right whitespace-nowrap">
                                        <span class="inline-flex items-center px-4 py-1.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 rounded-full font-black text-xs uppercase tracking-wider animate-pulse">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Menunggu Verifikasi
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer: Sign-off + Security Badges -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <!-- Left: Verified by Kepala Bidang -->
                        <div class="flex items-center gap-3 px-4 py-3 bg-emerald-500/5 border border-emerald-500/15 rounded-2xl">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/15 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Diverifikasi Oleh</div>
                                <div class="text-sm font-extrabold text-slate-800 dark:text-slate-200">Kepala Bidang CSIRT Kalselprov</div>
                                <div class="text-[10px] font-semibold text-slate-400">Tanda tangan digital &amp; stempel resmi</div>
                            </div>
                        </div>

                        <!-- Right: QR + Signatory -->
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Penetap Tugas</p>
                                <p class="text-base font-black text-slate-800 dark:text-slate-200">Kepala CSIRT Kalselprov</p>
                                <p class="text-xs font-mono text-slate-400 dark:text-slate-500 mt-0.5">ID: ST-{{ substr($ticket->uuid, 0, 8) }}</p>
                            </div>
                            <div class="p-2 bg-white border-2 border-slate-200 dark:border-slate-700 rounded-xl shadow-lg">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(56)->generate(route('agent.tickets.show', $ticket)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Threads / Timeline -->
            <div class="space-y-6">
                <h3 class="text-sm font-black text-slate-500 uppercase tracking-[0.3em] ml-4 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                    Kronologi Laporan
                </h3>
                
                <div class="space-y-6 relative before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-200 dark:before:bg-slate-800 transition-colors">
                    @forelse($ticket->threads as $thread)
                        <div class="relative pl-12 group">
                            <!-- Timeline Dot -->
                            <div class="absolute left-0 top-1 w-10 h-10 rounded-xl border-2 {{ $thread->type === 'message' ? 'bg-blue-500/20 border-blue-500 shadow-[0_0_10px_#3b82f644]' : ($thread->type === 'reply' ? 'bg-emerald-500/20 border-emerald-500 shadow-[0_0_10px_#10b98144]' : 'bg-slate-800 border-slate-700') }} flex items-center justify-center z-10 transition-transform group-hover:scale-110">
                                @if($thread->type === 'message')
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                @elseif($thread->type === 'reply')
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                @else
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                @endif
                            </div>

                            <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm dark:shadow-xl transition-colors">
                                <div class="flex justify-between items-center mb-3">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight transition-colors">
                                            @if($thread->type === 'message')
                                                PELAPOR SIBER
                                            @elseif($thread->type === 'reply')
                                                {{ $thread->user->name ?? 'AGEN ANALIS' }}
                                            @else
                                                CATATAN INTERNAL
                                            @endif
                                        </span>
                                        <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700 transition-colors"></span>
                                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 transition-colors">{{ $thread->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    @if($thread->type === 'note')
                                        <span class="px-2 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-500 border border-amber-500/20 rounded text-[8px] font-black uppercase tracking-widest transition-colors">RESTRICTED</span>
                                    @endif
                                </div>
                                <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap transition-colors">{{ $thread->body }}</div>

                                @if($thread->attachments->count() > 0)
                                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800/50 transition-colors">
                                        <div class="flex flex-wrap gap-3">
                                            @foreach($thread->attachments as $attachment)
                                                <a href="{{ route('attachments.download', $attachment) }}" target="_blank"
                                                    class="group/file flex items-center px-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl hover:border-emerald-500/50 transition-all">
                                                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover/file:text-emerald-600 dark:group-hover/file:text-emerald-400 mr-2 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 group-hover/file:text-slate-900 dark:group-hover/file:text-white transition-colors">{{ Str::limit($attachment->original_filename ?? $attachment->filename, 20) }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="relative pl-12">
                            <div class="absolute left-0 top-1 w-10 h-10 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 flex items-center justify-center z-10">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                            </div>
                            <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm dark:shadow-xl transition-colors">
                                <p class="text-sm font-bold text-slate-400 dark:text-slate-500 italic">Belum ada kronologi laporan dari pelapor.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Actions (Kepala Bidang) -->
        <div class="space-y-8">
            <!-- Verification Action Panel -->
            <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm dark:shadow-xl transition-colors sticky top-28">
                <h3 class="text-xs font-black text-slate-900 dark:text-white mb-6 uppercase tracking-[0.2em] flex items-center transition-colors">
                    <svg class="w-4 h-4 mr-2 text-amber-600 dark:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    Verifikasi Surat Tugas
                </h3>

                <!-- Assigned Agent Info -->
                <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-black text-lg shadow-lg shrink-0">
                            {{ $ticket->assignee ? strtoupper(substr($ticket->assignee->name, 0, 1)) : '?' }}
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Analis Tujuan</p>
                            <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $ticket->assignee->name ?? 'Tidak diketahui' }}</p>
                            <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">Agent 2 — Analis Siber TI</p>
                        </div>
                    </div>
                </div>

                <!-- Ticket Info Summary -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center py-2 px-3 bg-slate-50 dark:bg-slate-900/80 rounded-xl border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Departemen</span>
                        <span class="text-xs font-extrabold text-slate-900 dark:text-white">{{ $ticket->department->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 px-3 bg-slate-50 dark:bg-slate-900/80 rounded-xl border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Prioritas</span>
                        @php
                            $priorityName = $ticket->priority?->name ?? 'Normal';
                            $pColor = match($priorityName) {
                                'Kritis' => 'text-red-600 dark:text-red-400',
                                'Tinggi' => 'text-orange-600 dark:text-orange-400',
                                'Sedang' => 'text-yellow-600 dark:text-yellow-400',
                                default => 'text-slate-600 dark:text-slate-400',
                            };
                        @endphp
                        <span class="text-xs font-extrabold {{ $pColor }}">{{ $priorityName }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 px-3 bg-slate-50 dark:bg-slate-900/80 rounded-xl border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ditugaskan</span>
                        <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $ticket->assigned_at?->diffForHumans() ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 px-3 bg-slate-50 dark:bg-slate-900/80 rounded-xl border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pelapor</span>
                        <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $ticket->requester->name ?? $ticket->reporter_name ?? '-' }}</span>
                    </div>
                </div>

                <!-- Verify Button -->
                <form method="POST" action="{{ route('agent.verification.verify', $ticket) }}"
                    onsubmit="return confirm('✅ Konfirmasi Verifikasi Surat Tugas?\n\nSurat tugas #{{ $ticket->ticket_number }} akan diverifikasi dan diteruskan ke {{ $ticket->assignee->name ?? 'Agent 2' }} untuk segera ditindaklanjuti.\n\nPastikan surat tugas sudah sesuai sebelum diverifikasi.')">
                    @csrf
                    <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white text-xs font-black uppercase tracking-[0.2em] rounded-2xl shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_30px_rgba(16,185,129,0.5)] transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Verifikasi & Teruskan ke Agent 2
                    </button>
                </form>

                <div class="mt-4 p-3 bg-amber-500/5 border border-amber-500/20 rounded-xl">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-[10px] font-bold text-amber-700 dark:text-amber-400 leading-relaxed">
                            Dengan memverifikasi, Anda menyetujui bahwa penugasan ini sah dan dapat diteruskan kepada analis untuk ditindaklanjuti.
                        </p>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-800">
                    <a href="{{ route('agent.verification.index') }}"
                        class="w-full py-3 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-700 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali ke Daftar Verifikasi
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-agent-layout>
