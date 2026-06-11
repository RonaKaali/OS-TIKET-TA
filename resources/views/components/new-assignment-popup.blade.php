@if(\App\Support\RoleUi::isFieldAgent(auth()->user()))
<div id="assignment-notification-root" class="fixed top-4 right-4 z-50">
    <!-- Bell Icon with Badge -->
    <button id="assignment-bell-btn" type="button" class="relative p-2 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-lg hover:shadow-xl transition-all hover:scale-110"
        aria-label="Notifikasi surat tugas">
        <svg class="w-6 h-6 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span id="assignment-badge" class="absolute top-0 right-0 w-5 h-5 bg-red-500 text-white text-xs font-black rounded-full flex items-center justify-center hidden">0</span>
    </button>

    <!-- Dropdown Panel -->
    <div id="assignment-dropdown" class="absolute top-full right-0 mt-2 w-80 max-h-96 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl overflow-hidden hidden">
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-amber-500/10 to-orange-500/10">
            <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Surat Tugas Baru</h3>
        </div>
        <div id="assignment-list" class="divide-y divide-slate-100 dark:divide-slate-700 max-h-64 overflow-y-auto"></div>
        <div id="assignment-empty" class="p-6 text-center text-slate-500 dark:text-slate-400 text-sm font-bold">
            Tidak ada surat tugas baru
        </div>
        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700 flex gap-2">
            <button type="button" id="assignment-confirm-all"
                class="flex-1 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-lg uppercase tracking-widest transition-colors hidden">
                Konfirmasi Semua
            </button>
            <button type="button" id="assignment-close"
                class="flex-1 px-3 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-black rounded-lg uppercase tracking-widest transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    const bellBtn = document.getElementById('assignment-bell-btn');
    const dropdown = document.getElementById('assignment-dropdown');
    const badge = document.getElementById('assignment-badge');
    const listEl = document.getElementById('assignment-list');
    const emptyEl = document.getElementById('assignment-empty');
    const confirmAllBtn = document.getElementById('assignment-confirm-all');
    const closeBtn = document.getElementById('assignment-close');
    let pendingItems = [];
    let isOpen = false;

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function updateBadge() {
        if (pendingItems.length > 0) {
            badge.textContent = pendingItems.length;
            badge.classList.remove('hidden');
            confirmAllBtn.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
            confirmAllBtn.classList.add('hidden');
        }
    }

    function renderList() {
        if (pendingItems.length === 0) {
            listEl.innerHTML = '';
            emptyEl.classList.remove('hidden');
            return;
        }
        
        emptyEl.classList.add('hidden');
        listEl.innerHTML = pendingItems.map((item, idx) => `
            <div class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1">
                        <div class="text-xs font-black text-emerald-600 dark:text-emerald-400">${item.ticket_number}</div>
                        <div class="font-bold text-slate-900 dark:text-white text-sm mt-0.5">${item.subject}</div>
                        <div class="text-xs text-slate-500 mt-1">${item.priority || '—'} · ${item.status || '—'}</div>
                    </div>
                    <a href="${item.url}" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black rounded transition-colors whitespace-nowrap">
                        Lihat
                    </a>
                </div>
            </div>
        `).join('');
    }

    function toggleDropdown() {
        isOpen = !isOpen;
        if (isOpen) {
            dropdown.classList.remove('hidden');
        } else {
            dropdown.classList.add('hidden');
        }
    }

    async function poll() {
        try {
            const res = await fetch('{{ route('agent.assignments.pending') }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) return;
            const data = await res.json();
            pendingItems = data.assignments || [];
            updateBadge();
            if (isOpen) renderList();
        } catch (e) { /* ignore */ }
    }

    async function acknowledge(ticketIds) {
        if (!ticketIds.length) return;
        await fetch('{{ route('agent.assignments.acknowledge') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ ticket_ids: ticketIds }),
        });
        pendingItems = [];
        updateBadge();
        renderList();
    }

    bellBtn?.addEventListener('click', () => {
        toggleDropdown();
        if (isOpen) renderList();
    });

    closeBtn?.addEventListener('click', () => toggleDropdown());

    confirmAllBtn?.addEventListener('click', () => {
        const ids = pendingItems.map(i => i.id);
        acknowledge(ids);
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#assignment-notification-root')) {
            if (isOpen) toggleDropdown();
        }
    });

    poll();
    setInterval(poll, 20000);
})();
</script>
@endif
