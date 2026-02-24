<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Verifikasi Two-Factor Authentication</h2>
        <p class="text-gray-600">Masukkan kode 6 digit dari aplikasi authenticator Anda</p>
    </div>

    <form method="POST" action="{{ route('mfa.verify') }}" class="space-y-6">
        @csrf

        <!-- MFA Code (TOTP) -->
        <div>
            <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                Kode Verifikasi <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="code"
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-center text-2xl tracking-widest font-mono"
                    type="text" name="code" required autofocus autocomplete="one-time-code" maxlength="6"
                    placeholder="000000" pattern="[0-9]{6}" inputmode="numeric">
            </div>
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
            <p class="mt-2 text-sm text-gray-500">
                Buka aplikasi authenticator Anda (Google Authenticator, Authy, dll) dan masukkan kode 6 digit yang ditampilkan.
            </p>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-3 px-4 rounded-lg font-semibold hover:shadow-lg transition-all transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Verifikasi</span>
            </button>
        </div>

        <!-- Help Text -->
        <div class="text-center pt-4 border-t border-gray-200 space-y-1">
            <p class="text-sm text-gray-600">
                Tidak punya akses ke aplikasi authenticator?
            </p>
            <p class="text-sm text-gray-500">
                Anda dapat masuk menggunakan <span class="font-semibold">backup code</span> di halaman terpisah:
                <a href="{{ route('mfa.verify-backup') }}" class="text-blue-600 hover:text-blue-700 font-semibold transition">
                    Verifikasi dengan backup code
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>

