<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengaduan Insiden Siber - CSIRT Kalselprov</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .cyber-pattern {
            background-image:
                linear-gradient(45deg, rgba(59, 130, 246, 0.1) 25%, transparent 25%),
                linear-gradient(-45deg, rgba(59, 130, 246, 0.1) 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, rgba(59, 130, 246, 0.1) 75%),
                linear-gradient(-45deg, transparent 75%, rgba(59, 130, 246, 0.1) 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }
    </style>
</head>

<body class="antialiased bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-md shadow-md sticky top-0 z-50 border-b border-blue-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 flex items-center justify-center shadow-lg">
                        <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Kalselprov"
                            class="w-12 h-12 object-contain">
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900">CSIRT Kalselprov</div>
                        <div class="text-xs text-gray-600">Computer Security Incident Response Team</div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        @can('admin.panel')
                            <a href="{{ route('dashboard') }}"
                                class="text-gray-700 hover:text-blue-600 font-medium transition">Dashboard</a>
                        @endcan
                        <a href="{{ route('profile.edit') }}"
                            class="text-gray-700 hover:text-blue-600 font-medium transition">Profile</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="text-gray-700 hover:text-blue-600 font-medium transition">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium transition">Log
                            in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-4 py-2 rounded-lg font-medium hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Success/Status Messages -->
    @if(session('status'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-green-800 font-medium">{{ session('status') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Hero Section -->
    <div class="relative overflow-hidden cyber-pattern">
        <div class="absolute inset-0 bg-gradient-to-b from-blue-600/10 to-transparent"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl shadow-2xl mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold mb-6">
                    <span
                        class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 bg-clip-text text-transparent">
                        Pengaduan Insiden Siber
                    </span>
                </h1>
                <p class="text-xl text-gray-700 mb-4 max-w-3xl mx-auto font-medium">
                    Portal resmi untuk melaporkan insiden keamanan siber di lingkungan Pemerintah Provinsi Kalimantan
                    Selatan
                </p>
                <p class="text-lg text-gray-600 mb-12 max-w-2xl mx-auto">
                    Lapor insiden keamanan siber Anda dan dapatkan respons cepat dari tim CSIRT Kalselprov
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                    @auth
                        <a href="{{ route('portal.ticket.create') }}"
                            class="group relative bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-8 py-4 rounded-xl font-semibold text-lg shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1 flex items-center space-x-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Laporkan Insiden Siber</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="group relative bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-8 py-4 rounded-xl font-semibold text-lg shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1 flex items-center space-x-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span>Login untuk Melaporkan</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endauth
                    <a href="{{ route('portal.ticket.status.form') }}"
                        class="bg-white text-blue-600 px-8 py-4 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 border-2 border-blue-200 hover:border-blue-300 flex items-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span>Cek Status Laporan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 border border-white/20">
                <div class="text-center text-white">
                    <h2 class="text-2xl font-bold mb-4">Penting: Kapan Harus Melaporkan?</h2>
                    <p class="text-lg text-blue-100 mb-6 max-w-3xl mx-auto">
                        Segera laporkan jika Anda mengalami atau menemukan insiden keamanan siber seperti:
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                        <div class="bg-white/10 rounded-lg p-4 border border-white/20">
                            <div class="text-3xl mb-2">🔐</div>
                            <div class="font-semibold mb-2">Serangan Siber</div>
                            <div class="text-sm text-blue-100">Malware, Ransomware, Phishing, atau serangan lainnya
                            </div>
                        </div>
                        <div class="bg-white/10 rounded-lg p-4 border border-white/20">
                            <div class="text-3xl mb-2">⚠️</div>
                            <div class="font-semibold mb-2">Kebocoran Data</div>
                            <div class="text-sm text-blue-100">Kebocoran informasi atau akses tidak sah ke sistem</div>
                        </div>
                        <div class="bg-white/10 rounded-lg p-4 border border-white/20">
                            <div class="text-3xl mb-2">🚨</div>
                            <div class="font-semibold mb-2">Vulnerability</div>
                            <div class="text-sm text-blue-100">Temuan kerentanan keamanan pada sistem atau aplikasi
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Mengapa Melaporkan ke CSIRT?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Tim ahli kami siap membantu menangani dan menganalisis insiden
                keamanan siber</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div
                class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 border border-gray-100">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Respon Cepat</h3>
                <p class="text-gray-600">Tim CSIRT akan merespons laporan Anda dalam waktu singkat untuk mencegah
                    kerugian lebih lanjut.</p>
            </div>

            <!-- Feature 2 -->
            <div
                class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 border border-gray-100">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Analisis Profesional</h3>
                <p class="text-gray-600">Tim ahli akan menganalisis insiden secara mendalam dan memberikan rekomendasi
                    penanganan.</p>
            </div>

            <!-- Feature 3 -->
            <div
                class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 border border-gray-100">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-slate-500 to-slate-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Kerahasiaan Terjamin</h3>
                <p class="text-gray-600">Laporan Anda dijaga kerahasiaannya dan hanya ditangani oleh tim CSIRT yang
                    berwenang.</p>
            </div>

            <!-- Feature 4 -->
            <div
                class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 border border-gray-100">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Tracking Real-time</h3>
                <p class="text-gray-600">Pantau status laporan Anda secara real-time melalui portal yang aman dan
                    terenkripsi.</p>
            </div>

            <!-- Feature 5 -->
            <div
                class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 border border-gray-100">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">SLA Terjamin</h3>
                <p class="text-gray-600">Service Level Agreement yang jelas untuk memastikan respons dan penanganan
                    tepat waktu.</p>
            </div>

            <!-- Feature 6 -->
            <div
                class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2 border border-gray-100">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Koordinasi Terintegrasi</h3>
                <p class="text-gray-600">Koordinasi dengan instansi terkait untuk penanganan insiden yang komprehensif.
                </p>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="text-white">
                    <div class="text-4xl md:text-5xl font-bold mb-2">24/7</div>
                    <div class="text-blue-100">Layanan Tersedia</div>
                </div>
                <div class="text-white">
                    <div class="text-4xl md:text-5xl font-bold mb-2">
                        < 1 Jam</div>
                            <div class="text-blue-100">Waktu Respon Rata-rata</div>
                    </div>
                    <div class="text-white">
                        <div class="text-4xl md:text-5xl font-bold mb-2">100%</div>
                        <div class="text-blue-100">Kerahasiaan Terjamin</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-slate-900 text-gray-300 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-12 h-12 flex items-center justify-center">
                                <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Kalselprov"
                                    class="w-12 h-12 object-contain">
                            </div>
                            <div>
                                <div class="text-white font-bold">CSIRT Kalselprov</div>
                                <div class="text-xs text-gray-400">Computer Security Incident Response Team</div>
                            </div>
                        </div>
                        <p class="text-gray-400 text-sm">Tim respons insiden keamanan siber untuk Pemerintah Provinsi
                            Kalimantan Selatan.</p>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('portal.ticket.create') }}"
                                    class="hover:text-white transition text-sm">Laporkan Insiden</a></li>
                            <li><a href="{{ route('portal.ticket.status.form') }}"
                                    class="hover:text-white transition text-sm">Cek Status Laporan</a></li>
                            @auth
                                @can('admin.panel')
                                    <li><a href="{{ route('dashboard') }}"
                                            class="hover:text-white transition text-sm">Dashboard</a></li>
                                @endcan
                                <li><a href="{{ route('profile.edit') }}"
                                        class="hover:text-white transition text-sm">Profile</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="hover:text-white transition text-sm">Login</a>
                                </li>
                                <li><a href="{{ route('register') }}"
                                        class="hover:text-white transition text-sm">Register</a></li>
                            @endauth
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-4">Kontak</h4>
                        <ul class="space-y-2 text-gray-400 text-sm">
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>csirt@kalselprov.go.id</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>+62 XXX XXX XXXX</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Dinas Komunikasi dan Informatika Prov. Kalsel</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-slate-800 mt-8 pt-8 text-center text-gray-400 text-sm">
                    <p>&copy; {{ date('Y') }} CSIRT Kalselprov - Computer Security Incident Response Team. All rights
                        reserved.</p>
                    <p class="mt-2 text-xs">Pemerintah Provinsi Kalimantan Selatan</p>
                </div>
            </div>
        </footer>

    <!-- Chatbot Widget -->
    @include('components.chatbot-widget')
</body>

</html>