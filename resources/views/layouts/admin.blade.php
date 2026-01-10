<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel Admin CSIRT - {{ config('app.name', 'CSIRT Kalselprov') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col flex-grow bg-indigo-600 pt-5 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center p-1">
                                <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Kalselprov"
                                    class="w-full h-full object-contain">
                            </div>
                            <div>
                                <div class="text-sm font-bold text-white">CSIRT Kalselprov</div>
                                <div class="text-xs text-indigo-200">Panel Admin</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 flex-1 flex flex-col">
                        <nav class="flex-1 px-2 space-y-1">
                            <a href="{{ route('agent.dashboard') }}"
                                class="text-white hover:bg-indigo-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Dasbor
                            </a>
                            <a href="{{ route('agent.tickets.index') }}"
                                class="text-white hover:bg-indigo-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                Tiket
                            </a>
                            <div class="pt-4 border-t border-indigo-800">
                                <p class="px-2 text-xs font-semibold text-indigo-300 uppercase tracking-wider">Admin</p>
                                <a href="{{ route('admin.departments.index') }}"
                                    class="mt-1 text-white hover:bg-indigo-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    Departemen
                                </a>
                                <a href="{{ route('admin.help-topics.index') }}"
                                    class="text-white hover:bg-indigo-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    Topik Bantuan
                                </a>
                                <a href="{{ route('admin.statuses.index') }}"
                                    class="text-white hover:bg-indigo-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    Status
                                </a>
                                <a href="{{ route('admin.priorities.index') }}"
                                    class="text-white hover:bg-indigo-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    Prioritas
                                </a>
                                <a href="{{ route('admin.sla.index') }}"
                                    class="text-white hover:bg-indigo-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    Rencana SLA
                                </a>
                                <a href="{{ route('admin.teams.index') }}"
                                    class="text-white hover:bg-indigo-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    Tim
                                </a>
                                <a href="{{ route('admin.canned.index') }}"
                                    class="text-white hover:bg-indigo-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    Tanggapan Cepat
                                </a>
                                <a href="{{ route('admin.organizations.index') }}"
                                    class="text-white hover:bg-indigo-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    Organisasi
                                </a>
                                <a href="{{ route('admin.users.index') }}"
                                    class="text-white hover:bg-indigo-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    Pengguna
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-md text-gray-400">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700">
                                        {{ Auth::user()->name }}
                                        <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                        Keluar
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 sm:p-6 lg:p-8">
                @if(session('ok'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                        role="alert">
                        <span class="block sm:inline">{{ session('ok') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>