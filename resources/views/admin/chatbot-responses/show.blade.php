<x-admin-layout>
    <div class="mb-8">
        <a href="{{ route('admin.chatbot-responses.index') }}" class="inline-flex items-center text-[10px] font-black text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 uppercase tracking-[0.2em] transition-colors mb-4 group">
            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase transition-colors">Detail <span class="text-indigo-600 dark:text-indigo-400 transition-colors">Chatbot Response</span></h1>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest transition-colors">Analisis Konfigurasi Automasi</p>
    </div>

    <div class="max-w-4xl space-y-6">
        <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-8 relative overflow-hidden group shadow-sm dark:shadow-2xl transition-colors">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/5 blur-[80px] rounded-full -mr-32 -mt-32 transition-colors"></div>
            
            <div class="relative z-10 space-y-8">
                <!-- Keyword Area -->
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">Keyword / Frasa Pemicu</label>
                    <div class="p-6 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-inner transition-colors">
                        <code class="text-lg font-black text-indigo-600 dark:text-indigo-400 tracking-tight">{{ $chatbotResponse->keyword }}</code>
                    </div>
                </div>

                <!-- Response Area -->
                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">Templat Respon Automatis</label>
                    <div class="p-8 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-inner transition-colors">
                        <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 font-bold text-sm leading-relaxed whitespace-pre-wrap">
                            {{ $chatbotResponse->response }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl transition-colors">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 transition-colors">Logic Type</label>
                        <span class="px-3 py-1 text-[10px] font-black rounded-lg uppercase tracking-widest border
                            @if($chatbotResponse->match_type === 'exact') bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20
                            @elseif($chatbotResponse->match_type === 'starts_with') bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20
                            @else bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20
                            @endif">
                            {{ $chatbotResponse->match_type }}
                        </span>
                    </div>

                    <div class="p-6 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl transition-colors">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 transition-colors">Priority Score</label>
                        <div class="flex items-end space-x-1">
                            <span class="text-2xl font-black text-slate-900 dark:text-white transition-colors">{{ $chatbotResponse->priority }}</span>
                            <span class="text-[10px] font-bold text-slate-400 mb-1">/ 100</span>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl transition-colors">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 transition-colors">System Status</label>
                        @if($chatbotResponse->is_active)
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest transition-colors">ENABLED</span>
                            </div>
                        @else
                            <div class="flex items-center space-x-2 opacity-50">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                <span class="text-[10px] font-black text-red-600 dark:text-red-400 uppercase tracking-widest transition-colors">DISABLED</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col md:flex-row md:items-center gap-6 pt-8 border-t border-slate-100 dark:border-slate-800/50 transition-colors">
                    <div class="flex-1">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] transition-colors">Audit Trail</p>
                        <div class="flex items-center space-x-4 mt-1 text-[10px] font-bold text-slate-500 dark:text-slate-400 transition-colors uppercase">
                            <span>Created: {{ $chatbotResponse->created_at->format('d/m/Y H:i') }}</span>
                            <span class="w-1 h-1 bg-slate-300 dark:bg-slate-700 rounded-full"></span>
                            <span>Updated: {{ $chatbotResponse->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.chatbot-responses.edit', $chatbotResponse) }}"
                            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg transition-all transform hover:-translate-y-1 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Modify Response
                        </a>
                        <form method="POST" action="{{ route('admin.chatbot-responses.destroy', $chatbotResponse) }}" 
                            class="inline"
                            onsubmit="return confirmDelete('{{ $chatbotResponse->keyword }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="px-6 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-red-500/10 text-slate-500 hover:text-red-600 dark:hover:text-red-400 border border-slate-200 dark:border-slate-700 hover:border-red-500/30 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                                Purge Record
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(keyword) {
            return confirm(`CRITICAL ACTION: Purge response for keyword "${keyword}"?\n\nThis action cannot be undone.`);
        }
    </script>
</x-admin-layout>
