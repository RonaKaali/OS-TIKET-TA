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
    <title>Tentang & Creator Sistem - CSIRT Kalselprov</title>
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

        .glass-panel {
            background: rgba(255, 255, 255, 0.78);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(16, 185, 129, 0.12);
        }

        .dark .glass-panel {
            background: rgba(15, 23, 42, 0.78);
            border-color: rgba(16, 185, 129, 0.22);
        }
    </style>
</head>

<body class="antialiased min-h-screen cyber-grid relative overflow-x-hidden bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-200">
    <div class="fixed top-[-12%] left-[-10%] w-[42%] h-[42%] rounded-full bg-emerald-900/20 blur-[120px] pointer-events-none"></div>
    <div class="fixed bottom-[-12%] right-[-10%] w-[42%] h-[42%] rounded-full bg-blue-900/20 blur-[120px] pointer-events-none"></div>

    <nav x-data="{ navOpen: false }" class="bg-white/90 dark:bg-slate-900/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200 dark:border-emerald-500/30 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20 gap-3">
                <a href="{{ route('welcome') }}" class="flex items-center gap-2.5 sm:gap-4 min-w-0 shrink">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 flex items-center justify-center bg-emerald-500/5 rounded-xl border border-emerald-500/20 shrink-0">
                        <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Kalselprov" class="w-7 h-7 sm:w-9 sm:h-9 md:w-10 md:h-10 object-contain">
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs sm:text-sm md:text-lg font-extrabold text-slate-900 dark:text-white tracking-wide truncate max-w-[150px] sm:max-w-none">
                            CSIRT <span class="text-emerald-600 dark:text-emerald-400">KALSELPROV</span>
                        </div>
                        <div class="hidden sm:flex text-[10px] sm:text-xs text-emerald-700/80 dark:text-emerald-300/70 uppercase tracking-widest items-center">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse shrink-0"></span>
                            <span class="truncate">Zero Trust Secured</span>
                        </div>
                    </div>
                </a>

                <div class="hidden md:flex items-center gap-3 lg:gap-4 shrink-0">
                    <x-theme-toggle />
                    <a href="{{ route('welcome') }}" class="text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium text-sm transition">Beranda</a>
                    <a href="{{ route('about') }}" class="text-emerald-700 dark:text-emerald-300 font-bold text-sm transition">Tentang</a>
                    <a href="{{ route('portal.ticket.status.form') }}" class="text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium text-sm transition">Lacak Laporan</a>
                    @auth
                        @can('admin.panel')
                            <a href="{{ route('dashboard') }}" class="text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium text-sm transition">Dasbor Admin</a>
                        @else
                            <a href="{{ route('portal.dashboard') }}" class="text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium text-sm transition">Dasbor Saya</a>
                        @endcan
                    @else
                        <a href="{{ route('login') }}" class="text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 px-4 py-2 rounded-lg font-bold text-sm transition">Masuk</a>
                    @endauth
                </div>

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

        <div x-show="navOpen" x-cloak x-transition class="md:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-6 space-y-3">
            <a href="{{ route('welcome') }}" class="block px-4 py-3.5 rounded-xl text-slate-700 dark:text-slate-200 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition">Beranda</a>
            <a href="{{ route('about') }}" class="block px-4 py-3.5 rounded-xl text-emerald-700 dark:text-emerald-300 font-bold bg-emerald-50 dark:bg-emerald-500/10 transition">Tentang</a>
            <a href="{{ route('portal.ticket.status.form') }}" class="block px-4 py-3.5 rounded-xl text-slate-700 dark:text-slate-200 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition">Lacak Laporan</a>
            @auth
                @can('admin.panel')
                    <a href="{{ route('dashboard') }}" class="block px-4 py-3.5 rounded-xl text-slate-700 dark:text-slate-200 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition">Dasbor Admin</a>
                @else
                    <a href="{{ route('portal.dashboard') }}" class="block px-4 py-3.5 rounded-xl text-slate-700 dark:text-slate-200 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition">Dasbor Saya</a>
                @endcan
            @else
                <a href="{{ route('login') }}" class="block px-4 py-3.5 rounded-xl text-center font-bold text-white bg-gradient-to-r from-emerald-600 to-blue-600 transition">Masuk</a>
            @endauth
        </div>
    </nav>

    <main class="relative z-10">
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-[1.1fr_0.9fr] gap-8 lg:gap-12 items-center">
                <div>
                    <div class="inline-flex items-center justify-center px-3 sm:px-4 py-1.5 mb-6 rounded-full border border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs sm:text-sm font-semibold uppercase tracking-wider backdrop-blur-md">
                        <span class="relative flex h-2 w-2 mr-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Sistem Tugas Akhir
                    </div>
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight text-slate-950 dark:text-white">
                        Tentang & Creator
                        <span class="block bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 via-teal-500 to-blue-600 dark:from-emerald-400 dark:via-teal-300 dark:to-blue-500">OS-Tiket CSIRT</span>
                    </h1>
                    <p class="mt-6 text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
                        OS-Tiket CSIRT Kalselprov adalah portal pelaporan insiden siber yang dirancang untuk menerima laporan, mengelola tiket, memantau tindak lanjut, dan memperkuat akses sistem dengan pendekatan Zero Trust.
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row gap-4">
                        @auth
                            <a href="{{ route('portal.ticket.create') }}" class="inline-flex items-center justify-center px-7 py-3.5 text-sm font-bold text-white bg-emerald-600 rounded-xl shadow-[0_0_20px_rgba(16,185,129,0.35)] hover:bg-emerald-500 hover:-translate-y-0.5 transition">
                                Laporkan Insiden
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-7 py-3.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-blue-600 rounded-xl shadow-[0_0_20px_rgba(16,185,129,0.35)] hover:-translate-y-0.5 transition">
                                Login untuk Lapor
                            </a>
                        @endauth
                        <a href="{{ route('portal.ticket.status.form') }}" class="inline-flex items-center justify-center px-7 py-3.5 text-sm font-bold text-emerald-700 dark:text-emerald-300 border-2 border-emerald-500/50 rounded-xl hover:bg-emerald-500/10 transition">
                            Lacak Laporan
                        </a>
                    </div>
                </div>

                <div class="glass-panel rounded-2xl p-6 sm:p-8 shadow-xl dark:shadow-2xl">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 flex items-center justify-center bg-white dark:bg-white/5 rounded-xl border border-slate-200 dark:border-slate-700">
                            <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Kalselprov" class="w-10 h-10 object-contain">
                        </div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.25em] text-emerald-600 dark:text-emerald-400">Secure Service</p>
                            <h2 class="text-xl font-black text-slate-900 dark:text-white">Portal Insiden Siber</h2>
                        </div>
                    </div>
                    <div class="space-y-4 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                        <p class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-500 shrink-0"></span>Pelaporan dibuat terstruktur melalui tiket, kategori, prioritas, lampiran, dan riwayat percakapan.</p>
                        <p class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-500 shrink-0"></span>Tim admin dan agent dapat memantau, menugaskan, membalas, dan menyelesaikan tiket dari panel internal.</p>
                        <p class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-amber-500 shrink-0"></span>Lapisan keamanan dilengkapi MFA, verifikasi perangkat, audit event, enkripsi lampiran, dan kontrol akses kontekstual.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 sm:pb-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                <div class="glass-panel rounded-2xl p-6">
                    <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Manajemen Tiket</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">Laporan masuk diproses sebagai tiket agar status, prioritas, penugasan, dan riwayat penanganan dapat ditelusuri.</p>
                </div>

                <div class="glass-panel rounded-2xl p-6">
                    <div class="w-11 h-11 rounded-xl bg-blue-500/10 border border-blue-500/25 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-6a2 2 0 012-2h10M9 17H5a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v4M9 17l4 4m0 0l4-4m-4 4V9" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Panel Operasional</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">Admin mengelola master data dan penugasan, sementara agent menangani respons dan penyelesaian laporan.</p>
                </div>

                <div class="glass-panel rounded-2xl p-6">
                    <div class="w-11 h-11 rounded-xl bg-amber-500/10 border border-amber-500/25 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Zero Trust</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">Akses dilindungi dengan prinsip verifikasi berlapis melalui MFA, perangkat tepercaya, konteks akses, dan audit keamanan.</p>
                </div>
            </div>
        </section>

        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 sm:pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-[0.82fr_1.18fr] gap-6 lg:gap-8 items-stretch">
                <div class="glass-panel rounded-2xl p-6 sm:p-8">
                    <p class="text-xs font-black uppercase tracking-[0.25em] text-emerald-600 dark:text-emerald-400">Creator</p>
                    <h2 class="mt-3 text-2xl sm:text-3xl font-black text-slate-950 dark:text-white">Pengembang Sistem</h2>
                    <div class="mt-8 divide-y divide-slate-200 dark:divide-slate-700">
                        <div class="py-4">
                            <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-500 font-bold">Mahasiswa Pengembang</p>
                            <div class="mt-3 grid gap-3">
                                <div>
                                    <p class="text-lg font-extrabold text-slate-900 dark:text-white">Muhammad Abrar Ridhani</p>
                                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">C030323137</p>
                                </div>
                                <div>
                                    <p class="text-lg font-extrabold text-slate-900 dark:text-white">Ahmad Rona Fatahilah</p>
                                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">C030323074</p>
                                </div>
                            </div>
                        </div>
                        <div class="py-4">
                            <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-500 font-bold">Dosen Pembimbing</p>
                            <div class="mt-3 grid gap-3">
                                <div>
                                    <p class="text-sm uppercase tracking-widest text-slate-500 dark:text-slate-500 font-bold">Pembimbing 1</p>
                                    <p class="mt-1 text-base font-extrabold text-slate-900 dark:text-white">Rahimi Fitri, S.Kom, M.Kom.</p>
                                </div>
                                <div>
                                    <p class="text-sm uppercase tracking-widest text-slate-500 dark:text-slate-500 font-bold">Pembimbing 2</p>
                                    <p class="mt-1 text-base font-extrabold text-slate-900 dark:text-white">Drs. Koes Wiyatmoko, M.Kom.</p>
                                </div>
                            </div>
                        </div>
                        <div class="py-4">
                            <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-500 font-bold">Konteks</p>
                            <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-slate-300">Tugas Akhir - Integrasi Keamanan Zero Trust pada OS-Tiket</p>
                        </div>
                    </div>
                </div>

                <div class="glass-panel rounded-2xl p-6 sm:p-8">
                    <p class="text-xs font-black uppercase tracking-[0.25em] text-blue-600 dark:text-blue-400">Alur Sistem</p>
                    <h2 class="mt-3 text-2xl sm:text-3xl font-black text-slate-950 dark:text-white">Dari Laporan ke Tindak Lanjut</h2>
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-950/40 p-5">
                            <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400">01</span>
                            <h3 class="mt-3 font-extrabold text-slate-900 dark:text-white">Pelapor membuat tiket</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">Insiden dicatat dengan detail masalah, kategori, dan lampiran pendukung.</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-950/40 p-5">
                            <span class="text-3xl font-black text-blue-600 dark:text-blue-400">02</span>
                            <h3 class="mt-3 font-extrabold text-slate-900 dark:text-white">Admin meninjau</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">Laporan diprioritaskan dan diarahkan ke agent atau tim yang sesuai.</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-950/40 p-5">
                            <span class="text-3xl font-black text-cyan-600 dark:text-cyan-400">03</span>
                            <h3 class="mt-3 font-extrabold text-slate-900 dark:text-white">Agent menangani</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">Komunikasi, catatan teknis, dan perubahan status disimpan dalam riwayat tiket.</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-950/40 p-5">
                            <span class="text-3xl font-black text-amber-600 dark:text-amber-400">04</span>
                            <h3 class="mt-3 font-extrabold text-slate-900 dark:text-white">Akses diaudit</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">Aktivitas penting dipantau untuk menjaga integritas dan kerahasiaan data.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="relative z-10 bg-slate-50 dark:bg-[#0b1120] text-slate-500 dark:text-slate-400 py-10 border-t border-slate-200 dark:border-slate-800 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row gap-6 md:items-center md:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex items-center justify-center bg-white dark:bg-white/5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">
                    <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Kalselprov" class="w-8 h-8 object-contain">
                </div>
                <div>
                    <p class="font-bold text-slate-900 dark:text-slate-200">CSIRT <span class="text-emerald-600 dark:text-emerald-500">KALSEL</span></p>
                    <p class="text-xs">Zero Trust Network Access Enabled</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-4 text-sm font-semibold">
                <a href="{{ route('welcome') }}" class="hover:text-emerald-500 transition-colors">Beranda</a>
                <a href="{{ route('portal.ticket.status.form') }}" class="hover:text-emerald-500 transition-colors">Lacak Laporan</a>
                <a href="{{ route('login') }}" class="hover:text-emerald-500 transition-colors">Login</a>
            </div>
        </div>
    </footer>

    @include('components.chatbot-widget')
</body>

</html>
