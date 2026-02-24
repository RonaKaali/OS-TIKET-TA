<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Backup Codes</h2>
                <p class="text-sm text-gray-600 mt-1">Simpan kode-kode ini di tempat yang aman</p>
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
                @if(session('status'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-green-800">{{ session('status') }}</p>
                    </div>
                @endif

                <div class="space-y-6">
                    <!-- Warning -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <h4 class="font-semibold text-yellow-900 mb-1">PENTING: Simpan Backup Codes Ini!</h4>
                                <p class="text-sm text-yellow-800">
                                    Backup codes ini hanya ditampilkan sekali. Simpan di tempat yang aman. 
                                    Anda akan membutuhkannya jika kehilangan akses ke aplikasi authenticator Anda.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Backup Codes -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Backup Codes Anda</h3>
                        <div class="bg-gray-50 border-2 border-gray-200 rounded-lg p-6">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                @foreach($backupCodes as $code)
                                    <div class="bg-white border border-gray-300 rounded px-4 py-3 font-mono text-center text-lg font-semibold text-gray-900">
                                        {{ $code }}
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" 
                                    onclick="printBackupCodes()"
                                    class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print atau Simpan sebagai PDF
                            </button>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-900 mb-2">Cara Menggunakan Backup Codes</h4>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Gunakan backup code jika Anda tidak bisa mengakses aplikasi authenticator</li>
                            <li>• Setiap backup code hanya bisa digunakan sekali</li>
                            <li>• Setelah digunakan, backup code tersebut tidak bisa digunakan lagi</li>
                            <li>• Simpan backup codes di tempat yang aman dan tidak mudah diakses orang lain</li>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end">
                        <a href="{{ route('profile.edit') }}"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-lg hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                            Saya Sudah Menyimpan Backup Codes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printBackupCodes() {
            const printWindow = window.open('', '_blank');
            const codes = @json($backupCodes);
            
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Backup Codes - {{ config('app.name') }}</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; }
                            h1 { color: #333; }
                            .codes { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 20px 0; }
                            .code { border: 1px solid #ccc; padding: 10px; text-align: center; font-family: monospace; font-size: 18px; }
                            .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px; }
                        </style>
                    </head>
                    <body>
                        <h1>Backup Codes - {{ config('app.name') }}</h1>
                        <div class="warning">
                            <strong>PENTING:</strong> Simpan dokumen ini di tempat yang aman. 
                            Backup codes ini hanya ditampilkan sekali.
                        </div>
                        <div class="codes">
                            ${codes.map(code => `<div class="code">${code}</div>`).join('')}
                        </div>
                        <p><strong>Tanggal:</strong> {{ now()->format('d F Y H:i') }}</p>
                        <p><strong>User:</strong> {{ Auth::user()->email }}</p>
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</x-app-layout>

