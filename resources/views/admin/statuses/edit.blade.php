<x-admin-layout>
    <div class="mb-8">
        <a href="{{ route('admin.statuses.index') }}" class="inline-flex items-center text-[10px] font-black text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400 uppercase tracking-[0.2em] transition-colors mb-4 group">
            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase transition-colors">Edit <span class="text-emerald-600 dark:text-emerald-500 transition-colors">Status</span></h1>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest transition-colors">Modifikasi Tahapan Laporan: {{ $status->name }}</p>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-8 relative overflow-hidden group shadow-sm dark:shadow-2xl transition-colors">
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 blur-[80px] rounded-full -mr-32 -mt-32"></div>
            
            <form method="POST" action="{{ route('admin.statuses.update', $status) }}" class="relative z-10 space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label for="name" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">Nama Status <span class="text-emerald-600 dark:text-emerald-500 transition-colors">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $status->name) }}" required
                            placeholder="Contoh: Teranalisis"
                            class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-4 px-6 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:border-emerald-500 outline-none transition-all shadow-inner transition-colors">
                        @error('name')
                            <p class="mt-1 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="slug" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">Slug (System ID) <span class="text-emerald-600 dark:text-emerald-500 transition-colors">*</span></label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $status->slug) }}" required
                            placeholder="contoh: open, closed"
                            class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-4 px-6 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:border-emerald-500 outline-none transition-all shadow-inner transition-colors">
                        @error('slug')
                            <p class="mt-1 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="is_closing" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">Jenis Status <span class="text-emerald-600 dark:text-emerald-500 transition-colors">*</span></label>
                    <select name="is_closing" id="is_closing" required
                        class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 dark:text-slate-300 focus:border-emerald-500 outline-none transition-all cursor-pointer shadow-inner appearance-none transition-colors">
                        <option value="0" {{ old('is_closing', $status->is_closing) == false ? 'selected' : '' }} class="bg-white dark:bg-slate-900">AKTIF (Sedang dalam proses penanganan)</option>
                        <option value="1" {{ old('is_closing', $status->is_closing) == true ? 'selected' : '' }} class="bg-white dark:bg-slate-900">CLOSING (Tahap penyelesaian/selesai)</option>
                    </select>
                    @error('is_closing')
                        <p class="mt-1 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-100 dark:border-slate-800/50 transition-colors">
                    <a href="{{ route('admin.statuses.index') }}"
                        class="px-8 py-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all">
                        Batalkan
                    </a>
                    <button type="submit" class="px-12 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_30px_rgba(16,185,129,0.5)] transition-all transform hover:-translate-y-1">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>