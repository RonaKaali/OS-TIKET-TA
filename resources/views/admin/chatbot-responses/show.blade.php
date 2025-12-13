<x-admin-layout>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Chatbot Response</h1>
                <p class="text-sm text-gray-600 mt-1">Lihat detail response chatbot</p>
            </div>
            <a href="{{ route('admin.chatbot-responses.index') }}"
                class="text-gray-600 hover:text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6 space-y-6">
        <!-- Keyword -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Keyword / Pertanyaan</label>
            <div class="mt-1 p-3 bg-gray-50 rounded-md border border-gray-200">
                <code class="text-sm font-semibold text-gray-900">{{ $chatbotResponse->keyword }}</code>
            </div>
        </div>

        <!-- Response -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Response / Jawaban</label>
            <div class="mt-1 p-4 bg-gray-50 rounded-md border border-gray-200">
                <pre class="text-sm text-gray-700 whitespace-pre-wrap font-sans">{{ $chatbotResponse->response }}</pre>
            </div>
        </div>

        <!-- Match Type & Priority -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Pencocokan</label>
                <div class="mt-1">
                    <span class="px-3 py-1.5 text-sm font-semibold rounded-full
                        @if($chatbotResponse->match_type === 'exact') bg-blue-100 text-blue-800
                        @elseif($chatbotResponse->match_type === 'starts_with') bg-purple-100 text-purple-800
                        @else bg-green-100 text-green-800
                        @endif">
                        @if($chatbotResponse->match_type === 'exact')
                            Exact (Persis sama)
                        @elseif($chatbotResponse->match_type === 'starts_with')
                            Starts With (Dimulai dengan)
                        @else
                            Contains (Mengandung kata)
                        @endif
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                <div class="mt-1 p-3 bg-gray-50 rounded-md border border-gray-200">
                    <span class="text-lg font-semibold text-gray-900">{{ $chatbotResponse->priority }}</span>
                    <span class="text-sm text-gray-500 ml-2">/ 100</span>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <div class="mt-1">
                @if($chatbotResponse->is_active)
                    <span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                        Aktif
                    </span>
                    <p class="mt-2 text-xs text-gray-500">Response ini aktif dan akan digunakan oleh chatbot</p>
                @else
                    <span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                        Tidak Aktif
                    </span>
                    <p class="mt-2 text-xs text-gray-500">Response ini tidak aktif dan tidak akan digunakan oleh chatbot</p>
                @endif
            </div>
        </div>

        <!-- Timestamps -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat</label>
                <div class="text-sm text-gray-600">
                    {{ $chatbotResponse->created_at->format('d M Y, H:i') }}
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Diperbarui</label>
                <div class="text-sm text-gray-600">
                    {{ $chatbotResponse->updated_at->format('d M Y, H:i') }}
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-3 pt-4 border-t">
            <a href="{{ route('admin.chatbot-responses.index') }}"
                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                Kembali
            </a>
            <a href="{{ route('admin.chatbot-responses.edit', $chatbotResponse) }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Response
            </a>
            <form method="POST" action="{{ route('admin.chatbot-responses.destroy', $chatbotResponse) }}" 
                class="inline"
                onsubmit="return confirmDelete('{{ $chatbotResponse->keyword }}')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 inline-flex items-center focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Response
                </button>
            </form>
        </div>
    </div>

    <script>
        function confirmDelete(keyword) {
            return confirm(`Yakin ingin menghapus response dengan keyword "${keyword}"?\n\nData yang dihapus tidak dapat dikembalikan.`);
        }
    </script>
</x-admin-layout>

