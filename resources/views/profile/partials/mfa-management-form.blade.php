<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Two-Factor Authentication (2FA)') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Tingkatkan keamanan akun Anda dengan Two-Factor Authentication. Setelah diaktifkan, Anda akan diminta kode verifikasi setiap kali login.') }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        <!-- Status MFA -->
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    @if($mfaEnabled)
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    @else
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">
                        Status 2FA
                    </h3>
                    <p class="text-sm text-gray-600">
                        @if($mfaEnabled)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Aktif
                            </span>
                            @if($user->mfa_enabled_at)
                                <span class="text-xs text-gray-500 ml-2">
                                    Diaktifkan pada {{ \Carbon\Carbon::parse($user->mfa_enabled_at)->format('d M Y H:i') }}
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                Belum Aktif
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-4">
            @if($mfaEnabled)
                <!-- Disable MFA -->
                <form method="POST" action="{{ route('mfa.disable') }}" class="inline">
                    @csrf
                    <x-danger-button type="submit"
                        onclick="return confirm('Apakah Anda yakin ingin menonaktifkan 2FA? Akun Anda akan menjadi kurang aman.')">
                        {{ __('Nonaktifkan 2FA') }}
                    </x-danger-button>
                </form>
                <a href="{{ route('mfa.backup-codes') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-md transition text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Lihat Backup Codes
                </a>
            @else
                <!-- Setup MFA -->
                <a href="{{ route('mfa.setup') }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-lg hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    {{ __('Aktifkan 2FA') }}
                </a>
            @endif
        </div>

        <!-- Info -->
        @if($mfaEnabled)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="font-semibold text-blue-900 mb-1">2FA Aktif</h4>
                        <p class="text-sm text-blue-800">
                            Akun Anda dilindungi dengan Two-Factor Authentication. Setiap kali login, Anda akan diminta kode verifikasi dari aplikasi authenticator Anda.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h4 class="font-semibold text-yellow-900 mb-1">2FA Belum Aktif</h4>
                        <p class="text-sm text-yellow-800">
                            Akun Anda belum dilindungi dengan Two-Factor Authentication. Klik tombol "Aktifkan 2FA" di atas untuk meningkatkan keamanan akun Anda.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tips -->
        <div class="border-t border-gray-200 pt-4">
            <h4 class="text-sm font-semibold text-gray-900 mb-2">Tips Keamanan:</h4>
            <ul class="text-sm text-gray-600 space-y-1">
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-gray-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    Simpan backup codes di tempat yang aman
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-gray-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    Jangan share QR code atau secret key dengan siapapun
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-gray-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    Pastikan waktu di smartphone sudah benar (sinkronisasi waktu)
                </li>
            </ul>
        </div>
    </div>
</section>

