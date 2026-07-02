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

    <title>{{ config('app.name', 'CSIRT Kalselprov') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .cyber-bg {
            background-image: 
                radial-gradient(circle at 50% 0%, rgba(16, 185, 129, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(59, 130, 246, 0.05) 0%, transparent 50%);
        }
    </style>
</head>

<body class="font-sans antialiased cyber-bg bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300 transition-colors">
    <div class="min-h-screen">
        <div class="fixed top-4 right-4 z-[100]">
            <x-theme-toggle />
        </div>
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white/80 dark:bg-slate-900/80 shadow-sm dark:shadow-[0_4px_20px_rgba(0,0,0,0.3)] border-b border-slate-200 dark:border-slate-800 backdrop-blur-md relative z-10">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="relative z-0">
            {{ $slot }}
        </main>
    </div>
    
    <!-- Chatbot Widget -->
    @include('components.chatbot-widget')
</body>

</html>