<section class="space-y-6">
    <header>
        <h2 class="text-lg font-black text-red-500 uppercase tracking-tight">
            {{ __('Terminasi Akun') }}
        </h2>

        <p class="mt-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
            {{ __('Setelah akun dihapus, semua sumber daya dan data Anda akan dihapus secara permanen. Harap unduh data penting sebelum melakukan tindakan ini.') }}
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-8 py-3 bg-red-600/10 hover:bg-red-600/20 text-red-500 text-[10px] font-black uppercase tracking-widest rounded-2xl border border-red-500/20 transition-all active:scale-95 shadow-lg"
    >{{ __('Hapus Akun Permanen') }}</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden relative">
            @csrf
            @method('delete')

            <h2 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight">
                {{ __('Konfirmasi Penghapusan Akun') }}
            </h2>

            <p class="mt-2 text-xs font-bold text-slate-500 uppercase leading-relaxed tracking-tighter">
                {{ __('Tindakan ini tidak dapat dibatalkan. Harap masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.') }}
            </p>

            <div class="mt-8">
                <x-input-label for="password" value="{{ __('Konfirmasi Password') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1" />

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3 px-5 text-sm font-bold text-slate-900 dark:text-white focus:border-red-500 outline-none transition-all"
                    placeholder="{{ __('Password Anda') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-10 flex justify-end space-x-4">
                <button type="button" 
                    x-on:click="$dispatch('close')"
                    class="px-6 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all border border-slate-300 dark:border-slate-700">
                    {{ __('Batalkan') }}
                </button>

                <button type="submit" 
                    class="px-8 py-2.5 bg-red-600 hover:bg-red-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-[0_0_15px_rgba(239,68,68,0.3)] transition-all transform active:scale-95">
                    {{ __('Hapus Sekarang') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
