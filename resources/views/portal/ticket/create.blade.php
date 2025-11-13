<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporkan Insiden Siber - CSIRT Kalselprov</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .cyber-grid {
            background-image:
                linear-gradient(45deg, rgba(59, 130, 246, 0.05) 25%, transparent 25%),
                linear-gradient(-45deg, rgba(59, 130, 246, 0.05) 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, rgba(59, 130, 246, 0.05) 75%),
                linear-gradient(-45deg, transparent 75%, rgba(59, 130, 246, 0.05) 75%);
            background-size: 30px 30px;
            background-position: 0 0, 0 15px, 15px -15px, -15px 0px;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 cyber-grid py-12 px-4 sm:px-6 lg:px-8">
        <!-- Navigation -->
        <nav class="max-w-7xl mx-auto mb-8">
            <a href="{{ route('welcome') }}"
                class="inline-flex items-center space-x-2 px-5 py-2.5 bg-white border-2 border-gray-300 rounded-lg text-base font-semibold text-gray-700 hover:text-blue-600 hover:border-blue-500 hover:bg-blue-50 shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
        </nav>

        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl shadow-lg mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Laporkan Insiden Siber</h1>
                <p class="text-gray-600">Isi form di bawah ini untuk melaporkan insiden keamanan siber. Tim CSIRT akan
                    segera menanggapi laporan Anda.</p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <form method="POST" action="{{ route('portal.ticket.store') }}" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf

                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">
                            Subjek <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                            placeholder="Contoh: Masalah dengan login akun"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Help Topic -->
                    <div>
                        <label for="help_topic_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="help_topic_id" id="help_topic_id" required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">
                            <option value="">Pilih Kategori</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ old('help_topic_id') == $topic->id ? 'selected' : '' }}>
                                    {{ $topic->department->name }} - {{ $topic->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('help_topic_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority_id"
                            class="block text-sm font-semibold text-gray-700 mb-2">Prioritas</label>
                        <select name="priority_id" id="priority_id"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">
                            <option value="">Normal</option>
                            @foreach(\App\Models\Priority::orderBy('weight')->get() as $priority)
                                <option value="{{ $priority->id }}" {{ old('priority_id') == $priority->id ? 'selected' : '' }}>
                                    {{ $priority->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Requester Info (Read-only dari user yang login) -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Informasi Pelapor</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Nama</label>
                                <p class="text-sm text-gray-900 font-medium">{{ Auth::user()->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                                <p class="text-sm text-gray-900 font-medium">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                            Pesan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" id="message" rows="6" required
                            placeholder="Jelaskan masalah atau permintaan Anda secara detail..."
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Attachments -->
                    <div x-data="{ 
                        files: [],
                        isDragging: false,
                        handleFiles(filesList) {
                            if (!filesList || filesList.length === 0) return;
                            const newFiles = Array.from(filesList);
                            // Validasi ukuran file (10MB = 10485760 bytes)
                            const validFiles = newFiles.filter(file => {
                                if (file.size > 10485760) {
                                    alert(`File ${file.name} terlalu besar. Maksimal 10MB.`);
                                    return false;
                                }
                                return true;
                            });
                            // Tambahkan file baru ke array
                            validFiles.forEach(file => {
                                // Cek apakah file sudah ada (berdasarkan name dan size)
                                const exists = this.files.some(f => f.name === file.name && f.size === file.size);
                                if (!exists) {
                                    this.files.push(file);
                                }
                            });
                        },
                        removeFile(index) {
                            this.files.splice(index, 1);
                            // Update file input dengan DataTransfer
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
                        <label for="attachments" class="block text-sm font-semibold text-gray-700 mb-2">
                            Lampiran <span class="text-gray-500 font-normal">(opsional)</span>
                        </label>
                        <div
                            @click="$refs.fileInput.click()"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="isDragging = false; handleFiles($event.dataTransfer.files)"
                            :class="isDragging ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300'"
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-lg hover:border-indigo-400 transition cursor-pointer">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                    viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-4h12m-6 4h12"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <span class="font-medium text-indigo-600">Klik untuk upload</span>
                                    <span class="pl-1">atau drag and drop</span>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, PDF, DOC, DOCX hingga 10MB</p>
                            </div>
                        </div>
                        <input 
                            x-ref="fileInput"
                            id="attachments" 
                            name="attachments[]" 
                            type="file" 
                            multiple
                            accept="image/*,.pdf,.doc,.docx" 
                            class="hidden"
                            @change="handleFiles($event.target.files)"
                            x-init="
                                // Keep original files in sync with Alpine state for drag & drop
                                $watch('files', (newFiles) => {
                                    // Update the input's files when files array changes
                                    if (newFiles && newFiles.length > 0 && typeof DataTransfer !== 'undefined') {
                                        const dt = new DataTransfer();
                                        newFiles.forEach(file => dt.items.add(file));
                                        $refs.fileInput.files = dt.files;
                                    }
                                });
                            ">
                        
                        <!-- File Preview -->
                        <div x-show="files.length > 0" class="mt-4 space-y-2">
                            <template x-for="(file, index) in files" :key="index">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate" x-text="file.name"></p>
                                            <p class="text-xs text-gray-500" x-text="formatSize(file.size)"></p>
                                        </div>
                                    </div>
                                    <button 
                                        type="button"
                                        @click.prevent="removeFile(index)"
                                        class="ml-3 text-red-600 hover:text-red-800 flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        
                        @error('attachments.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <a href="{{ route('portal.ticket.status.form') }}"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Cek Status Laporan
                        </a>
                        <button type="submit"
                            class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <span>Kirim Laporan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>