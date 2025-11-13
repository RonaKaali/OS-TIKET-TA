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
                                            <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank"
                                                class="text-xs text-indigo-600 hover:text-indigo-900 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                {{ $attachment->filename }}
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                        <textarea name="message" rows="5" required
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
                        Update Status
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
                            Assign
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
</x-agent-layout>