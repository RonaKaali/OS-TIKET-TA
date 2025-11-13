<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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

<body class="font-sans text-gray-900 antialiased">
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 cyber-grid">
        <!-- Logo/Brand -->
        <div class="mb-8 sm:mb-12">
            <a href="/" class="flex items-center space-x-3 group">
                <div
                    class="w-16 h-16 flex items-center justify-center shadow-xl group-hover:shadow-2xl transition-all transform group-hover:scale-105">
                    <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Kalselprov"
                        class="w-16 h-16 object-contain">
                </div>
                <div class="text-left">
                    <div
                        class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-700 bg-clip-text text-transparent">
                        CSIRT Kalselprov</div>
                    <div class="text-xs text-gray-600">Computer Security Incident Response Team</div>
                </div>
            </a>
        </div>

        <!-- Form Card -->
        <div
            class="w-full sm:max-w-md mt-6 px-6 sm:px-8 py-8 bg-white/95 backdrop-blur-sm shadow-2xl overflow-hidden sm:rounded-2xl border border-gray-100">
            {{ $slot }}
        </div>

        <!-- Footer Link -->
        <div class="mt-6 text-center">
            <a href="{{ route('welcome') }}"
                class="inline-flex items-center space-x-2 px-6 py-3 bg-white border-2 border-gray-300 rounded-lg text-base font-semibold text-gray-700 hover:text-blue-600 hover:border-blue-500 hover:bg-blue-50 shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </div>
</body>

</html>