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
    <title>{{ config('app.name', 'CSIRT Kalselprov') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .cyber-grid {
            background-image:
                linear-gradient(45deg, rgba(59, 130, 246, 0.05) 25%, transparent 25%),
                linear-gradient(-45deg, rgba(59, 130, 246, 0.05) 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, rgba(59, 130, 246, 0.05) 75%),
                linear-gradient(-45deg, transparent 75%, rgba(59, 130, 246, 0.05) 75%);
            background-size: 30px 30px;
            background-position: 0 0, 0 15px, 15px -15px, -15px 0px;
        }
    </style>
</head>

<body class="font-sans text-slate-800 dark:text-slate-200 antialiased selection:bg-emerald-500/30 transition-colors duration-300">
    <div class="fixed top-4 right-4 z-[100]">
        <x-theme-toggle />
    </div>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50 dark:bg-[#020617] relative overflow-hidden transition-colors duration-300">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 cyber-grid opacity-20"></div>
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s"></div>

        <!-- Logo/Brand -->
        <div class="mb-8 sm:mb-12 relative z-10">
            <a href="/" class="flex flex-col items-center space-y-4 group">
                <div class="relative">
                    <div class="absolute inset-0 bg-emerald-500/20 blur-xl rounded-full group-hover:bg-emerald-500/40 transition-all duration-500"></div>
                    <div class="relative w-20 h-20 flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl dark:shadow-2xl transition-all transform group-hover:scale-110 group-hover:border-emerald-500/50">
                        <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Kalselprov"
                            class="w-14 h-14 object-contain">
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-black tracking-tighter text-slate-900 dark:text-white transition-colors">
                        CSIRT <span class="text-emerald-600 dark:text-emerald-500">KALSEL</span>
                    </div>
                    <div class="text-[10px] font-bold uppercase tracking-[0.3em] text-slate-500">Secure Authentication Portal</div>
                </div>
            </a>
        </div>

        <!-- Form Card -->
        <div class="w-full sm:max-w-md mt-6 px-6 sm:px-10 py-10 bg-white dark:bg-slate-900/50 backdrop-blur-xl shadow-lg dark:shadow-[0_0_50px_rgba(0,0,0,0.3)] overflow-hidden sm:rounded-3xl border border-slate-200 dark:border-slate-800/50 relative z-10 transition-all">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-emerald-500/50 to-transparent"></div>
            {{ $slot }}
        </div>

        <!-- Footer Link -->
        <div class="mt-8 text-center relative z-10">
            <a href="{{ route('welcome') }}"
                class="inline-flex items-center space-x-2 px-6 py-3 text-sm font-bold text-slate-400 hover:text-emerald-400 transition-all duration-300 group">
                <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Pusat Komando</span>
            </a>
        </div>
    </div>
</body>

</html>