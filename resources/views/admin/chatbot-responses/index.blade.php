<x-admin-layout>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Chatbot Responses</h1>
        <a href="{{ route('admin.chatbot-responses.create') }}"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            + Tambah Response
        </a>
    </div>

    @if(session('ok'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
            <p class="text-green-800">{{ session('ok') }}</p>
        </div>
    @endif

    <!-- Filter & Search -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.chatbot-responses.index') }}" class="flex gap-4 items-end">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Cari keyword atau response..."
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Filter
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.chatbot-responses.index') }}"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 ml-2">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keyword</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Response</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Match Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $item->keyword }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500 max-w-md truncate" title="{{ $item->response }}">
                                {{ Str::limit($item->response, 100) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($item->match_type === 'exact') bg-blue-100 text-blue-800
                                @elseif($item->match_type === 'starts_with') bg-purple-100 text-purple-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ $item->match_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->priority }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Tidak Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.chatbot-responses.show', $item) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors border border-blue-200"
                                    title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.chatbot-responses.edit', $item) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-100 transition-colors border border-indigo-200"
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.chatbot-responses.destroy', $item) }}" 
                                    class="inline"
                                    onsubmit="return confirmDelete('{{ $item->keyword }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-red-50 text-red-700 rounded hover:bg-red-100 transition-colors border border-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada data. <a href="{{ route('admin.chatbot-responses.create') }}"
                                class="text-indigo-600 hover:text-indigo-900">Tambah response pertama</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($items->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
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

