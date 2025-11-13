<x-admin-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit Departemen</h1>
            <p class="text-sm text-gray-600 mt-1">Ubah informasi departemen</p>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <form method="POST" action="{{ route('admin.departments.update', $department) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Departemen *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $department->name) }}" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $department->email) }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="is_public" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="is_public" id="is_public" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="1" {{ old('is_public', $department->is_public) ? 'selected' : '' }}>Public
                        </option>
                        <option value="0" {{ !old('is_public', $department->is_public) ? 'selected' : '' }}>Private
                        </option>
                    </select>
                    @error('is_public')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.departments.index') }}"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                        Batal
                    </a>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>