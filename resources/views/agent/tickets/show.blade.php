<x-agent-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $ticket->ticket_number }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $ticket->subject }}</p>
            </div>
            <a href="{{ route('agent.tickets.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Ticket Info -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Laporan</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Status:</span>
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $ticket->status->name }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Prioritas:</span>
                        <span class="text-sm text-gray-900">{{ $ticket->priority->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Departemen:</span>
                        <span class="text-sm text-gray-900">{{ $ticket->department->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Dibuat:</span>
                        <span class="text-sm text-gray-900">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($ticket->due_at)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Batas Waktu:</span>
                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-sm text-gray-900 {{ $ticket->due_at->isPast() ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $ticket->due_at->format('d/m/Y H:i') }}
                                </span>
                                @if($ticket->isOverdue())
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Terlambat ({{ \App\Models\Ticket::countWorkingDays($ticket->due_at) }} hari kerja)
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Threads -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Riwayat Percakapan</h3>
                <div class="space-y-4">
                    @foreach($ticket->threads as $thread)
                        <div
                            class="border-l-4 {{ $thread->type === 'message' ? 'border-blue-500' : ($thread->type === 'reply' ? 'border-green-500' : 'border-gray-500') }} pl-4 py-2">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="text-sm font-medium text-gray-900">
                                        @if($thread->type === 'message')
                                            Pelapor
                                        @elseif($thread->type === 'reply')
                                            {{ $thread->user->name ?? 'Agen' }}
                                        @else
                                            Catatan Internal - {{ $thread->user->name ?? 'Agen' }}
                                        @endif
                                    </span>
                                    <span
                                        class="text-xs text-gray-500 ml-2">{{ $thread->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $thread->body }}</div>

                            @if($thread->attachments->count() > 0)
                                <div class="mt-2">
                                    <p class="text-xs text-gray-500 mb-1">Lampiran:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($thread->attachments as $attachment)
                                            <a href="{{ route('attachments.download', $attachment) }}" target="_blank"
                                                class="text-xs text-indigo-600 hover:text-indigo-900 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                {{ $attachment->original_filename ?? $attachment->filename }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Reply Form -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Balas Laporan</h3>
                <form method="POST" action="{{ route('agent.tickets.reply', $ticket) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-medium text-gray-700">Pesan</label>
                            @if($cannedResponses->count() > 0)
                                <div class="relative">
                                    <button type="button" id="selectTemplateBtn"
                                        class="text-sm text-indigo-600 hover:text-indigo-900 font-medium"
                                        title="Pilih Template / Select Canned Response">
                                        📋 Pilih Template
                                    </button>
                                    <div id="templateDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-10 border border-gray-200 max-h-96 overflow-y-auto">
                                        <div class="p-2">
                                            <input type="text" id="templateSearch" placeholder="Cari template..."
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            <div id="templateList" class="space-y-1">
                                                @foreach($cannedResponses as $template)
                                                    <button type="button" 
                                                        class="w-full text-left px-3 py-2 text-sm hover:bg-indigo-50 rounded-md border border-transparent hover:border-indigo-200 template-item"
                                                        data-title="{{ json_encode($template->title) }}"
                                                        data-body="{{ json_encode($template->body) }}">
                                                        <div class="font-medium text-gray-900">{{ $template->title }}</div>
                                                        <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit($template->body, 80) }}</div>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <textarea name="message" id="messageTextarea" rows="5" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('message') }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran (opsional)</label>
                        <input type="file" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-500 mt-1">Maksimal 10MB per file</p>
                    </div>
                    <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Kirim Balasan
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Update -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ubah Status</h3>
                <form method="POST" action="{{ route('agent.tickets.status', $ticket) }}">
                    @csrf
                    <select name="status_id"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mb-3">
                        @foreach(\App\Models\Status::all() as $status)
                            <option value="{{ $status->id }}" {{ $ticket->status_id == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        Perbarui Status
                    </button>
                </form>
            </div>

            <!-- Assignment -->
            @can('tickets.assign')
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Penugasan</h3>
                    @if($ticket->assignee)
                        <p class="text-sm text-gray-600 mb-3">Ditugaskan ke: <strong>{{ $ticket->assignee->name }}</strong></p>
                    @else
                        <p class="text-sm text-gray-600 mb-3">Belum ditugaskan</p>
                    @endif
                    <form method="POST" action="{{ route('agent.tickets.assign', $ticket) }}">
                        @csrf
                        <select name="user_id"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mb-3">
                            <option value="">Pilih Agen</option>
                            @foreach($agents ?? [] as $user)
                                <option value="{{ $user->id }}" {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                    @if($user->roles->isNotEmpty())
                                        ({{ $user->roles->first()->name }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <button type="submit"
                        class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Tugaskan
                        </button>
                    </form>
                </div>
            @endcan

            <!-- Internal Note -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan Internal</h3>
                <form method="POST" action="{{ route('agent.tickets.note', $ticket) }}">
                    @csrf
                    <textarea name="note" rows="4" placeholder="Catatan internal (hanya untuk agen)"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mb-3"></textarea>
                    <button type="submit"
                        class="w-full bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700">
                        Tambah Catatan
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(isset($cannedResponses) && $cannedResponses->count() > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectBtn = document.getElementById('selectTemplateBtn');
            const dropdown = document.getElementById('templateDropdown');
            const searchInput = document.getElementById('templateSearch');
            const templateList = document.getElementById('templateList');
            const messageTextarea = document.getElementById('messageTextarea');
            const templateItems = document.querySelectorAll('.template-item');

            // Toggle dropdown
            if (selectBtn && dropdown) {
                selectBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                    if (!dropdown.classList.contains('hidden')) {
                        searchInput.focus();
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target) && e.target !== selectBtn) {
                        dropdown.classList.add('hidden');
                    }
                });
            }

            // Search functionality
            if (searchInput && templateItems.length > 0) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    templateItems.forEach(item => {
                        const title = item.getAttribute('data-title').toLowerCase();
                        const body = item.getAttribute('data-body').toLowerCase();
                        if (title.includes(searchTerm) || body.includes(searchTerm)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // Insert template into textarea
            templateItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Parse JSON dari data attribute
                    let body, title;
                    try {
                        body = JSON.parse(this.getAttribute('data-body'));
                        title = JSON.parse(this.getAttribute('data-title'));
                    } catch(e) {
                        // Fallback jika bukan JSON (untuk kompatibilitas)
                        body = this.getAttribute('data-body');
                        title = this.getAttribute('data-title');
                    }
                    
                    // Replace placeholder jika ada
                    let templateBody = body;
                    templateBody = templateBody.replace(/\{\{TICKET_NUMBER\}\}/g, '{{ $ticket->ticket_number }}');
                    templateBody = templateBody.replace(/\{\{SUBJECT\}\}/g, '{{ $ticket->subject }}');
                    templateBody = templateBody.replace(/\{\{REPORTER_NAME\}\}/g, '{{ $ticket->requester->name ?? $ticket->reporter_name ?? "Pelapor" }}');
                    
                    // Jika textarea sudah ada isi, tanyakan apakah ingin mengganti atau menambahkan
                    if (messageTextarea.value.trim() !== '') {
                        if (confirm('Textarea sudah berisi teks. Apakah Anda ingin menggantinya dengan template "' + title + '"?\n\nKlik OK untuk mengganti, Cancel untuk membatalkan.')) {
                            messageTextarea.value = templateBody;
                        }
                    } else {
                        messageTextarea.value = templateBody;
                    }
                    
                    // Focus ke textarea dan scroll ke bawah sedikit
                    messageTextarea.focus();
                    const len = templateBody.length;
                    messageTextarea.setSelectionRange(len, len);
                    
                    // Close dropdown
                    if (dropdown) {
                        dropdown.classList.add('hidden');
                    }
                });
            });
        });
    </script>
    @endif
</x-agent-layout>