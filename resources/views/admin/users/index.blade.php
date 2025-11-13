<x-admin-layout>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen User</h1>
        <a href="{{ route('admin.users.create') }}"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            + Tambah User
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email..."
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Filter
                </button>
                <a href="{{ route('admin.users.index') }}"
                    class="ml-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Success/Error Messages -->
    @if(session('ok'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
            <p class="text-green-800">{{ session('ok') }}</p>
        </div>
    @endif

    @if($errors->has('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
            <p class="text-red-800">{{ $errors->first('error') }}</p>
        </div>
    @endif

    <!-- Users Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terdaftar
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $user->phone ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->roles->isNotEmpty())
                                @foreach($user->roles as $role)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($role->name == 'Super Admin') bg-purple-100 text-purple-800
                                                    @elseif($role->name == 'Admin') bg-blue-100 text-blue-800
                                                    @elseif($role->name == 'Agent') bg-green-100 text-green-800
                                                    @elseif($role->name == 'Support Agent') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            @else
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    User
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.users.edit', $user) }}"
                                class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                    onsubmit="return confirm('Yakin hapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada user ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>