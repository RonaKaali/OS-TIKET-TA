<x-agent-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center transition-colors">
            <div>
                <div class="flex items-center space-x-3 mb-1">
                    <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded text-[10px] font-black uppercase tracking-widest transition-colors">Live Ticket</span>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter font-mono transition-colors">{{ $ticket->ticket_number }}</h2>
                </div>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 transition-colors">{{ $ticket->subject }}</p>
            </div>
            <a href="{{ route('agent.tickets.index') }}" class="text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors uppercase tracking-widest flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Surat Tugas Resmi / Official Assignment Letter -->
            <div class="bg-white dark:bg-slate-900 border-2 border-slate-300 dark:border-slate-600 rounded-3xl shadow-2xl dark:shadow-[0_0_40px_rgba(0,0,0,0.7)] overflow-hidden transition-all duration-300">
                <!-- Subtle Top Accent Bar -->
                <div class="h-1.5 bg-gradient-to-r from-emerald-500 via-emerald-400 to-cyan-400 dark:from-emerald-600 dark:via-emerald-500 dark:to-cyan-500"></div>

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
                            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white uppercase tracking-tight mt-1">Dinas Komunikasi, Informatika, dan Statistik</h2>
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
                            <a href="{{ route('agent.tickets.show', $ticket) }}" target="_blank" class="inline-block px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-500 transition shadow-md font-bold text-sm">Lihat Selengkapnya</a>
                        </div>
                    </div>

                    <!-- Preamble / Pembukaan -->
                    <div class="text-base text-slate-700 dark:text-slate-300 mb-8 leading-relaxed font-medium text-justify">
                        Menimbang urgensi keamanan siber dan perlu mitigasi insiden secara cepat pada infrastruktur teknologi informasi Pemerintah Provinsi Kalimantan Selatan, Kepala Dinas Komunikasi, Informatika, dan Statistik menginstruksikan kepada analis berikut untuk melaksanakan penanganan insiden:
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
                                        @if($ticket->acknowledged_at)
                                            <span class="inline-flex items-center px-4 py-1.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-full font-black text-xs uppercase tracking-wider">
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                                Diterima / Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-4 py-1.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 rounded-full font-black text-xs uppercase tracking-wider animate-pulse">
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                Tugas Baru
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer: Sign-off + Security Badges -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <!-- Left: Badge -->
                        <div class="flex items-center gap-3 px-4 py-3 bg-emerald-500/5 border border-emerald-500/15 rounded-2xl">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/15 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Zero Trust Verified</div>
                                <div class="text-xs font-semibold text-slate-400">Secured &amp; Digitally Signed</div>
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
                    Kronologi Interaksi
                </h3>
                
                <div class="space-y-6 relative before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-200 dark:before:bg-slate-800 transition-colors">
                    @foreach($ticket->threads as $thread)
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
                    @endforeach
                </div>
            </div>

            <!-- Reply Form Card -->
            <div class="bg-white dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-8 shadow-sm dark:shadow-2xl relative overflow-hidden transition-colors">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-blue-500"></div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white mb-6 uppercase tracking-tight flex items-center transition-colors">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mr-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                    Kirim Respon Analisis
                </h3>
                <form method="POST" action="{{ route('agent.tickets.reply', $ticket) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Pesan Tanggapan</label>
                            @if($cannedResponses->count() > 0)
                                <div class="relative">
                                    <button type="button" id="selectTemplateBtn"
                                        class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] font-black text-emerald-600 dark:text-emerald-400 hover:text-slate-900 dark:hover:text-white hover:border-emerald-500/50 transition-all flex items-center">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                        GUNAKAN TEMPLATE
                                    </button>
                                    <div id="templateDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden transition-colors">
                                        <div class="p-4 border-b border-slate-100 dark:border-slate-800 transition-colors">
                                            <input type="text" id="templateSearch" placeholder="Cari template..."
                                                class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-700 focus:border-emerald-500 outline-none transition-all">
                                        </div>
                                        <div id="templateList" class="max-h-64 overflow-y-auto p-2 space-y-1">
                                            @foreach($cannedResponses as $template)
                                                <button type="button" 
                                                    class="w-full text-left p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors group/item template-item border border-transparent hover:border-slate-200 dark:hover:border-slate-700"
                                                    data-title="{{ json_encode($template->title) }}"
                                                    data-body="{{ json_encode($template->body) }}">
                                                    <div class="text-xs font-black text-slate-700 dark:text-slate-200 group-hover/item:text-emerald-600 dark:group-hover/item:text-emerald-400 mb-1 transition-colors">{{ $template->title }}</div>
                                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 line-clamp-1 italic transition-colors">{{ Str::limit($template->body, 60) }}</div>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <textarea name="message" id="messageTextarea" rows="6" required
                            class="block w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-2xl text-slate-700 dark:text-slate-200 text-sm font-medium p-5 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none placeholder-slate-300 dark:placeholder-slate-800"
                            placeholder="Tulis pesan respon formal kepada pelapor..."></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Lampiran Pendukung</label>
                            <input type="file" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx"
                                class="block w-full text-[10px] text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-600 dark:file:text-slate-300 hover:file:bg-slate-200 dark:hover:file:bg-slate-700 cursor-pointer transition-all">
                        </div>
                        <div class="flex items-center justify-end">
                            <button type="submit"
                                class="w-full md:w-auto px-8 py-3.5 bg-gradient-to-r from-emerald-600 to-blue-600 hover:from-emerald-500 hover:to-blue-500 text-white text-xs font-black uppercase tracking-[0.2em] rounded-2xl shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_30px_rgba(16,185,129,0.5)] transition-all transform hover:-translate-y-1">
                                Kirim Balasan Ke Pelapor
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-8">
            <!-- Status & Action Panel -->
            <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm dark:shadow-xl transition-colors">
                <h3 class="text-xs font-black text-slate-900 dark:text-white mb-6 uppercase tracking-[0.2em] flex items-center transition-colors">
                    <svg class="w-4 h-4 mr-2 text-blue-600 dark:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Aksi Tiket
                </h3>

                @if($ticket->assigned_to === Auth::id())
                <!-- Tombol Selesaikan Tiket -->
                <form method="POST" action="{{ route('agent.tickets.complete', $ticket) }}" class="mb-3"
                    onsubmit="return confirm('✅ Konfirmasi Selesaikan Tiket?\n\nApakah Anda yakin tiket ini sudah selesai dikerjakan?\nPelapor akan otomatis mendapat notifikasi email.')">
                    @csrf
                    <button type="submit"
                        class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_30px_rgba(16,185,129,0.5)] transition-all transform hover:-translate-y-0.5 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Selesaikan Tiket
                    </button>
                </form>

                <!-- Tombol Kembalikan ke Super Admin -->
                <form method="POST" action="{{ route('agent.tickets.return', $ticket) }}" class="mb-4"
                    onsubmit="return confirm('🔄 Konfirmasi Kembalikan Tiket?\n\nTiket ini akan dikembalikan ke Super Admin.\nStatus akan diatur ulang dan Anda akan dilepaskan dari tiket ini.')">
                    @csrf
                    <button type="submit"
                        class="w-full py-3 bg-amber-50 dark:bg-amber-600/10 border border-amber-200 dark:border-amber-600/30 text-amber-700 dark:text-amber-500 hover:bg-amber-100 dark:hover:bg-amber-600/20 hover:text-amber-800 dark:hover:text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Kembalikan ke Super Admin
                    </button>
                </form>
                @endif

                <!-- Ubah Status Manual -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    <form method="POST" action="{{ route('agent.tickets.status', $ticket) }}">
                        @csrf
                        <div class="space-y-4">
                            <select name="status_id"
                                class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 focus:border-emerald-500 outline-none transition-all py-3 px-4">
                                @foreach(\App\Models\Status::all() as $status)
                                    <option value="{{ $status->id }}" {{ $ticket->status_id == $status->id ? 'selected' : '' }} class="bg-white dark:bg-slate-900">
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full py-3 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-700 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                                Konfirmasi Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Assignment Panel -->
            @can('tickets.assign')
                <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm dark:shadow-xl transition-colors">
                    <h3 class="text-xs font-black text-slate-900 dark:text-white mb-6 uppercase tracking-[0.2em] flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2 text-emerald-600 dark:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Penugasan Analis
                    </h3>
                    <div class="mb-4">
                        @if($ticket->assignee)
                            <div class="flex items-center p-3 bg-emerald-50 dark:bg-emerald-500/5 border border-emerald-100 dark:border-emerald-500/20 rounded-xl transition-colors">
                                <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-500/20 rounded flex items-center justify-center mr-3 transition-colors">
                                    <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 transition-colors">{{ substr($ticket->assignee->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest transition-colors">Active Assignee</div>
                                    <div class="text-sm font-black text-slate-700 dark:text-slate-200 transition-colors">{{ $ticket->assignee->name }}</div>
                                </div>
                            </div>
                        @else
                            <div class="p-3 bg-slate-50 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800 rounded-xl text-center italic text-xs text-slate-500 transition-colors">Belum ada analis ditugaskan</div>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('agent.tickets.assign', $ticket) }}">
                        @csrf
                        <div class="space-y-4">
                            <select name="user_id"
                                class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 focus:border-emerald-500 outline-none transition-all py-3 px-4">
                                <option value="" class="bg-white dark:bg-slate-900">Pilih Analis Keamanan</option>
                                @foreach($agents as $agent)
                                    @if($agent && isset($agent->id))
                                        <option value="{{ $agent->id }}" {{ $ticket->assigned_to == $agent->id ? 'selected' : '' }} class="bg-white dark:bg-slate-900">
                                            {{ $agent->name }}
                                            @php $role = $agent->roles->first(); @endphp
                                            @if($role)
                                                [{{ $role->name }}]
                                            @endif
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <button type="submit"
                            class="w-full py-3 bg-emerald-50 dark:bg-emerald-600/20 border border-emerald-200 dark:border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-600/30 hover:text-emerald-700 dark:hover:text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                                Tugaskan Analis
                            </button>
                        </div>
                    </form>
                </div>
            @endcan

            <!-- Internal Note Panel -->
            <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm dark:shadow-xl relative overflow-hidden transition-colors">
                <div class="absolute top-0 right-0 p-2">
                    <span class="text-[8px] font-black text-amber-600 dark:text-amber-500/50 uppercase tracking-widest transition-colors">Confidential</span>
                </div>
                <h3 class="text-xs font-black text-slate-900 dark:text-white mb-6 uppercase tracking-[0.2em] flex items-center transition-colors">
                    <svg class="w-4 h-4 mr-2 text-amber-600 dark:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg>
                    Catatan Internal (Ops)
                </h3>
                <form method="POST" action="{{ route('agent.tickets.note', $ticket) }}">
                    @csrf
                    <div class="space-y-4">
                        <textarea name="note" rows="4" placeholder="Hanya terlihat oleh tim analis..."
                            class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-400 p-4 focus:border-amber-500 outline-none transition-all placeholder-slate-300 dark:placeholder-slate-800"></textarea>
                        <button type="submit"
                            class="w-full py-3 bg-amber-50 dark:bg-amber-600/10 border border-amber-200 dark:border-amber-600/30 text-amber-600 dark:text-amber-500 hover:bg-amber-100 dark:hover:bg-amber-600/20 hover:text-amber-700 dark:hover:text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                            Simpan Log Internal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(isset($cannedResponses) && $cannedResponses->count() > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectBtn = document.getElementById('selectTemplateBtn');
            const dropdown = document.getElementById('templateDropdown');
            const searchInput = document.getElementById('templateSearch');
            const templateList = document.getElementById('templateList');
            const messageTextarea = document.getElementById('messageTextarea');
            const templateItems = document.querySelectorAll('.template-item');

            // Toggle dropdown
            if (selectBtn && dropdown) {
                selectBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                    if (!dropdown.classList.contains('hidden')) {
                        searchInput.focus();
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target) && e.target !== selectBtn) {
                        dropdown.classList.add('hidden');
                    }
                });
            }

            // Search functionality
            if (searchInput && templateItems.length > 0) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    templateItems.forEach(item => {
                        const title = item.getAttribute('data-title').toLowerCase();
                        const body = item.getAttribute('data-body').toLowerCase();
                        if (title.includes(searchTerm) || body.includes(searchTerm)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // Insert template into textarea
            templateItems.forEach(item => {
                item.addEventListener('click', function() {
                    let body, title;
                    try {
                        body = JSON.parse(this.getAttribute('data-body'));
                        title = JSON.parse(this.getAttribute('data-title'));
                    } catch(e) {
                        body = this.getAttribute('data-body');
                        title = this.getAttribute('data-title');
                    }
                    
                    let templateBody = body;
                    templateBody = templateBody.replace(/\{\{TICKET_NUMBER\}\}/g, '{{ $ticket->ticket_number }}');
                    templateBody = templateBody.replace(/\{\{SUBJECT\}\}/g, '{{ $ticket->subject }}');
                    templateBody = templateBody.replace(/\{\{REPORTER_NAME\}\}/g, '{{ $ticket->requester->name ?? $ticket->reporter_name ?? "Pelapor" }}');
                    
                    if (messageTextarea.value.trim() !== '') {
                        if (confirm('Textarea sudah berisi teks. Apakah Anda ingin menggantinya dengan template "' + title + '"?')) {
                            messageTextarea.value = templateBody;
                        }
                    } else {
                        messageTextarea.value = templateBody;
                    }
                    
                    messageTextarea.focus();
                    const len = templateBody.length;
                    messageTextarea.setSelectionRange(len, len);
                    
                    if (dropdown) {
                        dropdown.classList.add('hidden');
                    }
                });
            });
        });
    </script>
    @endif
</x-agent-layout>