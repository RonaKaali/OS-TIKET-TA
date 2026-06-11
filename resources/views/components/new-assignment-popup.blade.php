@if(\App\Support\RoleUi::isFieldAgent(auth()->user()))
<div id="assignment-popup-root" class="fixed inset-0 z-[100] hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" id="assignment-popup-backdrop"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-lg bg-white dark:bg-slate-900 border border-amber-500/30 rounded-2xl shadow-2xl overflow-hidden animate-fadeIn">
            <div class="px-6 py-5 bg-gradient-to-r from-amber-500/10 to-orange-500/10 border-b border-amber-500/20 flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/20 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-wide">Surat Tugas Baru</h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Admin menugaskan tiket kepada Anda. Konfirmasi untuk mulai mengerjakan.</p>
                </div>
            </div>
            <div id="assignment-popup-list" class="px-6 py-4 space-y-3 max-h-64 overflow-y-auto"></div>
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row gap-3">
                <button type="button" id="assignment-popup-confirm"
                    class="flex-1 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-xl uppercase tracking-widest transition-colors">
                    Saya Siap Mengerjakan
                </button>
                <button type="button" id="assignment-popup-later"
                    class="px-4 py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-black rounded-xl uppercase tracking-widest transition-colors">
                    Nanti
                </button>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    const root = document.getElementById('assignment-popup-root');
    if (!root) return;

    const listEl = document.getElementById('assignment-popup-list');
    const confirmBtn = document.getElementById('assignment-popup-confirm');
    const laterBtn = document.getElementById('assignment-popup-later');
    let pendingItems = [];

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function showPopup(items) {
        if (!items.length) {
            root.classList.add('hidden');
            return;
        }
        pendingItems = items;
        listEl.innerHTML = items.map(item => `
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                <div class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">${item.ticket_number}</div>
                <div class="font-bold text-slate-900 dark:text-white text-sm mt-1">${item.subject}</div>
                <div class="text-[10px] text-slate-500 mt-2 uppercase tracking-widest">${item.priority || '—'} · ${item.status || '—'} · ${item.assigned_at}</div>
            </div>
        `).join('');
        root.classList.remove('hidden');
    }

    async function poll() {
        try {
            const res = await fetch('{{ route('agent.assignments.pending') }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) return;
            const data = await res.json();
            if (data.count > 0) {
                showPopup(data.assignments);
            } else {
                root.classList.add('hidden');
            }
        } catch (e) { /* ignore */ }
    }

    async function acknowledge(redirectUrl) {
        const ids = pendingItems.map(i => i.id);
        if (!ids.length) return;
        await fetch('{{ route('agent.assignments.acknowledge') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ ticket_ids: ids }),
        });
        root.classList.add('hidden');
        if (redirectUrl) {
            window.location.href = redirectUrl;
        } else if (pendingItems.length === 1) {
            window.location.href = pendingItems[0].url;
        } else {
            window.location.href = '{{ route('agent.tickets.index') }}';
        }
    }

    confirmBtn?.addEventListener('click', () => acknowledge(null));
    laterBtn?.addEventListener('click', () => root.classList.add('hidden'));

    poll();
    setInterval(poll, 20000);
})();
</script>
@endif
