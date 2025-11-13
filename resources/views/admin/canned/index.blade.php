<x-admin-layout>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Canned Responses</h1>
        <a href="{{ route('admin.canned.create') }}"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            + Tambah Template
        </a>
    </div>

    @if(session('ok'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
            <p class="text-green-800">{{ session('ok') }}</p>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Body Preview</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->title }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($item->body, 100) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.canned.edit', $item) }}"
                                class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form method="POST" action="{{ route('admin.canned.destroy', $item) }}" class="inline"
                                onsubmit="return confirm('Yakin hapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td>
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
</x-admin-layout>