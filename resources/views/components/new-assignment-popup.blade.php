@php
    $isFieldAgent = \App\Support\RoleUi::isFieldAgent(auth()->user());
@endphp

@if($isFieldAgent)
<div id="assignment-notification-root" class="fixed inset-0 z-50">
    <!-- Modal Backdrop -->
    <div id="assignment-modal-backdrop" class="hidden fixed inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm transition-opacity duration-300"></div>

    <!-- Modal Container -->
    <div id="assignment-modal" class="hidden fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl mx-4 z-50 transition-all duration-300 scale-95 opacity-0">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
            <!-- Header -->
            <div class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-8 h-8 text-orange-500 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <div>
                            <h2 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-widest">Surat Tugas Baru</h2>
                            <p class="text-xs text-slate-600 dark:text-slate-400 font-bold">Anda memiliki penugasan tiket baru</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div id="assignment-modal-content" class="px-6 py-6 max-h-[60vh] overflow-y-auto">
                <div id="assignment-modal-list" class="space-y-4"></div>
                <div id="assignment-modal-empty" class="text-center py-12 hidden">
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-400">Tidak ada surat tugas baru</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950/50 flex gap-3">
                <button type="button" id="assignment-modal-confirm"
                    class="flex-1 px-4 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-xl uppercase tracking-widest transition-all hidden">
                    Saya Siap Mengerjakan
                </button>
                <button type="button" id="assignment-modal-close"
                    class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-black rounded-xl uppercase tracking-widest transition-all hidden">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('assignment-modal');
    const backdrop = document.getElementById('assignment-modal-backdrop');
    const listEl = document.getElementById('assignment-modal-list');
    const emptyEl = document.getElementById('assignment-modal-empty');
    const confirmBtn = document.getElementById('assignment-modal-confirm');
    const closeBtn = document.getElementById('assignment-modal-close');
    let pendingItems = [];

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function showModal() {
        modal.classList.remove('hidden');
        backdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideModal() {
        modal.classList.add('hidden');
        backdrop.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function renderList() {
        if (pendingItems.length === 0) {
            listEl.innerHTML = '';
            emptyEl.classList.remove('hidden');
            confirmBtn.classList.add('hidden');
            closeBtn.classList.remove('hidden');
            return;
        }
        
        emptyEl.classList.add('hidden');
        confirmBtn.classList.remove('hidden');
        closeBtn.classList.remove('hidden');
        
        listEl.innerHTML = pendingItems.map((item) => `
            <div class="border-2 border-slate-200 dark:border-slate-700 rounded-xl p-4 hover:border-amber-500 transition-colors bg-slate-50 dark:bg-slate-800/50">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs font-black rounded-full uppercase">
                                ${item.ticket_number}
                            </span>
                            ${item.priority ? `<span class="text-xs font-bold text-slate-600">${item.priority}</span>` : ''}
                        </div>
                        <h3 class="font-black text-slate-900 dark:text-white text-sm mb-2">${item.subject}</h3>
                    </div>
                    <a href="${item.url}" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-lg transition-colors whitespace-nowrap">
                        Lihat
                    </a>
                </div>
            </div>
        `).join('');
    }

    async function poll() {
        try {
            const res = await fetch('{{ route("agent.assignments.pending") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) return;
            const data = await res.json();
            const newItems = data.assignments || [];
            
            if (newItems.length > 0 && modal.classList.contains('hidden')) {
                pendingItems = newItems;
                renderList();
                showModal();
            } else {
                pendingItems = newItems;
                if (!modal.classList.contains('hidden')) {
                    renderList();
                }
            }
        } catch (e) {
            console.error('Error polling:', e);
        }
    }

    async function acknowledge(ticketIds) {
        if (!ticketIds.length) return;
        try {
            await fetch('{{ route("agent.assignments.acknowledge") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ ticket_ids: ticketIds }),
            });
            pendingItems = [];
            renderList();
            hideModal();
        } catch (e) {
            console.error('Error acknowledging:', e);
        }
    }

    confirmBtn?.addEventListener('click', () => {
        const ids = pendingItems.map(i => i.id);
        acknowledge(ids);
    });

    closeBtn?.addEventListener('click', hideModal);
    backdrop?.addEventListener('click', hideModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            hideModal();
        }
    });

    poll();
    setInterval(poll, 20000);
})();
</script>
@endif
