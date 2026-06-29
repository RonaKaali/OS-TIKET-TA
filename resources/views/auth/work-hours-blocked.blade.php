<x-guest-layout>
    @php
        $riskScore = max(0, min(100, (int) config('zero_trust.risk_score_threshold_high', 70)));
    @endphp

    <div class="relative py-1 text-center">
        <div class="pointer-events-none absolute -left-16 top-24 h-32 w-32 rounded-full bg-rose-400/10 blur-3xl"></div>

        <div class="relative mx-auto mb-6 h-24 w-24" aria-hidden="true">
            <div class="absolute inset-2 rounded-full bg-rose-400/20 blur-2xl animate-pulse"></div>
            <div class="relative flex h-full w-full items-center justify-center rounded-[2rem] border border-rose-200/80 bg-gradient-to-br from-rose-50 to-white shadow-[0_18px_45px_rgba(244,63,94,0.16)] dark:border-rose-500/20 dark:from-rose-950/60 dark:to-slate-900">
                <svg class="h-11 w-11 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                        d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.949 3.374H4.646c-1.732 0-2.815-1.874-1.949-3.374L10.05 3.388c.866-1.5 3.034-1.5 3.9 0l7.353 12.738ZM12 16.5h.008v.008H12V16.5Z" />
                </svg>
            </div>
        </div>

        <div class="mb-6">
            <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-[9px] font-black uppercase tracking-[0.22em] text-rose-600 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400">
                <span class="h-1.5 w-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                Kebijakan Jam Kerja Aktif
            </div>
            <h1 class="text-3xl font-black tracking-tight text-slate-950 dark:text-white">Akses Ditolak</h1>
            <p class="mx-auto mt-3 max-w-sm text-sm font-semibold leading-relaxed text-slate-500 dark:text-slate-400">
                Login dilakukan di luar jam kerja yang diizinkan. Demi keamanan sistem, sesi Anda tidak dibuat.
            </p>
        </div>

        <div class="space-y-3 rounded-2xl border border-rose-200/80 bg-rose-50/70 p-5 text-left shadow-inner dark:border-rose-500/20 dark:bg-rose-950/20">
            <div class="flex items-center justify-between gap-4">
                <span class="text-[9px] font-black uppercase tracking-[0.18em] text-slate-400">Waktu Akses</span>
                <div class="text-right">
                    <span class="block font-mono text-sm font-black text-slate-900 dark:text-white">{{ $accessTime }}</span>
                    <span class="block text-[9px] font-bold text-slate-400">{{ $accessDay }}{{ $accessDay ? ', ' : '' }}{{ $accessDate }}</span>
                </div>
            </div>

            <div class="h-px bg-rose-200/70 dark:bg-rose-500/10"></div>

            <div class="flex items-center justify-between gap-4">
                <span class="text-[9px] font-black uppercase tracking-[0.18em] text-slate-400">Jadwal Diizinkan</span>
                <span class="rounded-lg border border-white bg-white/80 px-3 py-1.5 text-right text-[11px] font-black text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200">{{ $schedule }}</span>
            </div>

            <div class="flex items-center justify-between gap-4">
                <span class="text-[9px] font-black uppercase tracking-[0.18em] text-slate-400">Zona Waktu</span>
                <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300">{{ $timezone }}</span>
            </div>

            <div class="h-px bg-rose-200/70 dark:bg-rose-500/10"></div>

            <div class="flex items-center justify-between gap-4">
                <span class="text-[9px] font-black uppercase tracking-[0.18em] text-slate-400">Risk Level</span>
                <div class="flex items-center gap-2.5">
                    <span class="rounded-lg border border-rose-200 bg-rose-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-rose-600 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400">Tinggi</span>
                    <div class="h-1.5 w-14 overflow-hidden rounded-full bg-rose-200 dark:bg-slate-700">
                        <div class="h-full rounded-full bg-rose-500" style="width: {{ $riskScore }}%"></div>
                    </div>
                    <span class="text-xs font-black text-slate-800 dark:text-white">{{ $riskScore }}%</span>
                </div>
            </div>
        </div>

        <div class="my-5 flex items-center justify-center gap-2 text-[10px] font-semibold text-slate-400 dark:text-slate-500">
            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span>Upaya login ini telah dicatat pada monitoring keamanan.</span>
        </div>

        <a href="{{ route('login') }}"
            class="group inline-flex w-full items-center justify-center rounded-2xl border border-slate-700 bg-slate-900 px-6 py-4 text-[10px] font-black uppercase tracking-[0.18em] text-white shadow-[0_14px_30px_rgba(15,23,42,0.22)] transition-all hover:-translate-y-0.5 hover:bg-slate-800 hover:shadow-[0_18px_36px_rgba(15,23,42,0.3)] active:scale-[0.98] dark:bg-slate-800 dark:hover:bg-slate-700">
            <svg class="mr-3 h-4 w-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Kembali ke Halaman Login
        </a>
    </div>
</x-guest-layout>
