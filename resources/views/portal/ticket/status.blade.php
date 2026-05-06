<x-guest-layout>
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl mb-6 relative group transition-all">
            <div class="absolute inset-0 bg-emerald-500/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight mb-2 uppercase transition-colors">Cek Status Laporan</h1>
        <p class="text-slate-600 dark:text-slate-500 font-medium transition-colors">Verifikasi progres penanganan insiden siber Anda secara real-time.</p>
    </div>

    <form method="POST" action="{{ route('portal.ticket.status.check') }}" class="space-y-8">
        @csrf

        @if($errors->has('not_found'))
            <div class="bg-rose-500/10 border-l-4 border-rose-500 p-5 rounded-r-xl">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-rose-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-sm text-rose-400 font-bold uppercase tracking-wide">{{ $errors->first('not_found') }}</p>
                </div>
            </div>
        @endif

        <div class="space-y-2">
            <label for="ticket_number" class="block text-xs font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">
                Nomor Laporan <span class="text-emerald-500">*</span>
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-600 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                    </svg>
                </div>
                <input type="text" name="ticket_number" id="ticket_number" value="{{ old('ticket_number') }}" required
                    placeholder="CSIRT-000001"
                    class="block w-full pl-12 pr-4 py-4 bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-700 focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-300 shadow-sm">
            </div>
            @error('ticket_number')
                <p class="mt-2 text-sm text-rose-500 ml-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-[0.2em] ml-1">
                Alamat Email <span class="text-emerald-500">*</span>
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-600 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    placeholder="nama@organisasi.id"
                    class="block w-full pl-12 pr-4 py-4 bg-slate-950/50 border border-slate-800 rounded-2xl text-white placeholder-slate-700 focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-300">
            </div>
            @error('email')
                <p class="mt-2 text-sm text-rose-500 ml-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full bg-gradient-to-r from-emerald-600 to-blue-600 hover:from-emerald-500 hover:to-blue-500 text-white py-4 rounded-2xl font-black uppercase tracking-widest shadow-[0_10px_25px_-5px_rgba(16,185,129,0.3)] hover:shadow-[0_15px_30px_-5px_rgba(16,185,129,0.4)] transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center space-x-3 group">
            <svg class="w-6 h-6 transform group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span>Verifikasi Laporan</span>
        </button>
    </form>

    <div class="mt-10 text-center border-t border-slate-200 dark:border-slate-800/50 pt-8 transition-colors">
        <a href="{{ route('portal.ticket.create') }}"
            class="inline-flex items-center space-x-2 text-sm font-bold text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all group">
            <div class="w-8 h-8 bg-white dark:bg-slate-950 rounded-lg flex items-center justify-center border border-slate-200 dark:border-slate-800 group-hover:border-emerald-500/30 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span class="uppercase tracking-[0.1em]">Buat Laporan Baru</span>
        </a>
    </div>
</x-guest-layout>