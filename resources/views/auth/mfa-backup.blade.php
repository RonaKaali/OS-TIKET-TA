<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="mx-auto w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6m-3-9a3 3 0 110-6 3 3 0 010 6z" />
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Verifikasi dengan Backup Code</h2>
        <p class="text-gray-600">Gunakan salah satu backup code yang Anda simpan saat setup Two-Factor Authentication</p>
    </div>

    <form method="POST" action="{{ route('mfa.verify-backup') }}" class="space-y-6">
        @csrf

        <!-- Backup Code -->
        <div>
            <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                Backup Code <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 11c0-1.105-.895-2-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h5a2 2 0 002-2v-6zm4-5V4a2 2 0 012-2h1a2 2 0 012 2v2M9 7h6" />
                    </svg>
                </div>
                <input id="code"
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition text-center text-xl tracking-widest font-mono"
                    type="text" name="code" required autofocus maxlength="32"
                    placeholder="contoh: AB12-CD34" inputmode="text">
            </div>
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
            <p class="mt-2 text-sm text-gray-500">
                Masukkan tepat salah satu backup code yang ditampilkan dan Anda simpan saat pertama kali mengaktifkan 2FA.
                Setiap backup code hanya bisa digunakan <span class="font-semibold">satu kali</span>.
            </p>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                class="w-full bg-gradient-to-r from-purple-600 to-indigo-700 text-white py-3 px-4 rounded-lg font-semibold hover:shadow-lg transition-all transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Verifikasi dengan Backup Code</span>
            </button>
        </div>

        <!-- Help Text -->
        <div class="text-center pt-4 border-t border-gray-200 space-y-1">
            <p class="text-sm text-gray-600">
                Masih punya akses ke aplikasi authenticator?
            </p>
            <p class="text-sm text-gray-500">
                Anda juga bisa kembali menggunakan verifikasi dengan kode 6 digit:
                <a href="{{ route('mfa.verify') }}" class="text-blue-600 hover:text-blue-700 font-semibold transition">
                    Verifikasi dengan aplikasi authenticator
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>

