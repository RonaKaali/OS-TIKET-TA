<x-guest-layout>
    <div class="text-center space-y-6 py-4">
        <!-- Large Shield Alert Icon -->
        <div class="relative mx-auto w-24 h-24 mb-6">
            <div class="absolute inset-0 bg-red-500/20 rounded-full blur-2xl animate-pulse"></div>
            <div class="relative w-full h-full flex items-center justify-center bg-gradient-to-br from-red-600 to-red-800 rounded-full shadow-lg shadow-red-500/30">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>

        <!-- Title -->
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Akses Ditolak
        </h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm font-bold max-w-sm mx-auto">
            Koneksi VPN atau proxy terdeteksi. Untuk keamanan sistem, akses dari jaringan tidak dikenal tidak diizinkan.
        </p>

        <!-- IP & Provider Details -->
        <!-- Notifikasi -->
        @if(session('vpn_confidence'))
        <div class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 rounded-2xl p-5 space-y-3 text-left">
        @else
        <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 space-y-3 text-left">
        @endif
            <!-- IP Address -->
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">IP Address</span>
                <span class="text-sm font-mono font-bold text-slate-900 dark:text-white bg-white dark:bg-slate-900/50 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700">
                    {{ session('vpn_ip') ?? request()->ip() }}
                </span>
            </div>

            <!-- Provider -->
            @if(session('vpn_provider'))
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Provider</span>
                <span class="text-sm font-bold text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30 px-3 py-1.5 rounded-lg border border-red-200 dark:border-red-800">
                    {{ session('vpn_provider') }}
                </span>
            </div>
            @endif

            <!-- Confidence Score -->
            @if(session('vpn_confidence'))
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Confidence</span>
                <div class="flex items-center gap-2">
                    <div class="w-20 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-red-500 rounded-full" style="width: {{ session('vpn_confidence') }}%"></div>
                    </div>
                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ session('vpn_confidence') }}%</span>
                </div>
            </div>
            @endif
        </div>

        <!-- Anomaly logged info -->
        <div class="flex items-center justify-center gap-2 text-xs text-slate-400 dark:text-slate-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Anomali ini telah dicatat di sistem monitoring keamanan.</span>
        </div>

        <!-- Return Button -->
        <div class="pt-4">
            <a href="{{ route('login') }}"
                class="inline-flex items-center justify-center w-full px-6 py-4 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-sm font-black rounded-2xl uppercase tracking-[0.15em] transition-all transform hover:-translate-y-0.5 active:scale-[0.98] shadow-lg border border-slate-600">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Halaman Login
            </a>
        </div>
    </div>
</x-guest-layout>
