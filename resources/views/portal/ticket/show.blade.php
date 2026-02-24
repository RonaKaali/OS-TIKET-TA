<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Laporan - CSIRT Kalselprov</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .cyber-grid {
            background-image:
                linear-gradient(45deg, rgba(59, 130, 246, 0.05) 25%, transparent 25%),
                linear-gradient(-45deg, rgba(59, 130, 246, 0.05) 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, rgba(59, 130, 246, 0.05) 75%),
                linear-gradient(-45deg, transparent 75%, rgba(59, 130, 246, 0.05) 75%);
            background-size: 30px 30px;
            background-position: 0 0, 0 15px, 15px -15px, -15px 0px;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 cyber-grid py-12 px-4 sm:px-6 lg:px-8">
        <!-- Navigation -->
        <nav class="max-w-7xl mx-auto mb-8">
            <a href="{{ route('portal.ticket.status.form') }}"
                class="inline-flex items-center space-x-2 px-5 py-2.5 bg-white border-2 border-gray-300 rounded-lg text-base font-semibold text-gray-700 hover:text-blue-600 hover:border-blue-500 hover:bg-blue-50 shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Cek Status</span>
            </a>
        </nav>

        <div class="max-w-4xl mx-auto space-y-6">
            <div class="bg-white shadow-lg rounded-lg p-6 sm:p-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $ticket->ticket_number }}</h1>
                        <p class="text-gray-600 mt-1">{{ $ticket->subject }}</p>
                        <p class="text-xs text-gray-500 mt-1">Laporan Insiden Siber</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $ticket->status->name }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Departemen:</span>
                        <span class="ml-2 text-gray-900">{{ $ticket->department->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Prioritas:</span>
                        <span class="ml-2 text-gray-900">{{ $ticket->priority->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Dibuat:</span>
                        <span class="ml-2 text-gray-900">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($ticket->due_at)
                        <div>
                            <span class="text-gray-500">Batas Waktu:</span>
                            <span class="ml-2 text-gray-900">{{ $ticket->due_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Threads -->
            <div class="bg-white shadow-lg rounded-lg p-6 sm:p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Riwayat Komunikasi</h2>
                <div class="space-y-6">
                    @foreach($ticket->threads as $thread)
                        <div
                            class="border-l-4 {{ $thread->type === 'message' ? 'border-blue-500' : 'border-green-500' }} pl-4 py-2">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="text-sm font-medium text-gray-900">
                                        @if($thread->type === 'message')
                                            Anda
                                        @else
                                            {{ $thread->user->name ?? 'Agen' }}
                                        @endif
                                    </span>
                                    <span
                                        class="text-xs text-gray-500 ml-2">{{ $thread->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $thread->body }}</div>

                            @if($thread->attachments->count() > 0)
                                <div class="mt-3">
                                    <p class="text-xs text-gray-500 mb-2">Lampiran:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($thread->attachments as $attachment)
                                            <a href="{{ route('attachments.download', $attachment) }}" target="_blank"
                                                class="inline-flex items-center px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                {{ $attachment->original_filename ?? $attachment->filename }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Reply Form -->
            @if($ticket->status->slug !== 'closed')
                <div class="bg-white shadow-lg rounded-lg p-6 sm:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Balas Laporan</h2>
                    <form method="POST" action="{{ route('portal.ticket.reply', $ticket->ticket_number) }}"
                        enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Pesan *</label>
                            <textarea name="message" id="message" rows="5" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">Lampiran
                                (opsional)</label>
                            <input type="file" name="attachments[]" id="attachments" multiple
                                accept="image/*,.pdf,.doc,.docx"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                            Kirim Balasan
                        </button>
                    </form>
                </div>
            @endif

            <div class="text-center pt-6">
                <a href="{{ route('portal.ticket.create') }}" class="text-blue-600 hover:text-blue-900">
                    Laporkan Insiden Siber
                </a>
            </div>
        </div>
</body>

</html>