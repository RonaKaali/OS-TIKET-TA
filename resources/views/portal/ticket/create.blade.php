<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<script>
    if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
</script>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporkan Insiden Siber - CSIRT Kalselprov</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .cyber-grid {
            background-image: 
                linear-gradient(rgba(16, 185, 129, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(16, 185, 129, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .neon-border-glow:focus-within {
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.2);
            border-color: rgba(16, 185, 129, 0.5);
        }
    </style>
</head>

<body class="antialiased text-slate-700 dark:text-slate-300 transition-colors">
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 cyber-grid py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden transition-colors">
        <div class="fixed top-4 right-4 z-50">
            <x-theme-toggle />
        </div>
        <!-- Background Glows -->
        <div class="absolute top-0 -left-4 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 -right-4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl opacity-30"></div>

        <!-- Navigation -->
        <nav class="max-w-7xl mx-auto mb-12 relative z-10">
            <a href="{{ route('welcome') }}"
                class="inline-flex items-center space-x-3 px-6 py-3 bg-white dark:bg-slate-900/50 backdrop-blur-md border border-slate-200 dark:border-slate-700/50 rounded-xl text-slate-600 dark:text-slate-300 font-medium hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/50 transition-all duration-300 group shadow-sm">
                <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
        </nav>

        <div class="max-w-4xl mx-auto relative z-10">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl mb-6 relative group transition-all">
                    <div class="absolute inset-0 bg-emerald-500/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h1 class="text-5xl font-bold text-slate-900 dark:text-white mb-4 tracking-tight transition-colors">Laporkan Insiden Siber</h1>
                <p class="text-slate-600 dark:text-slate-400 text-lg max-w-2xl mx-auto transition-colors">Sistem pelaporan aman berbasis arsitektur <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Zero Trust</span>. Laporan Anda dienkripsi secara end-to-end.</p>
            </div>

            <!-- Form Card -->
            <div class="glass-card rounded-3xl p-8 sm:p-10 shadow-2xl">
                <form method="POST" action="{{ route('portal.ticket.store') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    @if($errors->has('error'))
                        <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-5 py-4 text-rose-600 dark:text-rose-400 text-sm">
                            {{ $errors->first('error') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Subject -->
                        <div class="md:col-span-2">
                            <label for="subject" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 ml-1">
                                Subjek Insiden <span class="text-emerald-500">*</span>
                            </label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                                placeholder="Contoh: Terdeteksi aktivitas login mencurigakan"
                                class="block w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-5 py-4 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-300 shadow-sm">
                            @error('subject')
                                <p class="mt-2 text-sm text-rose-500 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Help Topic -->
                        <div class="neon-border-glow">
                            <label for="help_topic_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 ml-1">
                                Kategori <span class="text-emerald-500">*</span>
                            </label>
                            <select name="help_topic_id" id="help_topic_id" required
                                class="block w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-5 py-4 text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-300 appearance-none cursor-pointer shadow-sm">
                                <option value="" class="bg-white dark:bg-slate-900">Pilih Kategori</option>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}" {{ old('help_topic_id') == $topic->id ? 'selected' : '' }} class="bg-white dark:bg-slate-900">
                                        {{ $topic->department->name }} - {{ $topic->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('help_topic_id')
                                <p class="mt-2 text-sm text-rose-500 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 ml-1">Tingkat Urgensi</label>
                            <select name="priority_id" id="priority_id"
                                class="block w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-5 py-4 text-slate-900 dark:text-white focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-300 appearance-none cursor-pointer shadow-sm">
                                <option value="" class="bg-white dark:bg-slate-900">Normal</option>
                                @foreach(\App\Models\Priority::orderBy('weight')->get() as $priority)
                                    <option value="{{ $priority->id }}" {{ old('priority_id') == $priority->id ? 'selected' : '' }} class="bg-white dark:bg-slate-900">
                                        {{ $priority->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Requester Info -->
                    <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-2xl p-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-emerald-400 uppercase tracking-wider">Identitas Terverifikasi</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Nama Lengkap</label>
                                <p class="text-slate-800 dark:text-slate-200 font-medium transition-colors">{{ Auth::user()->name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Alamat Email</label>
                                <p class="text-slate-800 dark:text-slate-200 font-medium transition-colors">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 ml-1">
                            Detail Insiden <span class="text-emerald-500">*</span>
                        </label>
                        <textarea name="message" id="message" rows="6" required
                            placeholder="Berikan deskripsi teknis, kronologi, atau indikasi serangan yang ditemukan..."
                            class="block w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700/50 rounded-xl px-5 py-4 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-300 shadow-sm">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-2 text-sm text-rose-500 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Attachments -->
                    <div x-data="{ 
                        files: [],
                        isDragging: false,
                        handleFiles(filesList) {
                            if (!filesList || filesList.length === 0) return;
                            const newFiles = Array.from(filesList);
                            const validFiles = newFiles.filter(file => {
                                if (file.size > 10485760) {
                                    alert(`File ${file.name} terlalu besar. Maksimal 10MB.`);
                                    return false;
                                }
                                return true;
                            });
                            validFiles.forEach(file => {
                                const exists = this.files.some(f => f.name === file.name && f.size === file.size);
                                if (!exists) this.files.push(file);
                            });
                        },
                        removeFile(index) {
                            this.files.splice(index, 1);
                            const input = document.getElementById('attachments');
                            if (input && typeof DataTransfer !== 'undefined') {
                                const dt = new DataTransfer();
                                this.files.forEach(file => dt.items.add(file));
                                input.files = dt.files;
                            }
                        },
                        formatSize(bytes) {
                            if (bytes === 0) return '0 Bytes';
                            const k = 1024;
                            const sizes = ['Bytes', 'KB', 'MB'];
                            const i = Math.floor(Math.log(bytes) / Math.log(k));
                            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
                        }
                    }">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 ml-1">Bukti Digital (Screenshot/Logs)</label>
                        <div
                            @click="$refs.fileInput.click()"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="isDragging = false; handleFiles($event.dataTransfer.files)"
                            :class="isDragging ? 'border-emerald-500 bg-emerald-500/5' : 'border-slate-200 dark:border-slate-700/50 bg-slate-100 dark:bg-slate-900/30'"
                            class="mt-1 flex flex-col items-center justify-center px-10 py-12 border-2 border-dashed rounded-2xl hover:border-emerald-500/50 hover:bg-emerald-500/5 transition-all duration-300 cursor-pointer group shadow-inner">
                            <div class="w-16 h-16 bg-slate-200 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8 text-slate-500 dark:text-slate-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <div class="text-center">
                                <p class="text-slate-700 dark:text-slate-300 font-medium mb-1">Upload atau Drag & Drop file</p>
                                <p class="text-slate-500 text-sm">Maksimal 10MB per file (PNG, JPG, PDF)</p>
                            </div>
                        </div>
                        <input x-ref="fileInput" id="attachments" name="attachments[]" type="file" multiple class="hidden" @change="handleFiles($event.target.files)">
                        
                        <!-- File Preview -->
                        <div x-show="files.length > 0" class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template x-for="(file, index) in files" :key="index">
                                <div class="flex items-center justify-between p-4 bg-white dark:bg-slate-900/80 rounded-xl border border-slate-200 dark:border-slate-800 group hover:border-emerald-500/30 transition-colors shadow-sm">
                                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                                        <div class="w-10 h-10 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center text-slate-500 dark:text-slate-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate" x-text="file.name"></p>
                                            <p class="text-xs text-slate-500 uppercase tracking-widest" x-text="formatSize(file.size)"></p>
                                        </div>
                                    </div>
                                    <button @click.prevent="removeFile(index)" class="text-slate-500 hover:text-rose-500 transition-colors p-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row items-center justify-between pt-8 border-t border-slate-200 dark:border-slate-800 gap-6 transition-colors">
                        <a href="{{ route('portal.ticket.status.form') }}"
                            class="flex items-center space-x-2 text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors group">
                            <div class="w-8 h-8 bg-white dark:bg-slate-900 rounded-lg flex items-center justify-center border border-slate-200 dark:border-slate-800 group-hover:border-emerald-500/30 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium">Lacak Laporan Sebelumnya</span>
                        </a>
                        <button type="submit"
                            class="w-full sm:w-auto px-10 py-4 bg-gradient-to-r from-emerald-600 to-blue-600 hover:from-emerald-500 hover:to-blue-500 text-white font-bold rounded-2xl shadow-[0_10px_25px_-5px_rgba(16,185,129,0.3)] hover:shadow-[0_15px_30px_-5px_rgba(16,185,129,0.4)] transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center space-x-3 group">
                            <span>Kirim Laporan Keamanan</span>
                            <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            
            <p class="text-center mt-12 text-slate-600 text-sm">
                Informasi yang dikirimkan melalui portal ini dilindungi oleh <span class="text-slate-400 underline decoration-emerald-500/30">Protokol Keamanan CSIRT</span>.
            </p>
        </div>
    </div>
    
    @include('components.chatbot-widget')
</body>
</html>