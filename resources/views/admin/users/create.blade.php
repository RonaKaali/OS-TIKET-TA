<x-admin-layout>
    <div class="mb-8">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-[10px] font-black text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 uppercase tracking-[0.2em] transition-colors mb-4 group">
            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase transition-colors">Registrasi <span class="text-blue-600 dark:text-blue-500 transition-colors">User Baru</span></h1>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest transition-colors">Inisialisasi Akses Kredensial Sistem</p>
    </div>

    <div class="max-w-4xl">
        <div class="bg-white dark:bg-slate-900/50 backdrop-blur-xl border border-slate-200 dark:border-slate-800 rounded-3xl p-8 relative overflow-hidden group shadow-sm dark:shadow-2xl transition-colors">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/5 blur-[80px] rounded-full -mr-32 -mt-32"></div>
            
            <form method="POST" action="{{ route('admin.users.store') }}" class="relative z-10 space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Name -->
                    <div class="space-y-2">
                        <label for="name" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">
                            Nama Lengkap <span class="text-blue-600 dark:text-blue-500 transition-colors">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3.5 px-5 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:border-blue-500 outline-none transition-all shadow-inner"
                            placeholder="Contoh: Analis Keamanan Siber">
                        @error('name')
                            <p class="mt-1 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="email" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">
                            Email Institusi <span class="text-blue-600 dark:text-blue-500 transition-colors">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3.5 px-5 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:border-blue-500 outline-none transition-all shadow-inner"
                            placeholder="user@kalselprov.go.id">
                        @error('email')
                            <p class="mt-1 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="space-y-2">
                        <label for="phone" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">
                            Nomor Telepon / WhatsApp
                        </label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="08XXXXXXXXXX"
                            class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3.5 px-5 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:border-blue-500 outline-none transition-all shadow-inner">
                    </div>

                    <!-- Organization -->
                    <div class="space-y-2">
                        <label for="organization_id" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">
                            Afiliasi Organisasi
                        </label>
                        <select name="organization_id" id="organization_id"
                            class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3.5 px-5 text-sm font-bold text-slate-700 dark:text-slate-300 focus:border-blue-500 outline-none transition-all cursor-pointer shadow-inner appearance-none transition-colors">
                            <option value="" class="bg-white dark:bg-slate-900">Individu / Tanpa Organisasi</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }} class="bg-white dark:bg-slate-900">
                                    {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-slate-100 dark:border-slate-800/50 transition-colors">
                    <!-- Password -->
                    <div class="space-y-2">
                        <label for="password" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">
                            Password Sistem <span class="text-blue-600 dark:text-blue-500 transition-colors">*</span>
                        </label>
                        <input type="password" name="password" id="password" required
                            class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3.5 px-5 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:border-blue-500 outline-none transition-all shadow-inner">
                        @error('password')
                            <p class="mt-1 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">
                            Konfirmasi Password <span class="text-blue-600 dark:text-blue-500 transition-colors">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="block w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl py-3.5 px-5 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-300 dark:placeholder-slate-800 focus:border-blue-500 outline-none transition-all shadow-inner">
                    </div>
                </div>

                <!-- Role -->
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800/50 transition-colors">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 transition-colors">Penugasan Peran (Role)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                            <label class="relative flex cursor-pointer group">
                                <input type="radio" name="role" value="{{ $role->name }}" {{ old('role') == $role->name ? 'checked' : '' }} class="peer sr-only">
                                <div class="w-full p-4 bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-2xl peer-checked:border-blue-500 peer-checked:bg-blue-600/10 dark:peer-checked:bg-blue-500/10 transition-all hover:bg-slate-100 dark:hover:bg-slate-900 transition-colors">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase peer-checked:text-blue-700 dark:peer-checked:text-blue-400 transition-colors">{{ $role->name }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-600 mt-1 uppercase tracking-tighter transition-colors">Level Otoritas Terbatas</span>
                                    </div>
                                </div>
                                <div class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('role')
                        <p class="mt-1 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-4 pt-8 border-t border-slate-100 dark:border-slate-800/50 transition-colors">
                    <a href="{{ route('admin.users.index') }}"
                        class="px-8 py-3.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all">
                        Batalkan
                    </a>
                    <button type="submit" 
                        class="px-12 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-[0_0_20px_rgba(59,130,246,0.3)] hover:shadow-[0_0_30px_rgba(59,130,246,0.5)] transition-all transform hover:-translate-y-1">
                        Simpan Data User
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>