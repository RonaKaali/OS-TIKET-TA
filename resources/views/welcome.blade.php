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
    <title>Pengaduan Insiden Siber - CSIRT Kalselprov</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .cyber-grid {
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(16, 185, 129, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(16, 185, 129, 0.05) 1px, transparent 1px);
        }

        .glow-text {
            text-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(16, 185, 129, 0.1);
        }

        .dark .glass-panel {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .animated-border {
            position: relative;
            background: linear-gradient(90deg, #10b981, #3b82f6, #10b981);
            background-size: 200% 200%;
            animation: gradient-border 3s ease infinite;
        }
        
        @keyframes gradient-border {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>

<body class="antialiased min-h-screen cyber-grid relative overflow-x-hidden bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-200">
    <!-- Splash Screen -->
    <div id="splash-screen" class="fixed inset-0 z-[100] bg-slate-950 flex flex-col items-center justify-center transition-opacity duration-1000">
        <div class="relative">
            <div class="w-32 h-32 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo" class="w-16 h-16 animate-pulse">
            </div>
        </div>
        <h1 class="mt-8 text-lg sm:text-2xl font-black text-white tracking-[0.15em] sm:tracking-[0.3em] uppercase glow-text text-center px-6 leading-relaxed">
            Portal Pelaporan Insiden Siber<br>
            <span class="text-emerald-500">Pemprov Kalimantan Selatan</span>
        </h1>
        <div class="mt-4 flex space-x-1">
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
        </div>
    </div>

    <!-- Ambient Background Lights -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-emerald-900/20 blur-[120px] pointer-events-none"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-blue-900/20 blur-[120px] pointer-events-none"></div>

    <!-- Navigation -->
    <nav x-data="{ navOpen: false }" class="bg-white/90 dark:bg-slate-900/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200 dark:border-emerald-500/30 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20 gap-3">
                <!-- Brand -->
                <a href="{{ route('welcome') }}" class="flex items-center gap-2.5 sm:gap-4 min-w-0 shrink">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 flex items-center justify-center bg-emerald-500/5 rounded-xl border border-emerald-500/20 shrink-0" id="tour-brand">
                        <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Kalselprov" class="w-7 h-7 sm:w-9 sm:h-9 md:w-10 md:h-10 object-contain">
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm sm:text-base md:text-lg font-extrabold text-slate-900 dark:text-white tracking-wide truncate">
                            CSIRT <span class="text-emerald-600 dark:text-emerald-400">KALSELPROV</span>
                        </div>
                        <div class="hidden sm:flex text-[10px] sm:text-xs text-emerald-700/80 dark:text-emerald-300/70 uppercase tracking-widest items-center">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse shrink-0"></span>
                            <span class="truncate">Zero Trust Secured</span>
                        </div>
                    </div>
                </a>

                <!-- Desktop menu -->
                <div class="hidden md:flex items-center gap-3 lg:gap-4 shrink-0">
                    <x-theme-toggle />
                    @auth
                        @can('admin.panel')
                            <a href="{{ route('dashboard') }}" class="text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium text-sm transition">Dasbor</a>
                        @endcan
                        <a href="{{ route('profile.edit') }}" class="text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium text-sm transition">Profil</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 font-medium text-sm transition">Keluar</button>
                        </form>
                    @else
                        <a href="/admin" class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white text-xs font-bold uppercase tracking-widest transition">Admin</a>
                        <div class="h-4 w-px bg-slate-300 dark:bg-slate-700"></div>
                        <a href="{{ route('login') }}" class="text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 px-4 py-2 rounded-lg font-bold text-sm transition">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2 text-sm font-bold rounded-xl text-white bg-gradient-to-r from-emerald-600 to-blue-600 hover:from-emerald-500 hover:to-blue-500 shadow-md transition">Daftar</a>
                        @endif
                    @endauth
                    <!-- Tutorial Button -->
                    <button onclick="startCsirtTour && startCsirtTour()" class="csirt-tour-trigger" title="Lihat Panduan Sistem">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253" /></svg>
                        Tutorial
                    </button>
                </div>

                <!-- Mobile: theme + hamburger -->
                <div class="flex md:hidden items-center gap-2 shrink-0">
                    <x-theme-toggle />
                    <button type="button" @click="navOpen = !navOpen" class="p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition" aria-label="Menu">
                        <svg x-show="!navOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="navOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile dropdown -->
        <div x-show="navOpen" x-cloak x-transition class="md:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 space-y-2">
            @auth
                @can('admin.panel')
                    <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-xl text-slate-700 dark:text-slate-200 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition">Dasbor</a>
                @endcan
                <a href="{{ route('portal.ticket.create') }}" class="block px-4 py-3 rounded-xl text-emerald-700 dark:text-emerald-300 font-bold hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition">Laporkan Insiden</a>
                <a href="{{ route('portal.ticket.status.form') }}" class="block px-4 py-3 rounded-xl text-slate-700 dark:text-slate-200 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition">Lacak Laporan</a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-xl text-slate-700 dark:text-slate-200 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-3 rounded-xl text-red-600 dark:text-red-400 font-medium hover:bg-red-50 dark:hover:bg-red-900/20 transition">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-4 py-3 rounded-xl text-center font-bold text-white bg-gradient-to-r from-emerald-600 to-blue-600 transition">Masuk / Login untuk Lapor</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="block px-4 py-3 rounded-xl text-center font-bold text-emerald-700 dark:text-emerald-300 border-2 border-emerald-500/40 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition">Daftar Akun Baru</a>
                @endif
                <a href="{{ route('portal.ticket.status.form') }}" class="block px-4 py-3 rounded-xl text-slate-700 dark:text-slate-200 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition">Lacak Laporan</a>
                <a href="/admin" class="block px-4 py-3 rounded-xl text-slate-500 dark:text-slate-400 text-sm font-bold uppercase tracking-widest hover:bg-slate-100 dark:hover:bg-slate-800 transition">Admin Portal</a>
            @endauth
        </div>
    </nav>

    <!-- Success/Status Messages -->
    @if(session('status'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 relative z-10">
            <div class="bg-emerald-900/50 border border-emerald-500 p-4 rounded-lg shadow-[0_0_20px_rgba(16,185,129,0.2)] backdrop-blur-md">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-emerald-400 mr-3 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-emerald-100 font-medium">{{ session('status') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Hero Section -->
    <div class="relative py-12 sm:py-16 md:py-20 lg:py-32 overflow-hidden z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="inline-flex items-center justify-center px-3 sm:px-4 py-1.5 mb-6 sm:mb-8 rounded-full border border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs sm:text-sm font-semibold uppercase tracking-wider backdrop-blur-md">
                    <span class="relative flex h-2 w-2 mr-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Sistem Pelaporan Aktif
                </div>
                
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-7xl font-extrabold mb-4 sm:mb-6 tracking-tight leading-tight px-1">
                    <span class="text-slate-900 dark:text-white transition-colors">Portal Pelaporan Insiden Siber</span><br/>
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 dark:from-emerald-400 via-teal-500 dark:via-teal-300 to-blue-600 dark:to-blue-500">
                        Pemerintah Provinsi Kalimantan Selatan
                    </span>
                </h1>
                
                <p class="text-base sm:text-lg md:text-xl text-slate-600 dark:text-slate-300 mb-8 sm:mb-10 max-w-3xl mx-auto font-medium transition-colors px-2">
                    Layanan resmi untuk melaporkan celah keamanan, peretasan, dan insiden siber pada infrastruktur elektronik di lingkungan Pemprov Kalsel.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center items-stretch sm:items-center max-w-md sm:max-w-none mx-auto px-2 sm:px-0">
                    @auth
                        <a href="{{ route('portal.ticket.create') }}"
                            id="tour-cta-report"
                            class="group relative inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-200 bg-emerald-600 font-pj rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 shadow-[0_0_20px_rgba(16,185,129,0.4)] hover:shadow-[0_0_30px_rgba(16,185,129,0.6)] hover:bg-emerald-500 hover:-translate-y-1 w-full sm:w-auto">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Laporkan Insiden Siber
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="group relative inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-200 bg-gradient-to-r from-emerald-600 to-blue-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 shadow-[0_0_20px_rgba(16,185,129,0.4)] hover:shadow-[0_0_30px_rgba(16,185,129,0.6)] hover:-translate-y-1 w-full sm:w-auto border border-emerald-400/50">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Login untuk Lapor
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    @endauth
                    <a href="{{ route('portal.ticket.status.form') }}"
                        id="tour-cta-track"
                        class="inline-flex items-center justify-center px-6 sm:px-8 py-3.5 sm:py-4 text-base font-bold text-emerald-700 dark:text-emerald-400 transition-all duration-200 bg-transparent border-2 border-emerald-500/50 rounded-xl hover:bg-emerald-500/10 hover:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 w-full sm:w-auto">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Lacak Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 border-t border-slate-200 dark:border-slate-800 transition-colors">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4 tracking-tight transition-colors">Protokol <span class="text-emerald-600 dark:text-emerald-400">CSIRT</span></h2>
            <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto transition-colors">Sistem penanganan insiden siber canggih dengan perlindungan end-to-end</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white/80 dark:bg-slate-900/50 backdrop-blur-md rounded-2xl p-8 border border-slate-200 dark:border-slate-800 hover:-translate-y-2 transition-all duration-300 group hover:border-emerald-500/50 shadow-sm hover:shadow-md">
                <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl flex items-center justify-center mb-6 group-hover:bg-emerald-500/20 transition-colors shadow-inner">
                    <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-3 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Zero Trust Architecture</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm">Verifikasi berlapis untuk setiap akses. Memastikan pelaporan Anda hanya dapat diakses oleh pihak yang berwenang.</p>
            </div>

            <!-- Feature 2 -->
            <div class="glass-panel dark:bg-slate-900/50 rounded-2xl p-8 hover:-translate-y-2 transition-all duration-300 group hover:border-blue-500/50 shadow-sm hover:shadow-md">
                <div class="w-14 h-14 bg-blue-100 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 rounded-xl flex items-center justify-center mb-6 group-hover:bg-blue-500/20 transition-colors shadow-inner">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-3 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Respon Cepat (SLA)</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm">Tim CSIRT bersiaga memonitor ancaman. Insiden ditangani dengan prioritas tinggi sesuai tingkat bahaya.</p>
            </div>

            <!-- Feature 3 -->
            <div class="glass-panel dark:bg-slate-900/50 rounded-2xl p-8 hover:-translate-y-2 transition-all duration-300 group hover:border-purple-500/50 shadow-sm hover:shadow-md">
                <div class="w-14 h-14 bg-purple-100 dark:bg-purple-500/10 border border-purple-200 dark:border-purple-500/30 rounded-xl flex items-center justify-center mb-6 group-hover:bg-purple-500/20 transition-colors shadow-inner">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-3 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Forensik Mendalam</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm">Analisis malware dan jejak digital secara mendalam oleh analis keamanan bersertifikat.</p>
            </div>
        </div>
    </div>

    <!-- Zero Trust Explained Section -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 transition-colors" x-data="{ activeTab: 1 }">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4 tracking-tight transition-colors">Bagaimana Kami <span class="text-emerald-600 dark:text-emerald-400">Melindungi Anda?</span></h2>
            <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto transition-colors">Mengenal teknologi <span class="font-bold text-emerald-600 dark:text-emerald-500">Zero Trust Security</span> yang digunakan pada portal ini.</p>
        </div>

        <div class="glass-panel dark:bg-slate-900/50 rounded-3xl p-6 md:p-10 shadow-xl border border-slate-200 dark:border-emerald-500/20 max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Sidebar Tabs -->
                <div class="w-full md:w-1/3 flex flex-col space-y-3 border-r-0 md:border-r border-slate-200 dark:border-slate-800 pr-0 md:pr-6">
                    <button @click="activeTab = 1" :class="activeTab === 1 ? 'bg-emerald-500/10 border-emerald-500 text-emerald-700 dark:text-emerald-400' : 'border-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'" class="text-left px-5 py-4 rounded-xl border-l-4 font-bold transition-all flex items-center">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        Apa itu Zero Trust?
                    </button>
                    <button @click="activeTab = 2" :class="activeTab === 2 ? 'bg-emerald-500/10 border-emerald-500 text-emerald-700 dark:text-emerald-400' : 'border-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'" class="text-left px-5 py-4 rounded-xl border-l-4 font-bold transition-all flex items-center">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" /></svg>
                        Device Fingerprinting
                    </button>
                    <button @click="activeTab = 3" :class="activeTab === 3 ? 'bg-emerald-500/10 border-emerald-500 text-emerald-700 dark:text-emerald-400' : 'border-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'" class="text-left px-5 py-4 rounded-xl border-l-4 font-bold transition-all flex items-center">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Context-Aware Access
                    </button>
                </div>

                <!-- Content Area -->
                <div class="w-full md:w-2/3 relative" style="min-height: 200px;">
                    <!-- Tab 1 -->
                    <div x-show="activeTab === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="absolute inset-0 flex flex-col justify-center">
                        <span class="inline-block px-3 py-1 bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-black uppercase tracking-widest rounded-full mb-4 w-max">Prinsip Utama</span>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-4">Never Trust, Always Verify.</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Sistem ini tidak pernah berasumsi bahwa seseorang aman hanya karena mereka mengetahui password. Setiap tindakan selalu divalidasi ulang secara otomatis. Admin sekalipun akan diblokir aksesnya jika bertingkah laku mencurigakan.
                        </p>
                    </div>
                    <!-- Tab 2 -->
                    <div x-show="activeTab === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="absolute inset-0 flex flex-col justify-center" x-cloak>
                        <span class="inline-block px-3 py-1 bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-black uppercase tracking-widest rounded-full mb-4 w-max">Keamanan Hardware</span>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-4">Pendeteksi Sidik Jari Perangkat</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Kami mencatat identitas unik dari fisik perangkat (HP/Laptop) yang digunakan. Jika hacker berhasil mencuri password Anda, mereka tetap tidak bisa mengakses akun Anda karena perangkat mereka belum pernah diverifikasi oleh sistem.
                        </p>
                    </div>
                    <!-- Tab 3 -->
                    <div x-show="activeTab === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="absolute inset-0 flex flex-col justify-center" x-cloak>
                        <span class="inline-block px-3 py-1 bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-black uppercase tracking-widest rounded-full mb-4 w-max">Analisis Real-time</span>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-4">Deteksi Anomali Konteks</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Sistem AI kami memantau dari lokasi dan jaringan apa sebuah akses dilakukan. Jika tiba-tiba ada akses dari negara lain atau menggunakan VPN/Proxy anonim, akses akan diblokir secara otomatis demi melindungi kerahasiaan laporan Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Intelligence & Stats Section -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20 bg-slate-50 dark:bg-slate-900/50 rounded-2xl sm:rounded-[3rem] border border-slate-200 dark:border-slate-800 shadow-xl dark:shadow-2xl mb-12 sm:mb-20 transition-all">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16">
            <!-- Left: Chart -->
            <div>
                <div class="flex items-center space-x-3 mb-8">
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/20 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight uppercase transition-colors">Statistik Insiden <span class="text-emerald-600 dark:text-emerald-500">2026</span></h2>
                </div>
                <div class="bg-white dark:bg-slate-950/50 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 relative group overflow-hidden shadow-sm transition-all">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <canvas id="publicIncidentChart" height="250"></canvas>
                </div>
            </div>

            <!-- Right: Live Intel -->
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-500/20 rounded-lg flex items-center justify-center shadow-sm">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600 dark:bg-red-500"></span>
                            </span>
                        </div>
                        <h2 class="text-lg sm:text-2xl font-black text-slate-800 dark:text-white tracking-tight uppercase transition-colors">Global Threat <span class="text-red-600 dark:text-red-500">Intel</span></h2>
                    </div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-3 py-1 border border-slate-200 dark:border-slate-800 rounded-full transition-colors">Secure Feed</span>
                </div>
                <div class="space-y-4" id="public-news-feed">
                    <!-- News Items -->
                    <div class="p-4 bg-white dark:bg-slate-950/50 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500/30 transition-all group cursor-default shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">High Alert</span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-600">Active Now</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-white transition-colors mb-1">Potensi Serangan DDoS pada Infrastruktur Cloud Pemerintah</h4>
                        <p class="text-xs text-slate-500">Sumber: BSSN Nasional Security Portal</p>
                    </div>
                    <div class="p-4 bg-white dark:bg-slate-950/50 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-500/30 transition-all group cursor-default shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest bg-blue-500/10 px-2 py-0.5 rounded border border-blue-500/20 transition-colors">Intelligence</span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-600 transition-colors">2 jam lalu</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-white transition-colors mb-1">Analisis Kampanye Phishing Menargetkan Kredensial ASN</h4>
                        <p class="text-xs text-slate-500">Sumber: Cyber Intel Unit</p>
                    </div>
                    <div class="p-4 bg-white dark:bg-slate-950/50 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-purple-500/30 transition-all group cursor-default shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-[10px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest bg-purple-500/10 px-2 py-0.5 rounded border border-purple-500/20 transition-colors">Advisory</span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-600 transition-colors">5 jam lalu</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-purple-600 dark:group-hover:text-white transition-colors mb-1">Update Keamanan: Penguatan Enkripsi pada Layanan Digital</h4>
                        <p class="text-xs text-slate-500">Sumber: Internal Audit Provinsi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="relative z-10 border-t border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 backdrop-blur-sm py-16 transition-colors" id="tour-stats">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-slate-800">
                <div class="py-4">
                    <div class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-br from-emerald-500 to-emerald-700 dark:from-emerald-400 dark:to-emerald-600 mb-2 transition-all">24/7</div>
                    <div class="text-slate-500 dark:text-slate-400 font-medium tracking-wide uppercase text-sm transition-colors">Monitoring Aktif</div>
                </div>
                <div class="py-4">
                    <div class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-br from-blue-500 to-blue-700 dark:from-blue-400 dark:to-blue-600 mb-2 transition-all">&lt; 1 Jam</div>
                    <div class="text-slate-500 dark:text-slate-400 font-medium tracking-wide uppercase text-sm transition-colors">Rata-rata Respon</div>
                </div>
                <div class="py-4">
                    <div class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-br from-purple-500 to-purple-700 dark:from-purple-400 dark:to-purple-600 mb-2 transition-all">100%</div>
                    <div class="text-slate-500 dark:text-slate-400 font-medium tracking-wide uppercase text-sm transition-colors">Enkripsi Data</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="relative z-10 bg-slate-50 dark:bg-[#0b1120] text-slate-500 dark:text-slate-400 py-12 border-t border-slate-200 dark:border-slate-800 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 flex items-center justify-center bg-white dark:bg-white/5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm transition-all">
                            <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Kalselprov" class="w-8 h-8 object-contain">
                        </div>
                        <div>
                            <div class="text-slate-900 dark:text-slate-200 font-bold tracking-wider transition-colors">CSIRT <span class="text-emerald-600 dark:text-emerald-500">KALSEL</span></div>
                        </div>
                    </div>
                    <p class="text-sm leading-relaxed mb-6">Infrastruktur pelaporan keamanan siber berstandar tinggi untuk melindungi data Pemerintahan Provinsi Kalimantan Selatan.</p>
                </div>
                
                <div>
                    <h4 class="text-slate-900 dark:text-white font-bold mb-6 tracking-wide transition-colors">Navigasi Keamanan</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('portal.ticket.create') }}" class="hover:text-emerald-400 transition-colors flex items-center text-sm"><span class="w-1.5 h-1.5 rounded-full bg-slate-600 mr-2"></span>Laporkan Insiden</a></li>
                        <li><a href="{{ route('portal.ticket.status.form') }}" class="hover:text-emerald-400 transition-colors flex items-center text-sm"><span class="w-1.5 h-1.5 rounded-full bg-slate-600 mr-2"></span>Lacak Laporan</a></li>
                        @auth
                            <li><a href="{{ route('profile.edit') }}" class="hover:text-emerald-400 transition-colors flex items-center text-sm"><span class="w-1.5 h-1.5 rounded-full bg-slate-600 mr-2"></span>Pengaturan Profil (MFA)</a></li>
                        @endauth
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-slate-900 dark:text-white font-bold mb-6 tracking-wide transition-colors">Pusat Komando</h4>
                    <ul class="space-y-4 text-sm">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-emerald-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <span>csirt@kalselprov.go.id<br/><span class="text-xs text-slate-500">Enkripsi PGP tersedia</span></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-emerald-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span>Dinas Komunikasi dan Informatika Prov. Kalsel</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-800/80 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-xs text-slate-500">&copy; {{ date('Y') }} CSIRT Kalselprov. All rights reserved.</p>
                <div class="flex items-center space-x-2 mt-4 md:mt-0 text-xs text-slate-500">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>Zero Trust Network Access Enabled</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Chatbot Widget -->
    @include('components.chatbot-widget')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Splash Screen Timeout
            setTimeout(() => {
                const splash = document.getElementById('splash-screen');
                if (splash) {
                    splash.style.opacity = '0';
                    setTimeout(() => splash.remove(), 1000);
                }
            }, 2000);

            // Public Incident Chart
            const ctx = document.getElementById('publicIncidentChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                        datasets: [{
                            label: 'Insiden Terdeteksi',
                            data: [12, 19, 15, 25, 22, 30],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: 'rgba(255,255,255,0.5)',
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: '#64748b', font: { size: 10 } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: '#64748b', font: { size: 10 } }
                            }
                        }
                    }
                });
            }
        });
    </script>

    <!-- Shepherd.js Tour Library -->
    <link rel="stylesheet" href="{{ asset('css/shepherd-default.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shepherd-csirt-theme.css') }}">
    <script src="{{ asset('js/shepherd.min.js') }}"></script>
    <script>
        @auth
            @php
                $userRole = Auth::user()->roles->first()->name ?? 'portal';
                if (in_array($userRole, ['Agent 1', 'Agent 2'])) {
                    $tourRole = 'agent';
                } elseif (in_array($userRole, ['Super Admin', 'Admin'])) {
                    $tourRole = 'admin';
                } else {
                    $tourRole = 'portal';
                }
            @endphp
            window.csirtTourRole = '{{ $tourRole }}';
        @else
            window.csirtTourRole = 'visitor';
        @endauth
    </script>
    @include('partials.tour-config')
</body>

</html>