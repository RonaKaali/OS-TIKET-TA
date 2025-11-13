<x-admin-layout>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Status</h1>
        <a href="{{ route('admin.statuses.create') }}"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            + Tambah Status
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Closing</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->slug }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->is_closing ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $item->is_closing ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.statuses.edit', $item) }}"
                                class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form method="POST" action="{{ route('admin.statuses.destroy', $item) }}" class="inline"
                                onsubmit="return confirm('Yakin hapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td>
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