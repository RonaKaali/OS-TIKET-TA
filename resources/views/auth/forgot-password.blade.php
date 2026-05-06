<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-black text-white mb-2 tracking-tight">Pemulihan Akses</h2>
        <p class="text-slate-400 text-xs leading-relaxed px-4">
            Lupa passphrase anda? Masukkan identitas email anda dan sistem akan mengirimkan tautan enkripsi untuk inisialisasi ulang akses.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2 ml-1">
                Alamat Email Terdaftar
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-500 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <input id="email" 
                    class="block w-full pl-10 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-600 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none text-sm" 
                    type="email" name="email" :value="old('email')" required autofocus 
                    placeholder="nama@institusi.go.id" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-3.5 px-6 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] shadow-[0_0_15px_rgba(16,185,129,0.2)] hover:shadow-[0_0_25px_rgba(16,185,129,0.4)] transition-all flex items-center justify-center space-x-2">
                <span>Kirim Link Inisialisasi</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </form>
</x-guest-layout>
