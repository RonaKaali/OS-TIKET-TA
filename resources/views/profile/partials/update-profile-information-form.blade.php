<section>
    <header>
        <h2 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
            {{ __("Perbarui data diri, alamat email, dan pengaturan notifikasi akun Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-2xl text-sm font-bold text-slate-900 dark:text-white focus:border-blue-500 transition-all" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email Institusi')" class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-2xl text-sm font-bold text-slate-900 dark:text-white focus:border-blue-500 transition-all" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-2 p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                    <p class="text-xs font-bold text-amber-500">
                        {{ __('Alamat email Anda belum terverifikasi.') }}

                        <button form="send-verification"
                            class="ml-2 underline text-amber-400 hover:text-amber-300">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-[10px] font-black text-emerald-500 uppercase tracking-widest">
                            {{ __('Link verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="__('Nomor Telepon / WhatsApp')" class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1 ml-1" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 rounded-2xl text-sm font-bold text-slate-900 dark:text-white focus:border-blue-500 transition-all" :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div class="p-5 bg-blue-500/[0.03] border border-blue-500/10 rounded-2xl">
            <x-input-label for="telegram_username" :value="__('Integrasi Notifikasi Telegram')" class="text-[10px] font-black uppercase tracking-widest text-blue-400 mb-3 ml-1" />
            <div class="mt-1 flex rounded-2xl shadow-sm overflow-hidden border border-slate-800">
                <span
                    class="inline-flex items-center px-4 bg-slate-100 dark:bg-slate-900 text-slate-500 text-sm font-black border-r border-slate-200 dark:border-slate-800">
                    @
                </span>
                <input id="telegram_username" name="telegram_username" type="text"
                    class="block w-full bg-slate-50 dark:bg-slate-950 border-none py-3 px-4 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-700 focus:ring-0" 
                    value="{{ old('telegram_username', $user->telegram_username) }}" placeholder="username_telegram" autocomplete="off" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('telegram_username')" />
            
            <p class="mt-3 text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-tighter">
                Masukkan username Telegram Anda (tanpa @). Sistem akan otomatis mengirim notifikasi keamanan ke akun Anda.
            </p>
            
            <div class="mt-4 flex flex-col space-y-2">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-4 h-4 rounded-full bg-blue-500/10 border border-blue-500/20 flex items-center justify-center mt-0.5">
                        <span class="text-[8px] text-blue-400 font-black">1</span>
                    </div>
                    <p class="ml-2 text-[10px] text-slate-400 font-medium italic">Kirim pesan <code class="bg-slate-900 px-1.5 py-0.5 rounded border border-slate-700 text-blue-400">/start</code> ke bot CSIRT di Telegram <span class="text-blue-500 font-bold">@kominfokalsel_bot</span></p>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-4 h-4 rounded-full bg-blue-500/10 border border-blue-500/20 flex items-center justify-center mt-0.5">
                        <span class="text-[8px] text-blue-400 font-black">2</span>
                    </div>
                    <p class="ml-2 text-[10px] text-slate-400 font-medium italic">Chat ID akan otomatis tersinkronisasi dengan akun portal ini.</p>
                </div>
            </div>

            @if(isset($user->telegram_chat_id) && $user->telegram_chat_id)
                <div class="mt-4 flex items-center px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">✅ Notifikasi Telegram Aktif</span>
                </div>
            @elseif(isset($user->telegram_username) && $user->telegram_username)
                <div class="mt-4 flex items-center px-4 py-2 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                    <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">⚠️ Menunggu Aktivasi /start</span>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" 
                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-lg active:scale-95">
                {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">{{ __('Data Berhasil Disimpan.') }}</p>
            @endif
        </div>
    </form>
</section>