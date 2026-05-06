<x-admin-layout>
    <div class="mb-8">
        <a href="{{ route('admin.canned.index') }}" class="inline-flex items-center text-[10px] font-black text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400 uppercase tracking-[0.2em] transition-colors mb-4 group">
            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase transition-colors">Tambah <span class="text-emerald-600 dark:text-emerald-400 transition-colors">Tanggapan Cepat</span></h1>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest transition-colors">Buat Template Respon Standar Tim Analis</p>
    </div>

    <div class="max-w-4xl">
        <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-8 relative overflow-hidden group shadow-sm dark:shadow-2xl transition-colors">
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 blur-[80px] rounded-full -mr-32 -mt-32 transition-colors"></div>
            
            <form method="POST" action="{{ route('admin.canned.store') }}" class="relative z-10 space-y-8">
                @csrf

                <div class="space-y-2">
                    <label for="title" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">Judul Template <span class="text-emerald-600 dark:text-emerald-400 transition-colors">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        placeholder="Contoh: Prosedur Awal Penanganan Insiden"
                        class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-4 px-6 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:border-emerald-500 outline-none transition-all shadow-inner transition-colors">
                    @error('title')
                        <p class="mt-1 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="body" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">Konten Respon <span class="text-emerald-600 dark:text-emerald-400 transition-colors">*</span></label>
                    <textarea name="body" id="body" rows="12" required
                        placeholder="Tuliskan isi pesan template di sini..."
                        class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-4 px-6 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:border-emerald-500 outline-none transition-all shadow-inner transition-colors">{{ old('body') }}</textarea>
                    <div class="flex items-center space-x-2 mt-2 ml-1 transition-colors">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">Gunakan bahasa yang profesional dan informatif untuk memudahkan pelapor.</p>
                    </div>
                    @error('body')
                        <p class="mt-1 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-100 dark:border-slate-800/50 transition-colors">
                    <a href="{{ route('admin.canned.index') }}"
                        class="px-8 py-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all">
                        Batalkan
                    </a>
                    <button type="submit" class="px-12 py-4 bg-gradient-to-r from-emerald-600 to-blue-600 hover:from-emerald-500 hover:to-blue-500 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_30px_rgba(16,185,129,0.5)] transition-all transform hover:-translate-y-1">
                        Simpan Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>