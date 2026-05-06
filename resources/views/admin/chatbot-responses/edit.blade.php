<x-admin-layout>
    <div class="mb-8">
        <a href="{{ route('admin.chatbot-responses.index') }}" class="inline-flex items-center text-[10px] font-black text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 uppercase tracking-[0.2em] transition-colors mb-4 group">
            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase transition-colors">Edit <span class="text-indigo-600 dark:text-indigo-400 transition-colors">Chatbot Response</span></h1>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest transition-colors">Modifikasi Automasi: {{ $chatbotResponse->keyword }}</p>
    </div>

    <div class="max-w-4xl">
        <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-8 relative overflow-hidden group shadow-sm dark:shadow-2xl transition-colors">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/5 blur-[80px] rounded-full -mr-32 -mt-32 transition-colors"></div>
            
            <form method="POST" action="{{ route('admin.chatbot-responses.update', $chatbotResponse) }}" class="relative z-10 space-y-8">
                @csrf
                @method('PUT')

                <div class="space-y-2">
                    <label for="keyword" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">
                        Keyword / Pertanyaan Pemicu <span class="text-indigo-600 dark:text-indigo-400 transition-colors">*</span>
                    </label>
                    <input type="text" name="keyword" id="keyword" value="{{ old('keyword', $chatbotResponse->keyword) }}" required
                        placeholder="Contoh: halo, selamat pagi, info"
                        class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-4 px-6 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:border-indigo-500 outline-none transition-all shadow-inner transition-colors">
                    <div class="flex items-center space-x-2 mt-2 ml-1">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight transition-colors">Gunakan kata kunci yang sering digunakan user saat berinteraksi.</p>
                    </div>
                    @error('keyword')
                        <p class="mt-1 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="response" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">
                        Template Respon Chatbot <span class="text-indigo-600 dark:text-indigo-400 transition-colors">*</span>
                    </label>
                    <textarea name="response" id="response" rows="10" required
                        placeholder="Tuliskan respon otomatis yang akan dikirim sistem..."
                        class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-4 px-6 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:border-indigo-500 outline-none transition-all shadow-inner transition-colors">{{ old('response', $chatbotResponse->response) }}</textarea>
                    <div class="flex items-center space-x-2 mt-2 ml-1">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight transition-colors">Mendukung format HTML dasar untuk penekanan informasi penting.</p>
                    </div>
                    @error('response')
                        <p class="mt-1 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label for="match_type" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">Logika Pencocokan <span class="text-indigo-600 dark:text-indigo-400 transition-colors">*</span></label>
                        <select name="match_type" id="match_type" required
                            class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 dark:text-slate-300 focus:border-indigo-500 outline-none transition-all cursor-pointer shadow-inner appearance-none transition-colors">
                            <option value="contains" {{ old('match_type', $chatbotResponse->match_type) === 'contains' ? 'selected' : '' }} class="bg-white dark:bg-slate-900 transition-colors">CONTAINS (Mengandung Kata)</option>
                            <option value="exact" {{ old('match_type', $chatbotResponse->match_type) === 'exact' ? 'selected' : '' }} class="bg-white dark:bg-slate-900 transition-colors">EXACT (Persis Sama)</option>
                            <option value="starts_with" {{ old('match_type', $chatbotResponse->match_type) === 'starts_with' ? 'selected' : '' }} class="bg-white dark:bg-slate-900 transition-colors">STARTS WITH (Dimulai Dengan)</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="priority" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">Level Prioritas <span class="text-indigo-600 dark:text-indigo-400 transition-colors">*</span></label>
                        <input type="number" name="priority" id="priority" value="{{ old('priority', $chatbotResponse->priority) }}" 
                            min="0" max="100" required
                            class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-4 px-6 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:border-indigo-500 outline-none transition-all shadow-inner transition-colors">
                        @error('priority')
                            <p class="mt-1 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800/50 transition-colors">
                    <label class="relative flex items-center cursor-pointer group">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" 
                            {{ old('is_active', $chatbotResponse->is_active) ? 'checked' : '' }}
                            class="peer sr-only">
                        <div class="w-12 h-6 bg-slate-200 dark:bg-slate-800 rounded-full peer peer-checked:bg-indigo-600 transition-all after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-6"></div>
                        <span class="ml-4 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Status Response Aktif</span>
                    </label>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-8 border-t border-slate-100 dark:border-slate-800/50 transition-colors">
                    <a href="{{ route('admin.chatbot-responses.index') }}"
                        class="px-8 py-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all">
                        Batalkan Perubahan
                    </a>
                    <button type="submit" class="px-12 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-[0_0_20px_rgba(79,70,229,0.3)] hover:shadow-[0_0_30px_rgba(79,70,229,0.5)] transition-all transform hover:-translate-y-1">
                        Update Response
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
