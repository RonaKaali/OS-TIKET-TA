<section>
    <header>
        <h2 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight">
            {{ __('Two-Factor Authentication (2FA)') }}
        </h2>

        <p class="mt-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
            {{ __('Tingkatkan keamanan akun Anda dengan verifikasi dua langkah.') }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        <!-- Status MFA -->
        <div class="flex items-center justify-between p-5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl relative overflow-hidden group transition-all">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 blur-2xl rounded-full -mr-12 -mt-12"></div>
            
            <div class="flex items-center space-x-4 relative z-10">
                <div class="flex-shrink-0">
                    @if($mfaEnabled)
                        <div class="w-12 h-12 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center justify-center shadow-[0_0_15px_rgba(16,185,129,0.1)]">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    @else
                        <div class="w-12 h-12 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">
                        STATUS OTENTIKASI
                    </h3>
                    <div class="mt-1 flex items-center">
                        @if($mfaEnabled)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                TERPROTEKSI
                            </span>
                            @if($user->mfa_enabled_at)
                                <span class="text-[9px] text-slate-600 font-bold uppercase ml-3 tracking-tighter">
                                    ESTABLISHED: {{ \Carbon\Carbon::parse($user->mfa_enabled_at)->translatedFormat('d M Y') }}
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-slate-800 text-slate-500 border border-slate-700">
                                TIDAK AKTIF
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
            @if($mfaEnabled)
                <!-- Disable MFA -->
                <form method="POST" action="{{ route('mfa.disable') }}" class="w-full max-w-xl">
                    @csrf
                    <label for="mfa_disable_password" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">
                        Konfirmasi Password
                    </label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input id="mfa_disable_password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            placeholder="Password akun"
                            class="flex-1 bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 dark:text-white placeholder:text-slate-500 focus:border-red-500 focus:ring-red-500">

                        <button type="submit"
                            class="px-6 py-2.5 bg-red-500/10 hover:bg-red-500/20 text-red-500 text-[10px] font-black uppercase tracking-widest rounded-xl border border-red-500/20 transition-all active:scale-95"
                            onclick="return confirm('Apakah Anda yakin ingin menonaktifkan 2FA? Akun Anda akan menjadi kurang aman.')">
                            {{ __('Reset / Ganti 2FA') }}
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-[10px] font-bold text-red-400 uppercase tracking-wider">{{ $message }}</p>
                    @enderror
                </form>
                <a href="{{ route('mfa.backup-codes') }}"
                    class="inline-flex items-center px-6 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-black uppercase tracking-widest rounded-xl border border-slate-300 dark:border-slate-700 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Backup Codes
                </a>
            @else
                <!-- Setup MFA -->
                <a href="{{ route('mfa.setup') }}"
                    class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:shadow-[0_0_20px_rgba(59,130,246,0.3)] transition-all transform active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    {{ __('Aktifkan Proteksi 2FA') }}
                </a>
            @endif
        </div>

        <!-- Info Panels -->
        @if($mfaEnabled)
            <div class="p-5 bg-blue-500/[0.03] border border-blue-500/10 rounded-2xl flex items-start">
                <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-xs font-black text-blue-400 uppercase tracking-widest mb-1">PROTEKSI AKTIF</h4>
                    <p class="text-[10px] font-bold text-slate-500 uppercase leading-relaxed tracking-tighter">
                        Akun Anda telah dilindungi dengan Two-Factor Authentication. Sistem akan meminta kode verifikasi dari aplikasi authenticator Anda setiap kali ada upaya login baru.
                    </p>
                </div>
            </div>
        @else
            <div class="p-5 bg-amber-500/[0.03] border border-amber-500/10 rounded-2xl flex items-start">
                <svg class="w-5 h-5 text-amber-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h4 class="text-xs font-black text-amber-400 uppercase tracking-widest mb-1">TINGKAT KEAMANAN RENDAH</h4>
                    <p class="text-[10px] font-bold text-slate-500 uppercase leading-relaxed tracking-tighter">
                        Sangat disarankan untuk mengaktifkan 2FA guna mencegah akses ilegal ke akun Anda, meskipun password Anda diketahui oleh pihak lain.
                    </p>
                </div>
            </div>
        @endif

        <!-- Security Tips -->
        <div class="border-t border-slate-800 pt-6">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Protokol Keamanan:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center space-x-3 group">
                    <div class="w-2 h-2 rounded-full bg-slate-700 group-hover:bg-blue-500 transition-colors"></div>
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-tighter">Simpan backup codes di tempat offline yang aman</span>
                </div>
                <div class="flex items-center space-x-3 group">
                    <div class="w-2 h-2 rounded-full bg-slate-700 group-hover:bg-blue-500 transition-colors"></div>
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-tighter">Jangan bagikan QR code dengan siapapun</span>
                </div>
                <div class="flex items-center space-x-3 group">
                    <div class="w-2 h-2 rounded-full bg-slate-700 group-hover:bg-blue-500 transition-colors"></div>
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-tighter">Gunakan aplikasi Google/Microsoft Authenticator</span>
                </div>
                <div class="flex items-center space-x-3 group">
                    <div class="w-2 h-2 rounded-full bg-slate-700 group-hover:bg-blue-500 transition-colors"></div>
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-tighter">Sinkronkan waktu smartphone secara otomatis</span>
                </div>
            </div>
        </div>
    </div>
</section>
