<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Setup Two-Factor Authentication</h2>
                <p class="text-sm text-gray-600 mt-1">Tingkatkan keamanan akun Anda dengan 2FA</p>
            </div>
            <a href="{{ route('profile.edit') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-md transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg p-6 sm:p-8">
                <div class="space-y-6">
                    <!-- Step 1: Install App -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Langkah 1: Install Aplikasi Authenticator</h3>
                        <p class="text-gray-600 mb-4">
                            Jika Anda belum memiliki aplikasi authenticator, install salah satu aplikasi berikut:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Google Authenticator</h4>
                                <p class="text-sm text-gray-600 mb-3">Aplikasi authenticator resmi dari Google</p>
                                <div class="flex space-x-2">
                                    <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" 
                                       target="_blank" 
                                       class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        Android
                                    </a>
                                    <span class="text-gray-400">|</span>
                                    <a href="https://apps.apple.com/app/google-authenticator/id388497605" 
                                       target="_blank" 
                                       class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        iOS
                                    </a>
                                </div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Authy</h4>
                                <p class="text-sm text-gray-600 mb-3">Multi-device authenticator</p>
                                <div class="flex space-x-2">
                                    <a href="https://play.google.com/store/apps/details?id=com.authy.authy" 
                                       target="_blank" 
                                       class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        Android
                                    </a>
                                    <span class="text-gray-400">|</span>
                                    <a href="https://apps.apple.com/app/authy/id494168017" 
                                       target="_blank" 
                                       class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        iOS
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Scan QR Code -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Langkah 2: Scan QR Code</h3>
                        <p class="text-gray-600 mb-4">
                            Buka aplikasi authenticator Anda dan scan QR code di bawah ini:
                        </p>
                        
                        <div class="flex justify-center mb-4">
                            <div class="bg-white p-4 border-2 border-gray-200 rounded-lg inline-block">
                                @if(class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
                                    {!! QrCode::size(250)->generate($qrUrl) !!}
                                @else
                                    <div class="w-64 h-64 bg-gray-100 flex items-center justify-center rounded">
                                        <p class="text-gray-500 text-sm text-center px-4">
                                            Install package: <br>
                                            <code class="bg-gray-200 px-2 py-1 rounded">composer require simplesoftwareio/simple-qrcode</code>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Manual Entry -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Tidak bisa scan QR code?</p>
                            <p class="text-sm text-gray-600 mb-2">Masukkan kode berikut secara manual:</p>
                            <div class="flex items-center space-x-2">
                                <code class="flex-1 bg-white border border-gray-300 rounded px-3 py-2 font-mono text-sm text-gray-900 break-all">{{ $secret }}</code>
                                <button type="button" 
                                        onclick="navigator.clipboard.writeText('{{ $secret }}')"
                                        class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm">
                                    Copy
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Verify -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Langkah 3: Verifikasi</h3>
                        <p class="text-gray-600 mb-4">
                            Masukkan kode 6 digit dari aplikasi authenticator Anda untuk mengaktifkan 2FA:
                        </p>

                        <form method="POST" action="{{ route('mfa.enable') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="secret_token" value="{{ encrypt($secret) }}">

                            <div>
                                <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Kode Verifikasi <span class="text-red-500">*</span>
                                </label>
                                <input id="code"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-center text-2xl tracking-widest font-mono"
                                    type="text" name="code" required autofocus autocomplete="one-time-code" maxlength="6"
                                    placeholder="000000" pattern="[0-9]{6}" inputmode="numeric">
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>

                            <div class="flex space-x-4">
                                <button type="submit"
                                    class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-3 px-4 rounded-lg font-semibold hover:shadow-lg transition-all transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Aktifkan 2FA</span>
                                </button>
                                <a href="{{ route('profile.edit') }}"
                                    class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">Tips Keamanan</h4>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    <li>• Simpan backup codes yang akan diberikan setelah aktivasi</li>
                                    <li>• Jangan share QR code atau secret key dengan siapapun</li>
                                    <li>• Pastikan waktu di smartphone sudah benar (sinkronisasi waktu)</li>
                                    <li>• Setelah 2FA aktif, Anda akan diminta kode setiap kali login</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

