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
    <title>Panel Agen CSIRT - {{ config('app.name', 'CSIRT Kalselprov') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>
</head>

<body class="font-sans antialiased bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-200 transition-colors">
    <div class="min-h-screen relative overflow-hidden" x-data="{ open: false, sidebarOpen: false }">
        <!-- Ambient Background Lights -->
        <div class="fixed top-[-10%] right-[-5%] w-[40%] h-[40%] rounded-full bg-blue-900/10 blur-[120px] pointer-events-none"></div>
        <div class="fixed bottom-[-5%] left-[-5%] w-[30%] h-[30%] rounded-full bg-emerald-900/10 blur-[100px] pointer-events-none"></div>

        <!-- Modern Navigation -->
        <nav class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 sticky top-0 z-50 transition-colors">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <a href="{{ route('agent.dashboard') }}" class="flex items-center space-x-4 group">
                            <div class="w-12 h-12 bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-emerald-500/20 rounded-2xl flex items-center justify-center p-2 shadow-sm dark:shadow-[0_0_20px_rgba(16,185,129,0.1)] group-hover:shadow-md dark:group-hover:shadow-[0_0_30px_rgba(16,185,129,0.2)] transition-all duration-300">
                                <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Kalselprov"
                                    class="w-full h-full object-contain filter dark:drop-shadow-[0_0_8px_rgba(255,255,255,0.5)]">
                            </div>
                            <div class="hidden md:block">
                                <div class="text-sm font-black text-slate-900 dark:text-white tracking-wider uppercase leading-none mb-1 transition-colors">CSIRT <span class="text-emerald-600 dark:text-emerald-400">Kalselprov</span></div>
                                <div class="text-[10px] text-emerald-600 dark:text-emerald-500 font-bold uppercase tracking-[0.2em] transition-colors">Agen & Analis Portal</div>
                            </div>
                        </a>
                        
                        <div class="hidden sm:ml-12 sm:flex sm:space-x-2">
                            <a href="{{ route('agent.dashboard') }}" 
                                class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 {{ request()->routeIs('agent.dashboard') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                Dasbor
                            </a>
                            <a href="{{ route('agent.tickets.index') }}"
                                class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 {{ request()->routeIs('agent.tickets.*') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                Tiket Laporan
                            </a>
                            @role('Super Admin')
                            <a href="{{ route('admin.index') }}"
                                class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-200 text-blue-600 dark:text-blue-400 hover:bg-blue-500/10 border border-blue-500/20">
                                Panel Admin
                            </a>
                            @endrole
                        </div>
                    </div>
                    
                    <div class="hidden sm:flex sm:items-center sm:space-x-6">
                        <!-- Security Context Badge -->
                        <div class="flex items-center px-4 py-2 bg-emerald-500/10 dark:bg-slate-800/50 border border-emerald-500/20 dark:border-slate-700 rounded-full transition-colors">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse mr-2 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                            <span class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest transition-colors">Zero Trust Verified</span>
                        </div>

                        <x-theme-toggle />

                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center space-x-3 p-1.5 pr-4 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all border border-slate-200 dark:border-transparent hover:border-slate-300 dark:hover:border-slate-700 group shadow-sm">
                                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-black shadow-lg transform group-hover:scale-105 transition-transform">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <div class="text-left">
                                        <div class="text-xs font-black text-slate-900 dark:text-white leading-none mb-1 transition-colors">{{ Auth::user()->name }}</div>
                                        <div class="text-[9px] text-slate-500 font-bold uppercase tracking-tighter transition-colors">{{ Auth::user()->roles->first()->name ?? 'Operator' }}</div>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-600 group-hover:text-slate-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-2xl min-w-[200px] transition-all">
                                    <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50">
                                        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1 transition-colors">Signed in as</p>
                                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate transition-colors">{{ Auth::user()->email }}</p>
                                    </div>
                                    <x-dropdown-link :href="route('profile.edit')" class="px-4 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors flex items-center">
                                        <svg class="w-4 h-4 mr-3 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        Profil Saya
                                    </x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                            class="px-4 py-3 text-xs font-bold text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors flex items-center border-t border-slate-200 dark:border-slate-800">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                            Keluar Sistem
                                        </x-dropdown-link>
                                    </form>
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    
                    <div class="flex items-center sm:hidden">
                        <button @click="open = !open" class="p-3 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': !open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !open, 'inline-flex': open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 transition-colors">
                <div class="pt-4 pb-6 space-y-2 px-4">
                    <a href="{{ route('agent.dashboard') }}" class="block px-4 py-3 rounded-xl text-sm font-bold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition-colors">Dasbor</a>
                    <a href="{{ route('agent.tickets.index') }}" class="block px-4 py-3 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 transition-colors">Tiket Laporan</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 rounded-xl text-sm font-bold text-red-500 dark:text-red-400 transition-colors">Keluar Sistem</button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Header -->
        @isset($header)
            <header class="bg-white/80 dark:bg-slate-950/50 backdrop-blur-sm border-b border-slate-200 dark:border-slate-900/50 transition-colors">
                <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Main Content Area -->
        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 relative z-10">
            @if(session('ok'))
                <div class="mb-8 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 px-6 py-4 rounded-2xl flex items-center shadow-[0_0_20px_rgba(16,185,129,0.1)] animate-fadeIn" role="alert">
                    <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-sm font-black uppercase tracking-widest">{{ session('ok') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-8 bg-red-500/10 border border-red-500/30 text-red-600 dark:text-red-400 px-6 py-5 rounded-2xl shadow-[0_0_20px_rgba(239,68,68,0.1)]" role="alert">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 mr-4 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <div class="flex-1">
                            <p class="text-xs font-black uppercase tracking-[0.2em] mb-2">Peringatan Sistem / System Warnings:</p>
                            <ul class="list-disc list-inside text-xs font-bold space-y-1 ml-2 opacity-80">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{ $slot }}
        </main>
        
        <!-- Decoration Elements -->
        <div class="fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 via-blue-500 to-indigo-500 z-[60]"></div>
        <div class="fixed bottom-0 right-0 p-4 z-0 opacity-5 pointer-events-none">
            <div class="text-[120px] font-black select-none tracking-tighter">SECURED</div>
        </div>
    </div>
</body>

</html>