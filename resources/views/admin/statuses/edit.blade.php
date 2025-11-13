<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Status</h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('admin.statuses.update', $status) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $status->name) }}" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug *</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $status->slug) }}" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="is_closing" class="block text-sm font-medium text-gray-700 mb-2">Status Closing *</label>
                <select name="is_closing" id="is_closing" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="0" {{ old('is_closing', $status->is_closing) == false ? 'selected' : '' }}>Tidak
                    </option>
                    <option value="1" {{ old('is_closing', $status->is_closing) == true ? 'selected' : '' }}>Ya</option>
                </select>
                @error('is_closing')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('admin.statuses.index') }}"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Update
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>