<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-black text-white mb-2 tracking-tight">Verifikasi Identitas</h2>
        <p class="text-slate-400 text-xs leading-relaxed">
            Terima kasih telah bergabung. Sebelum memulai, mohon verifikasi alamat email Anda melalui tautan yang baru saja kami kirimkan.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-[10px] font-bold text-emerald-400 uppercase tracking-widest text-center">
            Tautan verifikasi baru telah dikirimkan ke email Anda.
        </div>
    @endif

    <div class="mt-8 flex flex-col space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-3 px-6 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] shadow-[0_0_15px_rgba(16,185,129,0.2)] hover:shadow-[0_0_25px_rgba(16,185,129,0.4)] transition-all flex items-center justify-center space-x-2">
                <span>Kirim Ulang Email Verifikasi</span>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="text-xs font-bold text-slate-500 hover:text-white transition-colors underline decoration-slate-700 underline-offset-4">
                Terminasi Sesi (Keluar)
            </button>
        </form>
    </div>
</x-guest-layout>
