<x-agent-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Dashboard CSIRT</h2>
                <p class="text-sm text-gray-600 mt-1">Ringkasan statistik laporan insiden siber hari ini</p>
            </div>
        </div>
    </x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Open Tickets -->
        <div
            class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 overflow-hidden border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Laporan Terbuka</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['open'] }}</p>
                    </div>
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('agent.tickets.index', ['status' => 'open']) }}"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center">
                        Lihat semua
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Answered Tickets -->
        <div
            class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 overflow-hidden border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Menunggu Pelapor</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['answered'] }}</p>
                    </div>
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('agent.tickets.index', ['status' => 'answered']) }}"
                        class="text-sm text-yellow-600 hover:text-yellow-700 font-medium flex items-center">
                        Lihat semua
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Overdue Tickets -->
        <div
            class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 overflow-hidden border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Terlambat (Overdue)</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['overdue'] }}</p>
                    </div>
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('agent.tickets.index') }}"
                        class="text-sm text-red-600 hover:text-red-700 font-medium flex items-center">
                        Perlu perhatian
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Closed Tickets -->
        <div
            class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 overflow-hidden border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Laporan Selesai</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['closed'] }}</p>
                    </div>
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('agent.tickets.index', ['status' => 'closed']) }}"
                        class="text-sm text-green-600 hover:text-green-700 font-medium flex items-center">
                        Lihat semua
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Aksi Cepat
        </h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('agent.tickets.index', ['status' => 'open']) }}"
                class="group flex items-center p-4 border-2 border-gray-200 rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-all transform hover:-translate-y-1">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-600">Laporan Terbuka</p>
                    <p class="text-xs text-gray-500">{{ $stats['open'] }} laporan perlu ditangani</p>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 group-hover:translate-x-1 transition-transform"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="{{ route('agent.tickets.index', ['status' => 'answered']) }}"
                class="group flex items-center p-4 border-2 border-gray-200 rounded-xl hover:border-yellow-500 hover:bg-yellow-50 transition-all transform hover:-translate-y-1">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-gray-900 group-hover:text-yellow-600">Menunggu Pelapor</p>
                    <p class="text-xs text-gray-500">{{ $stats['answered'] }} laporan menunggu</p>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-600 group-hover:translate-x-1 transition-transform"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="{{ route('agent.tickets.index') }}"
                class="group flex items-center p-4 border-2 border-gray-200 rounded-xl hover:border-purple-500 hover:bg-purple-50 transition-all transform hover:-translate-y-1">
                <div class="flex-shrink-0">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600">Semua Laporan</p>
                    <p class="text-xs text-gray-500">Lihat semua laporan insiden</p>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600 group-hover:translate-x-1 transition-transform"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</x-agent-layout>