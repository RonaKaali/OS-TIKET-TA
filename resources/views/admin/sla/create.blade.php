<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah SLA Plan</h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('admin.sla.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    placeholder="Contoh: Default SLA, Urgent SLA"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="grace_hours" class="block text-sm font-medium text-gray-700 mb-2">Grace Hours (1-720)
                    *</label>
                <input type="number" name="grace_hours" id="grace_hours" value="{{ old('grace_hours') }}" required
                    min="1" max="720" placeholder="Contoh: 48 untuk 2 hari"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('grace_hours')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('admin.sla.index') }}"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>