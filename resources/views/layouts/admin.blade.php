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
    @include('partials.zero-trust-meta')
    <title>Panel Admin CSIRT - {{ config('app.name', 'CSIRT Kalselprov') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        /* Global Admin Theme Overrides */
        main .bg-white { background-color: white !important; }
        .dark main .bg-white { background-color: rgba(15, 23, 42, 0.5) !important; backdrop-filter: blur(12px); border: 1px solid rgba(51, 65, 85, 0.5); }
        
        main .text-gray-900, main .text-gray-800 { color: #1e293b !important; }
        .dark main .text-gray-900, .dark main .text-gray-800 { color: #f8fafc !important; }
        
        main .text-gray-700, main .text-gray-600 { color: #475569 !important; }
        .dark main .text-gray-700, .dark main .text-gray-600 { color: #94a3b8 !important; }
        
        main .bg-gray-50 { background-color: #f8fafc !important; }
        .dark main .bg-gray-50 { background-color: rgba(2, 6, 23, 0.3) !important; }
        
        /* Premium Buttons */
        main .bg-indigo-600 { background: linear-gradient(to right, #059669, #2563eb) !important; border: none !important; font-weight: 800 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; font-size: 0.75rem !important; color: white !important; }
        main .bg-indigo-600:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }
        
        body { transition: background-color 0.3s ease, color 0.3s ease; }
    </style>
</head>

<body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-200 transition-colors">
    <div class="min-h-screen flex relative overflow-hidden">
        <!-- Ambient Background Lights -->
        <div class="fixed top-[-10%] left-[-10%] w-[30%] h-[30%] rounded-full bg-emerald-900/10 blur-[100px] pointer-events-none"></div>
        <div class="fixed bottom-[-10%] right-[-10%] w-[30%] h-[30%] rounded-full bg-blue-900/10 blur-[100px] pointer-events-none"></div>

        <!-- Sidebar -->
        <aside class="hidden lg:flex lg:flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 backdrop-blur-xl relative z-20 transition-colors">
            <div class="flex flex-col w-64">
                <div class="flex flex-col flex-grow pt-8 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-6 mb-8">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-emerald-500/20 rounded-xl flex items-center justify-center p-2 shadow-sm dark:shadow-[0_0_15px_rgba(16,185,129,0.1)] transition-all">
                                <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Kalselprov"
                                    class="w-full h-full object-contain">
                            </div>
                            <div>
                                <div class="text-sm font-black text-slate-900 dark:text-white tracking-wider uppercase transition-colors">CSIRT <span class="text-emerald-600 dark:text-emerald-400">ADMIN</span></div>
                                <div class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.2em] transition-colors">Kalselprov</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col">
                        <nav class="flex-1 px-3 space-y-1">
                            <a href="{{ route('admin.index') }}"
                                class="flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.index') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                Dasbor
                            </a>
                            <a href="{{ route('admin.reports') }}"
                                class="flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.reports') ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m32-2v-2a4 4 0 00-4-4h-2a4 4 0 00-4 4v2m-9-4h.01M12 12h.01M12 15h.01M12 18h.01M12 21h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                Dasbor Laporan Akhir
                            </a>
                            <a href="{{ route('agent.tickets.index') }}"
                                class="flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 {{ request()->routeIs('agent.tickets.*') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                                Tiket Laporan
                            </a>
                            
                            <div class="mt-8 mb-2 px-4">
                                <p class="text-[10px] font-black text-slate-600 uppercase tracking-[0.3em]">Sistem Manajemen</p>
                            </div>
                            
                            <div class="space-y-1">
                                @php
                                    $adminLinks = [
                                        ['route' => 'admin.security.dashboard', 'label' => 'Zero Trust Security', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                                        ['route' => 'admin.departments.index', 'label' => 'Departemen', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                                        ['route' => 'admin.help-topics.index', 'label' => 'Topik Bantuan', 'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                        ['route' => 'admin.statuses.index', 'label' => 'Status', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                        ['route' => 'admin.priorities.index', 'label' => 'Prioritas', 'icon' => 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9'],
                                        ['route' => 'admin.sla.index', 'label' => 'Rencana SLA', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                        ['route' => 'admin.teams.index', 'label' => 'Tim', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                                        ['route' => 'admin.canned.index', 'label' => 'Canned Response', 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
                                        ['route' => 'admin.organizations.index', 'label' => 'Organisasi', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                                        ['route' => 'admin.users.index', 'label' => 'Pengguna', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                                    ];
                                @endphp
                                @foreach($adminLinks as $link)
                                <a href="{{ route($link['route']) }}"
                                    class="flex items-center px-4 py-2.5 text-xs font-bold rounded-xl transition-all duration-200 {{ request()->routeIs($link['route']) ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' : 'text-slate-500 dark:text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}" /></svg>
                                    {{ $link['label'] }}
                                </a>
                                @endforeach
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden relative z-10">
            <!-- Top Navigation -->
            <header class="bg-white/50 dark:bg-slate-900/50 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 transition-colors">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div class="ml-4 lg:ml-0 flex items-center px-4 py-2 bg-emerald-500/10 dark:bg-slate-800/50 border border-emerald-500/20 dark:border-slate-700 rounded-full transition-colors">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse mr-2 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                                <span class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest transition-colors">Zero Trust Verified</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <x-theme-toggle />
                            
                            <div class="hidden md:flex items-center px-3 py-1 bg-emerald-50 dark:bg-slate-800 border border-emerald-200 dark:border-emerald-500/30 rounded-full transition-colors">
                                <span class="text-[10px] font-bold text-emerald-800 dark:text-emerald-300 mr-2 uppercase tracking-wider"><i class="fa-solid fa-shield-halved mr-1"></i> Keamanan:</span>
                                <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase">Tingkat Tinggi</span>
                            </div>
                            
                            <div class="h-6 w-[1px] bg-slate-800 mx-2"></div>

                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-slate-200 dark:border-slate-700 text-sm font-bold rounded-xl text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white hover:border-emerald-500/50 transition-all shadow-sm">
                                        <div class="w-6 h-6 bg-emerald-500/20 rounded-lg flex items-center justify-center mr-2">
                                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                        </div>
                                        {{ Auth::user()->name }}
                                        <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-xl">
                                        <x-dropdown-link :href="route('profile.edit')" class="text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Profil</x-dropdown-link>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();"
                                                class="text-red-500 dark:text-red-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                                Keluar
                                            </x-dropdown-link>
                                        </form>
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 dark:bg-slate-950/50 p-4 sm:p-6 lg:p-8 transition-colors">
                @if(session('ok'))
                    <div class="mb-6 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-6 py-4 rounded-2xl flex items-center shadow-[0_0_15px_rgba(16,185,129,0.1)]" role="alert">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="text-sm font-bold">{{ session('ok') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 px-6 py-4 rounded-2xl shadow-[0_0_15px_rgba(239,68,68,0.1)]" role="alert">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-sm font-bold uppercase tracking-wider">Kesalahan Validasi</span>
                        </div>
                        <ul class="list-disc list-inside text-xs font-medium space-y-1 ml-8">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>

</html>