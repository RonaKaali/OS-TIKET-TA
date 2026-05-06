<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight uppercase">
                    PROFIL <span class="text-blue-600 dark:text-blue-500">PENGGUNA</span>
                </h2>
                <p class="text-xs font-bold text-slate-500 mt-1 uppercase tracking-widest">Kelola identitas & parameter keamanan akun</p>
            </div>
            <a href="{{ route('welcome') }}"
                class="inline-flex items-center px-6 py-3 bg-slate-800 border border-slate-700 hover:border-slate-600 text-slate-300 hover:text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-lg active:scale-95 group">
                <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Portal Utama
            </a>
        </div>
    </x-slot>

    <div class="py-12 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-500/5 blur-[120px] rounded-full -mr-64 -mt-64 pointer-events-none"></div>
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10">
            <!-- Session Status -->
            <x-auth-session-status class="mb-6" :status="session('status')" />
            
            @if(session('error'))
                <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 px-6 py-4 rounded-2xl flex items-center shadow-lg" role="alert">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p class="text-sm font-bold uppercase tracking-tight">{{ session('error') }}</p>
                </div>
            @endif
            
            <div class="grid grid-cols-1 gap-8">
                <!-- Update Information -->
                <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-8 shadow-sm dark:shadow-2xl relative group overflow-hidden transition-all">
                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-500/30 group-hover:bg-blue-500 transition-colors"></div>
                    <div class="max-w-2xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Update Password -->
                <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-8 shadow-sm dark:shadow-2xl relative group overflow-hidden transition-all">
                    <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500/30 group-hover:bg-indigo-500 transition-colors"></div>
                    <div class="max-w-2xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- MFA Management -->
                <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-8 shadow-sm dark:shadow-2xl relative group overflow-hidden transition-all">
                    <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500/30 group-hover:bg-emerald-500 transition-colors"></div>
                    <div class="max-w-2xl">
                        @include('profile.partials.mfa-management-form')
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="bg-red-50 dark:bg-red-500/[0.02] border border-red-200 dark:border-red-500/10 rounded-3xl p-8 shadow-sm dark:shadow-2xl relative group overflow-hidden transition-all">
                    <div class="absolute top-0 left-0 w-1 h-full bg-red-500/20 group-hover:bg-red-500 transition-colors"></div>
                    <div class="max-w-2xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>