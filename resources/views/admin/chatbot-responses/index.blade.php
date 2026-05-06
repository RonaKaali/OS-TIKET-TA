<x-admin-layout>
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase transition-colors">Chatbot <span class="text-indigo-600 dark:text-indigo-400 transition-colors">Responses</span></h1>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest transition-colors">Manajemen Automasi Respon Keamanan</p>
            </div>
            <a href="{{ route('admin.chatbot-responses.create') }}"
                class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-[0_0_20px_rgba(79,70,229,0.3)] hover:shadow-[0_0_30px_rgba(79,70,229,0.5)] transition-all transform hover:-translate-y-1">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                Tambah Response
            </a>
        </div>
    </div>

    @if(session('ok'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center text-emerald-600 dark:text-emerald-400">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-xs font-bold uppercase tracking-wider">{{ session('ok') }}</span>
        </div>
    @endif

    <!-- Filter & Search -->
    <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-6 mb-8 transition-colors">
        <form method="GET" action="{{ route('admin.chatbot-responses.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div class="md:col-span-2">
                <label for="search" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 mb-2">Cari Keyword / Respon</label>
                <div class="relative group">
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Contoh: lupa password, cara lapor..."
                        class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3 px-5 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-800 focus:border-indigo-500 outline-none transition-all shadow-inner">
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                </div>
            </div>
            <div>
                <label for="status" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 mb-2">Status</label>
                <select name="status" id="status"
                    class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3 px-5 text-sm font-bold text-slate-700 dark:text-slate-300 focus:border-indigo-500 outline-none transition-all cursor-pointer shadow-inner appearance-none transition-colors">
                    <option value="">SEMUA STATUS</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>AKTIF</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>TIDAK AKTIF</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all border border-slate-200 dark:border-slate-700">
                    Terapkan
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.chatbot-responses.index') }}"
                        class="px-4 py-3 bg-red-500/10 hover:bg-red-500/20 text-red-600 dark:text-red-400 text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all border border-red-500/20 flex items-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm dark:shadow-2xl transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-950/50 border-b border-slate-200 dark:border-slate-800 transition-colors">
                        <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Keyword Utama</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Template Respon</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Match Type</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Prioritas</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 transition-colors">
                    @forelse($items as $item)
                        <tr class="hover:bg-indigo-500/[0.02] transition-colors group">
                            <td class="px-6 py-6 whitespace-nowrap">
                                <span class="text-sm font-black text-slate-900 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-white transition-colors">{{ $item->keyword }}</span>
                            </td>
                            <td class="px-6 py-6">
                                <div class="text-xs font-bold text-slate-500 dark:text-slate-400 max-w-xs truncate" title="{{ $item->response }}">
                                    {{ Str::limit($item->response, 80) }}
                                </div>
                            </td>
                            <td class="px-6 py-6 whitespace-nowrap">
                                <span class="px-3 py-1 text-[9px] font-black rounded-lg uppercase tracking-widest border
                                    @if($item->match_type === 'exact') bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20
                                    @elseif($item->match_type === 'starts_with') bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20
                                    @else bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20
                                    @endif">
                                    {{ $item->match_type }}
                                </span>
                            </td>
                            <td class="px-6 py-6 whitespace-nowrap">
                                <span class="text-sm font-black text-slate-400 dark:text-slate-600">#{{ $item->priority }}</span>
                            </td>
                            <td class="px-6 py-6 whitespace-nowrap">
                                @if($item->is_active)
                                    <div class="flex items-center space-x-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                        <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">AKTIF</span>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-2 opacity-50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">NONAKTIF</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-6 whitespace-nowrap text-right">
                                <div class="flex justify-end items-center space-x-2">
                                    <a href="{{ route('admin.chatbot-responses.show', $item) }}"
                                        class="p-2 bg-slate-50 dark:bg-slate-800 hover:bg-indigo-600/10 dark:hover:bg-indigo-600/20 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 hover:border-indigo-500/30 rounded-xl transition-all"
                                        title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    <a href="{{ route('admin.chatbot-responses.edit', $item) }}"
                                        class="p-2 bg-slate-50 dark:bg-slate-800 hover:bg-blue-600/10 dark:hover:bg-blue-600/20 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 border border-slate-200 dark:border-slate-700 hover:border-blue-500/30 rounded-xl transition-all"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.chatbot-responses.destroy', $item) }}" 
                                        class="inline"
                                        onsubmit="return confirmDelete('{{ $item->keyword }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="p-2 bg-slate-50 dark:bg-slate-800 hover:bg-red-600/10 dark:hover:bg-red-600/20 text-slate-400 hover:text-red-600 dark:hover:text-red-400 border border-slate-200 dark:border-slate-700 hover:border-red-500/30 rounded-xl transition-all"
                                            title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center text-slate-500 text-xs italic font-bold">
                                Tidak ada data chatbot response ditemukan. 
                                <a href="{{ route('admin.chatbot-responses.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Tambah data pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-6 border-t border-slate-200 dark:border-slate-800 transition-colors">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    <script>
        function confirmDelete(keyword) {
            return confirm(`Yakin ingin menghapus response dengan keyword "${keyword}"?\n\nData yang dihapus tidak dapat dikembalikan.`);
        }
    </script>
</x-admin-layout>
