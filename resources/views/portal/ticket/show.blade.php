<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<script>
    if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
</script>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Laporan - CSIRT Kalselprov</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .cyber-grid {
            background-image: 
                linear-gradient(rgba(16, 185, 129, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(16, 185, 129, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>

<body class="antialiased text-slate-700 dark:text-slate-300 transition-colors">
    <div class="fixed top-4 right-4 z-[100]">
        <x-theme-toggle />
    </div>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 cyber-grid py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden transition-colors">
        <!-- Background Glows -->
        <div class="absolute top-0 -left-4 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 -right-4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl opacity-30"></div>

        <!-- Navigation -->
        <nav class="max-w-4xl mx-auto mb-10 relative z-10">
            <a href="{{ route('portal.ticket.status.form') }}"
                class="inline-flex items-center space-x-3 px-6 py-3 bg-white dark:bg-slate-900/50 backdrop-blur-md border border-slate-200 dark:border-slate-700/50 rounded-xl text-slate-600 dark:text-slate-300 font-medium hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/50 transition-all duration-300 group shadow-sm">
                <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Cek Status</span>
            </a>
        </nav>

        <div class="max-w-4xl mx-auto space-y-8 relative z-10">
            @if(session('ok') === 'Tiket berhasil dibuat.')
                <!-- Success Message for New Ticket -->
                <div class="glass-card rounded-2xl p-6 border border-emerald-500/50 bg-emerald-500/10 flex flex-col sm:flex-row items-center sm:items-start gap-5 shadow-[0_0_30px_rgba(16,185,129,0.15)] animate-fade-in-down">
                    <div class="w-14 h-14 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0 shadow-inner">
                        <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="text-center sm:text-left">
                        <h3 class="text-xl font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">LAPORAN BERHASIL</h3>
                        <p class="text-sm font-bold text-emerald-700 dark:text-emerald-300 mt-2 leading-relaxed">
                            TIM CSIRT KALSEL SEDANG MEMPROSES LAPORAN ANDA. KAMI AKAN TERUS HUBUNGI MELALUI EMAIL.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Ticket Header Card -->
            <div class="glass-card rounded-3xl p-8 shadow-2xl">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-6 mb-8">
                    <div class="space-y-2">
                        <div class="inline-flex items-center px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-full text-[10px] font-black uppercase tracking-[0.2em] text-emerald-400 mb-2">
                            Secure Ticket Log
                        </div>
                        <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight transition-colors">{{ $ticket->ticket_number }}</h1>
                        <p class="text-xl text-slate-600 dark:text-slate-400 font-medium transition-colors">{{ $ticket->subject }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-3">
                        <span class="px-5 py-2 text-sm font-bold rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 shadow-sm transition-all">
                            <span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                            {{ $ticket->status?->name ?? 'Open' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 pt-8 border-t border-slate-800/50">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block">Departemen</span>
                        <span class="text-slate-800 dark:text-slate-200 font-medium transition-colors">{{ $ticket->department?->name ?? '-' }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block">Prioritas</span>
                        <span class="text-slate-800 dark:text-slate-200 font-medium transition-colors">{{ $ticket->priority?->name ?? 'Normal' }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block">Laporan Dibuat</span>
                        <span class="text-slate-800 dark:text-slate-200 font-medium transition-colors">{{ $ticket->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Communication Timeline -->
            <div class="space-y-6">
                <div class="flex items-center space-x-4 mb-2 ml-2">
                    <div class="w-10 h-1 bg-emerald-500/50 rounded-full"></div>
                    <h2 class="text-xs font-black text-slate-500 uppercase tracking-[0.3em]">Riwayat Komunikasi Terenkripsi</h2>
                </div>

                <div class="space-y-6">
                    @foreach($ticket->threads as $thread)
                        <div class="glass-card rounded-2xl overflow-hidden relative {{ $thread->type === 'message' ? 'ml-0' : 'ml-4 sm:ml-8' }}">
                            <div class="absolute top-0 left-0 w-1 h-full {{ $thread->type === 'message' ? 'bg-blue-500/50' : 'bg-emerald-500/50' }}"></div>
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs {{ $thread->type === 'message' ? 'bg-blue-500/10 text-blue-400' : 'bg-emerald-500/10 text-emerald-400' }}">
                                            {{ $thread->type === 'message' ? 'U' : 'A' }}
                                        </div>
                                        <div>
                                            <span class="text-sm font-bold text-slate-800 dark:text-white transition-colors">
                                                {{ $thread->type === 'message' ? 'Anda (Pelapor)' : ($thread->user?->name ?? 'Agen Keamanan') }}
                                            </span>
                                            <span class="text-[10px] text-slate-500 ml-2 uppercase tracking-tighter">{{ $thread->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed whitespace-pre-wrap transition-colors">{{ $thread->body }}</div>

                                @if($thread->attachments->count() > 0)
                                    <div class="mt-6 pt-4 border-t border-slate-800/50">
                                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Lampiran Terlampir:</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($thread->attachments as $attachment)
                                                <a href="{{ route('attachments.download', $attachment) }}" target="_blank"
                                                    class="inline-flex items-center px-4 py-2 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/30 transition-all group shadow-sm">
                                                    <svg class="w-4 h-4 mr-2 text-slate-400 dark:text-slate-500 group-hover:text-emerald-600 dark:group-hover:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    {{ $attachment->original_filename ?? $attachment->filename }}
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

            <!-- Reply Form -->
            @if(($ticket->status?->slug ?? 'open') !== 'closed')
                <div class="glass-card rounded-3xl p-8 border border-emerald-500/20 shadow-lg dark:shadow-[0_0_50px_rgba(16,185,129,0.05)] transition-all">
                    <h2 class="text-xl font-black text-slate-800 dark:text-white mb-6 uppercase tracking-tight transition-colors">Kirim Balasan</h2>
                    <form method="POST" action="{{ route('portal.ticket.reply', $ticket->ticket_number) }}"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div class="space-y-2">
                            <label for="message" class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Pesan Balasan <span class="text-emerald-500">*</span></label>
                            <textarea name="message" id="message" rows="5" required
                                placeholder="Berikan informasi tambahan atau tanggapan Anda di sini..."
                                class="block w-full bg-white dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-2xl px-5 py-4 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-700 focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-300 shadow-sm"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label for="attachments" class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Lampiran Baru</label>
                            <input type="file" name="attachments[]" id="attachments" multiple
                                accept="image/*,.pdf,.doc,.docx"
                                class="block w-full text-sm text-slate-500 file:mr-6 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-slate-100 dark:file:bg-slate-900 file:text-emerald-600 dark:file:text-emerald-400 hover:file:bg-slate-200 dark:hover:file:bg-slate-800 cursor-pointer transition-colors">
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-gradient-to-r from-emerald-600 to-blue-600 hover:from-emerald-500 hover:to-blue-500 text-white font-black uppercase tracking-[0.2em] rounded-2xl transition-all duration-300 transform hover:-translate-y-1 shadow-lg flex items-center justify-center space-x-3 group">
                            <span>Kirim Balasan</span>
                            <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </button>
                    </form>
                </div>
            @endif

            <div class="text-center pt-8 border-t border-slate-900">
                <a href="{{ route('portal.ticket.create') }}" class="inline-flex items-center space-x-2 text-slate-500 hover:text-emerald-400 transition-colors uppercase tracking-widest text-[10px] font-black">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>Laporkan Insiden Baru</span>
                </a>
            </div>
        </div>
    </div>
    @include('components.chatbot-widget')
</body>
</html>