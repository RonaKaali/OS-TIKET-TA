<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal - Dashboard Tiket Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950">

<!-- Header -->
<div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">Tiket Saya</h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Pantau status semua laporan Anda</p>
            </div>
            <a href="{{ route('portal.ticket.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-black rounded-lg uppercase tracking-widest transition-colors">
                + Buat Laporan
            </a>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Total Laporan</p>
            <p class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Aktif</p>
            <p class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ $stats['open'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Selesai</p>
            <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['closed'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 mb-6">
        <form method="GET" action="{{ route('portal.dashboard') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" placeholder="Cari nomor tiket atau judul..." 
                value="{{ request('search') }}"
                class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            
            <select name="sort_by" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Terbaru</option>
                <option value="updated_at" {{ request('sort_by') === 'updated_at' ? 'selected' : '' }}>Diupdate</option>
                <option value="priority_id" {{ request('sort_by') === 'priority_id' ? 'selected' : '' }}>Prioritas</option>
                <option value="status_id" {{ request('sort_by') === 'status_id' ? 'selected' : '' }}>Status</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-black rounded-lg uppercase tracking-widest transition-colors">
                Cari
            </button>
        </form>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        @if($tickets->isEmpty())
            <div class="p-12 text-center text-slate-500 dark:text-slate-400">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-lg font-bold">Belum ada laporan</p>
                <p class="text-sm mt-2">Mulai buat laporan pertama Anda sekarang</p>
                <a href="{{ route('portal.ticket.create') }}" class="inline-block mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-black rounded-lg uppercase tracking-widest transition-colors">
                    Buat Laporan
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900">
                            <th class="px-6 py-4 text-left font-black text-slate-900 dark:text-white uppercase tracking-widest text-[10px]">Nomor Tiket</th>
                            <th class="px-6 py-4 text-left font-black text-slate-900 dark:text-white uppercase tracking-widest text-[10px]">Judul</th>
                            <th class="px-6 py-4 text-left font-black text-slate-900 dark:text-white uppercase tracking-widest text-[10px]">Status</th>
                            <th class="px-6 py-4 text-left font-black text-slate-900 dark:text-white uppercase tracking-widest text-[10px]">Prioritas</th>
                            <th class="px-6 py-4 text-left font-black text-slate-900 dark:text-white uppercase tracking-widest text-[10px]">Balasan</th>
                            <th class="px-6 py-4 text-left font-black text-slate-900 dark:text-white uppercase tracking-widest text-[10px]">Dibuat</th>
                            <th class="px-6 py-4 text-center font-black text-slate-900 dark:text-white uppercase tracking-widest text-[10px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-black text-emerald-600 dark:text-emerald-400 text-xs">{{ $ticket->ticket_number }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900 dark:text-white text-sm max-w-xs truncate">{{ $ticket->subject }}</p>
                                    <p class="text-[10px] text-slate-500 mt-1">{{ $ticket->department?->name ?? 'Umum' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'open' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                                            'answered' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                                            'in_progress' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300',
                                            'closed' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                                            'resolved' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                                        ];
                                        $color = $statusColors[$ticket->status?->slug] ?? 'bg-slate-100 dark:bg-slate-900/30 text-slate-700 dark:text-slate-300';
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest {{ $color }}">
                                        {{ $ticket->status?->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $priorityColors = [
                                            'Low' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                                            'Medium' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                                            'High' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300',
                                            'Urgent' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                                        ];
                                        $pColor = $priorityColors[$ticket->priority?->name] ?? 'bg-slate-100 dark:bg-slate-900/30 text-slate-700 dark:text-slate-300';
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest {{ $pColor }}">
                                        {{ $ticket->priority?->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white rounded text-xs font-bold">
                                        {{ $ticket->threads_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-[10px] text-slate-600 dark:text-slate-400">
                                    {{ $ticket->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('portal.ticket.show', $ticket->ticket_number) }}" 
                                       class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black rounded transition-colors">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $tickets->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>

</body>
</html>
