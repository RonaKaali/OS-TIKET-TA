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
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.zero-trust-meta')
    <title>Dasbor Saya - CSIRT Kalselprov</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
        }

        @keyframes countUp {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-count-up {
            animation: countUp 0.4s ease-out forwards;
        }

        .stat-glow-blue { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); transition: box-shadow 0.3s; }
        .stat-glow-blue:hover { box-shadow: 0 0 25px -5px rgba(59, 130, 246, 0.3); }
        .stat-glow-yellow { box-shadow: 0 0 0 0 rgba(234, 179, 8, 0); transition: box-shadow 0.3s; }
        .stat-glow-yellow:hover { box-shadow: 0 0 25px -5px rgba(234, 179, 8, 0.3); }
        .stat-glow-green { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); transition: box-shadow 0.3s; }
        .stat-glow-green:hover { box-shadow: 0 0 25px -5px rgba(16, 185, 129, 0.3); }
        .stat-glow-red { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); transition: box-shadow 0.3s; }
        .stat-glow-red:hover { box-shadow: 0 0 25px -5px rgba(239, 68, 68, 0.3); }
    </style>
</head>

<body class="antialiased text-slate-700 dark:text-slate-300 transition-colors">
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 cyber-grid relative overflow-hidden transition-colors">
        <div class="fixed top-4 right-4 z-50">
            <x-theme-toggle />
        </div>
        <!-- Background Glows -->
        <div class="absolute top-0 -left-4 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
        <div class="absolute bottom-0 -right-4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl opacity-30 pointer-events-none"></div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 relative z-10">
            <!-- Navigation Back -->
            <nav class="mb-8">
                <a href="{{ route('welcome') }}"
                    class="inline-flex items-center space-x-3 px-5 py-2.5 bg-white dark:bg-slate-900/50 backdrop-blur-md border border-slate-200 dark:border-slate-700/50 rounded-xl text-slate-600 dark:text-slate-300 font-medium hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/50 transition-all duration-300 group shadow-sm text-sm">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </nav>

            <!-- Hero Greeting -->
            <div class="glass-card rounded-3xl p-6 sm:p-8 mb-8 animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                                <span class="text-white font-black text-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight transition-colors">
                                    Selamat Datang, <span class="text-emerald-600 dark:text-emerald-400">{{ $user->name }}</span>!
                                </h1>
                                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium transition-colors">Pusat kendali laporan insiden siber Anda</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        @if($user->mfa_secret)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/20 rounded-full text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                MFA Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-500/10 border border-yellow-500/20 rounded-full text-[10px] font-black uppercase tracking-widest text-yellow-600 dark:text-yellow-400 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                MFA Belum Aktif
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Bergabung {{ $user->created_at->format('d M Y') }}
                        </span>
                    </div>
                </div>

                @if($activeReports > 0)
                    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700/50">
                        <p class="text-sm text-slate-600 dark:text-slate-400 transition-colors">
                            📋 Anda memiliki <strong class="text-emerald-600 dark:text-emerald-400">{{ $activeReports }} laporan</strong> yang sedang diproses oleh tim CSIRT.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <!-- Total -->
                <div class="glass-card rounded-2xl p-5 sm:p-6 stat-glow-blue transform hover:-translate-y-1 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.15s">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-500/20 border border-blue-200 dark:border-blue-500/50 rounded-xl flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white animate-count-up transition-colors">{{ $totalReports }}</p>
                    <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1 transition-colors">Total Laporan</p>
                </div>

                <!-- Active -->
                <div class="glass-card rounded-2xl p-5 sm:p-6 stat-glow-yellow transform hover:-translate-y-1 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.2s">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-yellow-50 dark:bg-yellow-500/20 border border-yellow-200 dark:border-yellow-500/50 rounded-xl flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white animate-count-up transition-colors">{{ $activeReports }}</p>
                    <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1 transition-colors">Sedang Diproses</p>
                </div>

                <!-- Closed -->
                <div class="glass-card rounded-2xl p-5 sm:p-6 stat-glow-green transform hover:-translate-y-1 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.25s">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/50 rounded-xl flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white animate-count-up transition-colors">{{ $closedReports }}</p>
                    <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1 transition-colors">Selesai</p>
                </div>

            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 mb-8">
                <!-- Line Chart: Monthly Trend -->
                <div class="lg:col-span-2 glass-card rounded-3xl p-6 sm:p-8 animate-fade-in-up" style="animation-delay: 0.35s">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                        <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center tracking-tight transition-colors">
                            <div class="w-9 h-9 bg-indigo-500/10 rounded-xl flex items-center justify-center mr-3 transition-colors">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg>
                            </div>
                            Tren Laporan Saya
                        </h3>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-full border border-slate-200 dark:border-slate-700 transition-colors">12 Bulan Terakhir</span>
                    </div>
                    <div class="relative h-64 sm:h-72 w-full">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>

                <!-- Donut Chart: Status Distribution -->
                <div class="glass-card rounded-3xl p-6 sm:p-8 animate-fade-in-up" style="animation-delay: 0.4s">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center tracking-tight mb-6 transition-colors">
                        <div class="w-9 h-9 bg-purple-500/10 rounded-xl flex items-center justify-center mr-3 transition-colors">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                        </div>
                        Status Laporan
                    </h3>
                    <div class="relative h-52 sm:h-56 w-full flex items-center justify-center">
                        <canvas id="statusDonutChart"></canvas>
                    </div>
                    @if(count($statusLabels) > 0)
                        <div class="mt-4 space-y-2">
                            @foreach($statusLabels as $idx => $label)
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full" style="background-color: {{ ['#3b82f6', '#eab308', '#10b981', '#ef4444', '#8b5cf6', '#ec4899'][$idx % 6] }}"></span>
                                        <span class="text-slate-600 dark:text-slate-400 text-xs font-bold transition-colors">{{ $label }}</span>
                                    </div>
                                    <span class="text-slate-900 dark:text-white font-black text-xs transition-colors">{{ $statusData[$idx] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-slate-400 text-sm mt-4">Belum ada data</p>
                    @endif
                </div>
            </div>

            <!-- Recent Tickets -->
            <div class="glass-card rounded-3xl p-6 sm:p-8 mb-8 animate-fade-in-up" style="animation-delay: 0.45s">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center tracking-tight transition-colors">
                        <div class="w-9 h-9 bg-emerald-500/10 rounded-xl flex items-center justify-center mr-3 transition-colors">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        Laporan Terbaru
                    </h3>
                    @if($totalReports > 5)
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $totalReports }} total laporan</span>
                    @endif
                </div>

                @if($recentTickets->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentTickets as $ticket)
                            @php
                                $statusSlug = $ticket->status?->slug ?? 'open';
                                $isClosed = $ticket->status?->is_closed ?? false;
                                $isOverdue = !$isClosed && $ticket->due_at && $ticket->due_at->isPast() && $ticket->isOverdue();

                                if ($isClosed) {
                                    $badgeClass = 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20';
                                    $dotClass = 'bg-emerald-500';
                                } elseif ($isOverdue) {
                                    $badgeClass = 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20';
                                    $dotClass = 'bg-red-500 animate-pulse';
                                } elseif ($statusSlug === 'answered') {
                                    $badgeClass = 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 border-yellow-500/20';
                                    $dotClass = 'bg-yellow-500';
                                } else {
                                    $badgeClass = 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20';
                                    $dotClass = 'bg-blue-500';
                                }
                            @endphp
                            <a href="{{ route('portal.ticket.show', $ticket->ticket_number) }}"
                                class="block p-4 sm:p-5 bg-white dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-700/50 hover:border-emerald-500/50 dark:hover:border-emerald-500/30 transition-all duration-300 group hover:-translate-y-0.5">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest transition-colors">{{ $ticket->ticket_number }}</span>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-black uppercase tracking-widest rounded-full border {{ $badgeClass }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                                {{ $ticket->status?->name ?? 'Open' }}
                                            </span>
                                            @if($ticket->priority)
                                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider transition-colors">• {{ $ticket->priority->name }}</span>
                                            @endif
                                        </div>
                                        <h4 class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors truncate">
                                            {{ $ticket->subject }}
                                        </h4>
                                        <div class="flex flex-wrap items-center gap-3 mt-1.5">
                                            <span class="text-[10px] text-slate-500 dark:text-slate-500 font-medium transition-colors">
                                                {{ $ticket->department?->name ?? '-' }}
                                            </span>
                                            <span class="text-slate-300 dark:text-slate-700">·</span>
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium transition-colors">
                                                {{ $ticket->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center shrink-0">
                                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 opacity-0 group-hover:opacity-100 transition-all flex items-center gap-1">
                                            Lihat Detail
                                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="w-24 h-24 mx-auto mb-6 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center border-2 border-dashed border-slate-300 dark:border-slate-700 transition-colors">
                            <svg class="w-12 h-12 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h4 class="text-lg font-black text-slate-700 dark:text-slate-300 mb-2 transition-colors">Belum Ada Laporan</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-md mx-auto transition-colors">
                            Anda belum membuat laporan insiden siber. Mulailah dengan melaporkan insiden yang Anda temukan.
                        </p>
                        <a href="{{ route('portal.ticket.create') }}"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-blue-600 hover:from-emerald-500 hover:to-blue-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 transition-all transform hover:-translate-y-0.5 text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Buat Laporan Pertama
                        </a>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 animate-fade-in-up" style="animation-delay: 0.5s">
                <a href="{{ route('portal.ticket.create') }}"
                    class="glass-card rounded-2xl p-5 sm:p-6 flex items-center gap-4 hover:-translate-y-1 transition-all duration-300 group hover:border-emerald-500/50">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:shadow-emerald-500/40 transition-all shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-sm group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Buat Laporan Baru</h4>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5 transition-colors">Laporkan insiden siber</p>
                    </div>
                </a>

                <a href="{{ route('portal.ticket.status.form') }}"
                    class="glass-card rounded-2xl p-5 sm:p-6 flex items-center gap-4 hover:-translate-y-1 transition-all duration-300 group hover:border-blue-500/50">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:shadow-blue-500/40 transition-all shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-sm group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Lacak Laporan</h4>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5 transition-colors">Cek status dengan nomor tiket</p>
                    </div>
                </a>

                <a href="{{ route('profile.edit') }}"
                    class="glass-card rounded-2xl p-5 sm:p-6 flex items-center gap-4 hover:-translate-y-1 transition-all duration-300 group hover:border-purple-500/50">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/20 group-hover:shadow-purple-500/40 transition-all shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-sm group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Pengaturan Profil</h4>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5 transition-colors">Kelola akun & MFA</p>
                    </div>
                </a>
            </div>

            <!-- Footer text -->
            <p class="text-center mt-10 text-slate-500 dark:text-slate-500 text-xs transition-colors">
                Data yang ditampilkan hanya mencakup laporan milik Anda sendiri. Kerahasiaan terjaga oleh
                <span class="text-emerald-600 dark:text-emerald-400 font-bold">Protokol Zero Trust CSIRT</span>.
            </p>
        </div>
    </div>

    @include('components.chatbot-widget')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            const textColor = isDark ? '#64748b' : '#94a3b8';

            // Monthly Trend Line Chart
            const trendCtx = document.getElementById('monthlyTrendChart');
            if (trendCtx) {
                const gradient = trendCtx.getContext('2d').createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
                gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: @json($monthlyLabels),
                        datasets: [{
                            label: 'Laporan Saya',
                            data: @json($monthlyData),
                            borderColor: '#10b981',
                            backgroundColor: gradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: isDark ? '#0f172a' : '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 12,
                                borderColor: 'rgba(16, 185, 129, 0.3)',
                                borderWidth: 1,
                                callbacks: {
                                    title: (items) => items[0].label,
                                    label: (item) => `${item.raw} laporan`,
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: gridColor, drawBorder: false },
                                ticks: {
                                    color: textColor,
                                    font: { size: 10, weight: 'bold' },
                                    stepSize: 1,
                                    precision: 0
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: textColor, font: { size: 10, weight: 'bold' } }
                            }
                        }
                    }
                });
            }

            // Status Distribution Donut Chart
            const donutCtx = document.getElementById('statusDonutChart');
            if (donutCtx) {
                const statusLabels = @json($statusLabels);
                const statusData = @json($statusData);

                const colors = ['#3b82f6', '#eab308', '#10b981', '#ef4444', '#8b5cf6', '#ec4899'];

                if (statusLabels.length > 0) {
                    new Chart(donutCtx, {
                        type: 'doughnut',
                        data: {
                            labels: statusLabels,
                            datasets: [{
                                data: statusData,
                                backgroundColor: colors.slice(0, statusLabels.length),
                                borderColor: isDark ? '#0f172a' : '#ffffff',
                                borderWidth: 3,
                                hoverOffset: 8,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                    titleFont: { size: 13, weight: 'bold' },
                                    bodyFont: { size: 12 },
                                    padding: 12,
                                    borderColor: 'rgba(16, 185, 129, 0.3)',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: (item) => {
                                            const total = item.dataset.data.reduce((a, b) => a + b, 0);
                                            const pct = total > 0 ? ((item.raw / total) * 100).toFixed(0) : 0;
                                            return `${item.label}: ${item.raw} (${pct}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
</body>
</html>
