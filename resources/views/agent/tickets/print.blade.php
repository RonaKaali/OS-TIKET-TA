<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Tugas - {{ $ticket->ticket_number }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: white;
            color: black;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        @page {
            size: A4;
            margin: 0;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 2cm !important;
                width: 100%;
                height: 100%;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center py-8">
    
    <!-- Tombol navigasi (sembunyi saat diprint) -->
    <div class="no-print mb-6 flex gap-4">
        <button onclick="window.print()" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-md transition">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
            Cetak Dokumen
        </button>
        <button onclick="window.close()" class="px-6 py-2.5 bg-slate-600 hover:bg-slate-700 text-white font-bold rounded-lg shadow-md transition">
            Tutup Tab
        </button>
    </div>

    <!-- Area Kertas -->
    <div class="print-container bg-white border border-slate-300 w-[210mm] min-h-[297mm] shadow-2xl p-10 relative overflow-hidden">
        
        <!-- Header Aksen (Opsional, tapi bagus untuk cetak berwarna) -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-emerald-500 via-emerald-400 to-cyan-400"></div>

        <!-- Kop Surat -->
        <div class="flex items-start justify-between gap-6 mt-4">
            <div class="w-24 h-24 flex-shrink-0">
                <img src="{{ asset('images/logo-kalselprov.png') }}" alt="Logo Provinsi" class="w-full h-full object-contain drop-shadow-sm">
            </div>
            <div class="flex-1 text-center">
                <p class="text-sm font-semibold text-slate-600 uppercase tracking-widest">Pemerintah Provinsi Kalimantan Selatan</p>
                <h2 class="text-2xl font-extrabold text-slate-900 uppercase tracking-tight mt-1">Dinas Komunikasi dan Informatika</h2>
                <h3 class="text-lg font-black text-emerald-700 tracking-[0.25em] uppercase mt-1">CSIRT Kalselprov</h3>
                <p class="text-xs text-slate-500 font-medium mt-1.5">Jl. Dharma Praja II, Kawasan Perkantoran Pemprov Kalsel, Banjarbaru</p>
            </div>
            <div class="w-24 h-24 flex items-center justify-center rounded-2xl bg-emerald-50 border border-emerald-200 flex-shrink-0">
                <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        </div>

        <div class="mt-6 border-t-2 border-b-2 border-double border-slate-800 py-1.5">
            <div class="border-t border-slate-300"></div>
        </div>
    
        <!-- Document Title -->
        <div class="text-center my-8">
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-wide underline decoration-2 decoration-slate-800 underline-offset-4">Surat Tugas Penanganan Insiden Siber</h3>
            <p class="text-sm font-bold text-slate-600 font-mono mt-2 tracking-wider">Nomor: ST/{{ $ticket->ticket_number }}/CSIRT/{{ $ticket->created_at->format('Y') }}</p>
        </div>

        <!-- Preamble -->
        <div class="text-base text-slate-800 mb-8 leading-relaxed font-medium text-justify">
            Menimbang urgensi keamanan siber dan perlu mitigasi insiden secara cepat pada infrastruktur teknologi informasi Pemerintah Provinsi Kalimantan Selatan, Kepala Dinas Komunikasi dan Informatika menginstruksikan kepada analis berikut untuk melaksanakan penanganan insiden:
        </div>

        <!-- Grid Info -->
        <div class="grid grid-cols-2 gap-8 p-6 bg-slate-50 rounded-2xl border border-slate-200 mb-8">
            <div>
                <h4 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4">Penerima Tugas (Analis)</h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                        <span class="text-sm font-medium text-slate-600">Nama</span>
                        <span class="text-sm font-extrabold text-slate-900">{{ $ticket->assignee->name ?? 'Belum Ditugaskan' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                        <span class="text-sm font-medium text-slate-600">Jabatan</span>
                        <span class="text-sm font-extrabold text-slate-900">Analis Siber TI</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-600">Unit Kerja</span>
                        <span class="text-sm font-extrabold text-slate-900">CSIRT Kalselprov</span>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4">Rincian Insiden</h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                        <span class="text-sm font-medium text-slate-600">Nomor Laporan</span>
                        <span class="text-sm font-mono font-extrabold text-slate-900">{{ $ticket->ticket_number }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                        <span class="text-sm font-medium text-slate-600">Sektor / Instansi</span>
                        <span class="text-sm font-extrabold text-slate-900">{{ $ticket->department->name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-600">Kedaruratan</span>
                        <span class="text-sm font-extrabold text-red-600">{{ $ticket->priority?->name ?? 'Normal' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Description Table -->
        <div class="border border-slate-200 rounded-2xl overflow-hidden mb-8">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-black text-slate-600 uppercase tracking-wider">Deskripsi Tugas Resmi</th>
                        <th class="px-6 py-4 text-xs font-black text-slate-600 uppercase tracking-wider text-center">Batas Waktu (SLA)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white border-b border-slate-100">
                        <td class="px-6 py-5">
                            <div class="text-base font-extrabold text-slate-900 mb-2">{{ $ticket->subject }}</div>
                            <div class="text-sm text-slate-600 leading-relaxed">
                                Melaksanakan tindakan mitigasi, pemulihan, serta forensik digital pada infrastruktur {{ $ticket->department->name }} sehubungan dengan laporan siber ini. Laporan kronologi harus diunggah lengkap melalui log aktivitas sistem.
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center whitespace-nowrap">
                            @if($ticket->due_at)
                                <div class="inline-block font-mono font-bold text-sm text-slate-800">
                                    {{ $ticket->due_at->format('d M Y, H:i') }}
                                </div>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Spacer for Signature to fall to bottom -->
        <div class="flex-grow min-h-[100px]"></div>

        <!-- Footer: Sign-off -->
        <div class="flex justify-between items-end mt-12 pt-8 border-t border-slate-200">
            <!-- Left: Badge -->
            <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-black text-emerald-700 uppercase tracking-widest">Zero Trust Verified</div>
                    <div class="text-xs font-semibold text-slate-500">Secured &amp; Digitally Signed</div>
                </div>
            </div>

            <!-- Right: QR + Signatory -->
            <div class="flex items-center gap-6">
                <div class="text-right">
                    <p class="text-xs font-black text-slate-500 uppercase tracking-widest">Banjarbaru, {{ $ticket->created_at->translatedFormat('d F Y') }}</p>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-widest mt-1">Penetap Tugas,</p>
                    <p class="text-lg font-black text-slate-900 mt-12">Kepala CSIRT Kalselprov</p>
                    <p class="text-xs font-mono text-slate-500 mt-0.5">ID: ST-{{ substr($ticket->uuid, 0, 8) }}</p>
                </div>
                <div class="p-2 bg-white border-2 border-slate-200 rounded-xl">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(72)->generate(route('agent.tickets.show', $ticket)) !!}
                </div>
            </div>
        </div>

    </div>

    <!-- Auto Print Script -->
    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500); // Tunggu setengah detik agar font & css selesai dirender
        };
    </script>
</body>
</html>
