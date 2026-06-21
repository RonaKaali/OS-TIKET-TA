<x-guest-layout>
    <div class="mb-8 text-center transition-colors">
        <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-2 tracking-tight transition-colors">Pendaftaran Subjek</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm font-bold transition-colors">Inisialisasi akun untuk akses ke jaringan aman CSIRT</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">
                Nama Lengkap Sesuai ID
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400 dark:text-slate-500 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input id="name"
                    class="block w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none"
                    type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                    placeholder="Nama lengkap Anda">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">
                Alamat Email Institusi
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400 dark:text-slate-500 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <input id="email"
                    class="block w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none"
                    type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    placeholder="nama@example.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Organization -->
        <div>
            <label for="organization_name" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">
                Afiliasi Organisasi / Institusi
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400 dark:text-slate-500 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <input id="organization_name" name="organization_name" list="org-list"
                    class="block w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none"
                    placeholder="Ketik atau pilih organisasi..." value="{{ old('organization_name') }}">
                <datalist id="org-list">
                    <option value="Masyarakat Umum">
                    @foreach($organizations as $org)
                        <option value="{{ $org->name }}">
                    @endforeach
                </datalist>
            </div>
            <x-input-error :messages="$errors->get('organization_name')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">
                    Passphrase
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400 dark:text-slate-500 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input id="password"
                        class="block w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none"
                        type="password" name="password" required autocomplete="new-password"
                        placeholder="Min 8 karakter">
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">
                    Konfirmasi
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400 dark:text-slate-500 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <input id="password_confirmation"
                        class="block w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none"
                        type="password" name="password_confirmation" required autocomplete="new-password"
                        placeholder="Ulangi pass">
                </div>
            </div>
        </div>
        <x-input-error :messages="$errors->get('password')" class="mt-1" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />

        <!-- Telegram Username (Optional) -->
        <div class="bg-slate-100 dark:bg-slate-800/30 border border-slate-200 dark:border-slate-700/50 rounded-2xl p-4 transition-colors">
            <label for="telegram_username" class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-3 ml-1 transition-colors">
                Integrasi Telegram Alert
            </label>
            <div class="flex rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 focus-within:border-emerald-500 transition-all shadow-sm">
                <div class="relative flex items-center px-4 bg-slate-50 dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 text-slate-400 transition-colors">
                    <span class="text-sm font-black">@</span>
                </div>
                <input id="telegram_username"
                    class="flex-1 block w-full px-4 py-3 bg-white dark:bg-slate-900/50 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 outline-none transition-colors"
                    type="text" name="telegram_username" value="{{ old('telegram_username') }}" autocomplete="off"
                    placeholder="id_telegram">
            </div>
            <x-input-error :messages="$errors->get('telegram_username')" class="mt-2" />
            <div class="mt-3 flex items-start space-x-2">
                <svg class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-[10px] text-slate-500 leading-relaxed italic">
                    Kirim <code class="text-emerald-400">/start</code> ke Bot CSIRT setelah registrasi untuk aktivasi notifikasi intelijen siber.
                </p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-4 px-6 rounded-2xl font-black text-sm uppercase tracking-[0.2em] shadow-[0_0_20px_rgba(16,185,129,0.2)] hover:shadow-[0_0_30px_rgba(16,185,129,0.4)] transition-all transform hover:-translate-y-1 active:scale-[0.98] focus:outline-none flex items-center justify-center space-x-3">
                <span>Daftar Sekarang</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </button>
        </div>

        <!-- Login Link -->
        <div class="text-center pt-6 border-t border-slate-100 dark:border-slate-800/50 mt-4 transition-colors">
            <p class="text-xs font-bold text-slate-500 transition-colors">
                Sudah memiliki akses terdaftar?
                <a href="{{ route('login') }}" class="text-emerald-600 dark:text-emerald-500 hover:text-emerald-500 dark:hover:text-emerald-400 font-black ml-1 transition-colors underline decoration-emerald-500/20 decoration-2 underline-offset-4">
                    Kembali Login
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>