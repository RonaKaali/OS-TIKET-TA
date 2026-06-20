@props(['header'])

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
    <title>Panel Admin CSIRT - {{ config('app.name', 'CSIRT Kalselprov') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

<body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 transition-colors duration-300">
    <div class="min-h-screen flex relative overflow-hidden">
        <!-- Ambient Background Lights -->
        <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-900/10 blur-[120px] pointer-events-none"></div>
        <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-900/10 blur-[120px] pointer-events-none"></div>

        <!-- Sidebar (only for Admin roles) -->
        @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin']))
        <aside class="hidden lg:flex lg:flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 backdrop-blur-xl relative z-20 transition-colors">
            <div class="flex flex-col w-64">
                <div class="flex flex-col flex-grow pt-8 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-6 mb-10">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-blue-500/20 rounded-xl flex items-center justify-center p-2 shadow-sm dark:shadow-[0_0_15px_rgba(59,130,246,0.1)] transition-all">
                                <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Kalselprov"
                                    class="w-full h-full object-contain">
                            </div>
                            <div>
                                <div class="text-sm font-black text-slate-900 dark:text-white tracking-wider uppercase transition-colors">CSIRT <span class="text-blue-600 dark:text-blue-400">ADMIN</span></div>
                                <div class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.2em] transition-colors">Kalselprov</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex-1 flex flex-col">
                        <nav class="flex-1 px-3 space-y-1">
                            <a href="{{ route('admin.index') }}"
                                class="flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 {{ request()->routeIs('admin.index') ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                Dashboard Laporan Akhir
                            </a>
                            <a href="{{ route('agent.dashboard') }}"
                                class="flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 {{ request()->routeIs('agent.dashboard') ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                Dashboard Utama
                            </a>
                            <a href="{{ route('agent.tickets.index') }}"
                                class="flex items-center px-4 py-3 text-sm font-bold rounded-xl transition-all duration-200 {{ request()->routeIs('agent.tickets.*') ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                                Tiket Laporan
                            </a>
                            
                            <div class="mt-8 mb-2 px-4">
                                <p class="text-[10px] font-black text-slate-600 uppercase tracking-[0.3em]">Administrator</p>
                            </div>
                            
                            <div class="space-y-1">
                                @php
                                    $adminLinks = [
                                        ['route' => 'admin.departments.index', 'label' => 'Departemen', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                                        ['route' => 'admin.help-topics.index', 'label' => 'Topik Bantuan', 'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
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
        @endif

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden relative z-10">
            <!-- Top Navigation -->
            <header class="bg-white/50 dark:bg-slate-900/50 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 transition-colors">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            @isset($header)
                                {{ $header }}
                            @else
                                <h1 class="text-xl font-black text-slate-900 dark:text-white tracking-tight uppercase transition-colors">Panel <span class="text-blue-600 dark:text-blue-500">Admin</span></h1>
                            @endisset
                        </div>
                        <div class="flex items-center space-x-4">
                            <x-theme-toggle />
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-4 py-2 border border-slate-200 dark:border-slate-700 text-sm font-bold rounded-xl text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white hover:border-blue-500/50 transition-all shadow-sm">
                                        {{ Auth::user()->name }}
                                        <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-xl transition-all">
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
                <div class="max-w-7xl mx-auto">
                    @if(session('ok'))
                        <div class="mb-6 bg-blue-500/10 border border-blue-500/30 text-blue-400 px-6 py-4 rounded-2xl flex items-center shadow-[0_0_15px_rgba(59,130,246,0.1)]" role="alert">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-sm font-bold">{{ session('ok') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 px-6 py-4 rounded-2xl shadow-[0_0_15px_rgba(239,68,68,0.1)]" role="alert">
                            <ul class="list-disc list-inside text-xs font-medium space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>

</html>