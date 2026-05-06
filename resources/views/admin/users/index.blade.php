<x-admin-layout>
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase transition-colors">Manajemen <span class="text-blue-600 dark:text-blue-500 transition-colors">Pengguna</span></h1>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest transition-colors">Otoritas & Kontrol Akses Sistem</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-[0_0_20px_rgba(59,130,246,0.3)] hover:shadow-[0_0_30px_rgba(59,130,246,0.5)] transition-all transform hover:-translate-y-1">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                Tambah User Baru
            </a>
        </div>
    </div>

    <!-- Filter & Search Panel -->
    <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-6 mb-8 relative overflow-hidden group shadow-sm transition-colors">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 blur-[50px] rounded-full -mr-16 -mt-16 group-hover:bg-blue-500/10 transition-all duration-700"></div>
        
        <form method="GET" action="{{ route('admin.users.index') }}" class="relative z-10 grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-5">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Pencarian Cepat</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 dark:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, email, atau ID..."
                        class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3.5 pl-11 pr-4 text-xs font-bold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-700 focus:border-blue-500 outline-none transition-all shadow-inner">
                </div>
            </div>
            <div class="md:col-span-3">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Filter Peran</label>
                <select name="role"
                    class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3.5 px-4 text-xs font-bold text-slate-700 dark:text-slate-300 focus:border-blue-500 outline-none transition-all cursor-pointer shadow-inner appearance-none">
                    <option value="" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }} class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4 flex gap-3">
                <button type="submit" 
                    class="flex-1 py-3.5 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 border border-slate-800 dark:border-slate-700 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all shadow-lg active:scale-95">
                    Filter Data
                </button>
                <a href="{{ route('admin.users.index') }}"
                    class="px-6 py-3.5 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white hover:border-slate-400 dark:hover:border-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Users Data Table -->
    <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm dark:shadow-2xl transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-950/50 border-b border-slate-200 dark:border-slate-800 transition-colors">
                        <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Informasi Profil</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Afiliasi</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Otoritas / Role</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Registrasi</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 transition-colors">
                    @forelse($users as $user)
                        <tr class="hover:bg-blue-500/[0.02] transition-colors group">
                            <td class="px-6 py-6 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center mr-4 group-hover:border-blue-500/50 transition-all">
                                        <span class="text-xs font-black text-slate-400 dark:text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-slate-900 dark:text-slate-200 group-hover:text-blue-700 dark:group-hover:text-white transition-colors">{{ $user->name }}</div>
                                        <div class="text-[10px] font-bold text-slate-500 mt-0.5 transition-colors">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6 whitespace-nowrap">
                                <div class="text-xs font-bold text-slate-700 dark:text-slate-300 transition-colors">{{ $user->organization->name ?? 'Individu / Mandiri' }}</div>
                                <div class="text-[10px] font-medium text-slate-500 dark:text-slate-600 mt-0.5 tracking-tight transition-colors">{{ $user->telepon ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-6 whitespace-nowrap">
                                <div class="flex flex-wrap gap-2">
                                    @if($user->roles->isNotEmpty())
                                        @foreach($user->roles as $role)
                                            <span class="px-3 py-1 text-[8px] font-black uppercase tracking-widest rounded-full 
                                                @if($role->name == 'Super Admin') bg-purple-500/10 text-purple-400 border border-purple-500/20 shadow-[0_0_10px_rgba(168,85,247,0.1)]
                                                @elseif($role->name == 'Admin') bg-blue-500/10 text-blue-400 border border-blue-500/20
                                                @elseif($role->name == 'Agent') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                                @elseif($role->name == 'Support Agent') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                                @else bg-slate-800/50 text-slate-400 border border-slate-700
                                                @endif">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="px-3 py-1 text-[8px] font-black uppercase tracking-widest rounded-full bg-slate-800/50 text-slate-500 border border-slate-800">Pelapor</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-6 whitespace-nowrap">
                                <div class="text-[10px] font-black text-slate-400 mb-0.5">{{ $user->created_at->translatedFormat('d M Y') }}</div>
                                <div class="text-[9px] font-bold text-slate-600">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-6 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center space-x-2 opacity-30 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="p-2 bg-slate-50 dark:bg-slate-800 hover:bg-blue-600/10 dark:hover:bg-blue-600/20 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 border border-slate-200 dark:border-slate-700 hover:border-blue-500/30 rounded-xl transition-all"
                                        title="Edit Profil">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                            onsubmit="return confirm('Sistem Keamanan: Konfirmasi penghapusan akses user secara permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="p-2 bg-slate-50 dark:bg-slate-800 hover:bg-red-600/10 dark:hover:bg-red-600/20 text-slate-400 hover:text-red-600 dark:hover:text-red-400 border border-slate-200 dark:border-slate-700 hover:border-red-500/30 rounded-xl transition-all"
                                                title="Revoke Akses">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    @else
                                        <div class="p-2 bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 rounded-xl" title="Sesi Aktif">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-center mb-4 transition-colors">
                                        <svg class="w-8 h-8 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    </div>
                                    <h3 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest transition-colors">Data Tidak Ditemukan</h3>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-600 mt-1 italic transition-colors">Sesuaikan parameter filter untuk pencarian lain</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-6 border-t border-slate-200 dark:border-slate-800 transition-colors">
                <div class="custom-pagination">
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>

    <style>
        .custom-pagination nav { display: flex; justify-content: center; }
        .custom-pagination span, .custom-pagination a { 
            background: white !important; 
            border: 1px solid #e2e8f0 !important; 
            color: #64748b !important; 
            border-radius: 12px !important;
            padding: 8px 16px !important;
            margin: 0 4px !important;
            font-size: 10px !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            transition: all 0.2s !important;
        }
        .dark .custom-pagination span, .dark .custom-pagination a { 
            background: #0f172a !important; 
            border: 1px solid #1e293b !important; 
            color: #94a3b8 !important; 
        }
        .custom-pagination .active span { 
            background: #2563eb !important; 
            border-color: #3b82f6 !important; 
            color: white !important;
            box-shadow: 0 0 15px rgba(37, 99, 235, 0.3) !important;
        }
        .custom-pagination a:hover { 
            border-color: #3b82f6 !important; 
            color: #2563eb !important; 
        }
        .dark .custom-pagination a:hover { 
            color: white !important; 
        }
    </style>
</x-admin-layout>