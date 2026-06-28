<x-admin-layout>
    <div class="space-y-6" x-data="securityDashboard()" x-init="init()">

        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-white flex items-center tracking-tight transition-colors">
                    <div class="w-10 h-10 bg-red-500/10 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    Ruang Zero Trust
                </h2>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 mt-1 transition-colors">Pusat Kendali Keamanan & Pemantauan Ancaman</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('agent.dashboard') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-black rounded-lg border border-slate-600 flex items-center tracking-widest uppercase transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali ke Pusat Komando
                </a>
            </div>
        </div>

        <!-- Export Logs -->
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-slate-700 p-6 transition-colors">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Unduh Log Keamanan
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Export event Zero Trust ke file CSV (Excel). Event polling sistem dikecualikan.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.security.api.export', ['period' => 'day']) }}"
                       class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/20">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Hari Ini
                    </a>
                    <a href="{{ route('admin.security.api.export', ['period' => 'week']) }}"
                       class="inline-flex items-center px-4 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-lg shadow-cyan-500/20">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        7 Hari
                    </a>
                    <a href="{{ route('admin.security.api.export', ['period' => 'month']) }}"
                       class="inline-flex items-center px-4 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-lg shadow-violet-500/20">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Bulan Ini
                    </a>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-4 text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">
                <span><span class="text-emerald-600 dark:text-emerald-400">Hari ini</span> — sejak 00:00 hari ini</span>
                <span><span class="text-cyan-600 dark:text-cyan-400">7 hari</span> — 7 hari terakhir termasuk hari ini</span>
                <span><span class="text-violet-600 dark:text-violet-400">Bulan ini</span> — sejak tanggal 1 bulan berjalan</span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-slate-700 p-6 border-l-4 border-l-blue-500 transition-colors">
                <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest mb-1 transition-colors">Total Security Events</div>
                <div class="text-3xl font-black text-slate-900 dark:text-white transition-colors">{{ $stats['total_events'] }}</div>
            </div>
            <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-slate-700 p-6 border-l-4 border-l-cyan-500 transition-colors">
                <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest mb-1 transition-colors">Events Hari Ini</div>
                <div class="text-3xl font-black text-slate-900 dark:text-white transition-colors">{{ $stats['today_events'] }}</div>
            </div>
            <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-slate-700 p-6 border-l-4 border-l-red-500 transition-colors">
                <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest mb-1 transition-colors">Anomali Kritis</div>
                <div class="text-3xl font-black text-red-600 dark:text-red-400 transition-colors">{{ $stats['high_risk_count'] }}</div>
            </div>
            <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-slate-700 p-6 border-l-4 border-l-purple-500 transition-colors">
                <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest mb-1 transition-colors">Avg Device Trust Score</div>
                <div class="text-3xl font-black text-slate-900 dark:text-white transition-colors">{{ $stats['avg_trust_score'] }}%</div>
            </div>
        </div>

        <!-- Live Monitoring Section -->
        <div class="bg-white dark:bg-slate-900 overflow-hidden shadow-xl dark:shadow-[0_0_25px_rgba(0,0,0,0.5)] rounded-2xl border border-slate-200 dark:border-slate-700 transition-colors">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-cyan-600 dark:text-cyan-400 flex items-center">
                        <span class="relative flex h-3 w-3 mr-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-cyan-500"></span>
                        </span>
                        LIVE SECURITY MONITORING
                    </h3>
                    <div class="text-xs text-slate-500 dark:text-gray-500 uppercase tracking-widest bg-slate-100 dark:bg-gray-800 px-3 py-1 rounded-full border border-slate-200 dark:border-gray-700">
                        Polling: <span x-text="countdown">60</span>s
                    </div>
                </div>

                <!-- Feed Container -->
                <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    <template x-for="event in events" :key="event.id">
                        <div 
                            class="relative p-4 rounded-lg border bg-slate-50 dark:bg-gray-800 transition-all duration-500 hover:bg-slate-100 dark:hover:bg-gray-750"
                            :class="{
                                'border-red-500 dark:border-red-900 bg-red-50 dark:bg-red-950/20': event.severity === 'critical' || event.severity === 'high',
                                'border-yellow-500 dark:border-yellow-900 bg-yellow-50 dark:bg-yellow-950/20': event.severity === 'medium',
                                'border-slate-200 dark:border-gray-700': event.severity === 'low'
                            }"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 transform -translate-y-4"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                        >
                            <div class="flex items-start gap-4">
                                <!-- Icon Based on Type -->
                                <div class="flex-shrink-0 mt-1">
                                    <template x-if="event.event_type.includes('auth')">
                                        <i class="fas fa-shield-alt text-xl" :class="event.severity === 'low' ? 'text-blue-400' : 'text-yellow-400'"></i>
                                    </template>
                                    <template x-if="event.event_type.includes('vpn')">
                                        <i class="fas fa-shield-halved text-xl text-red-500"></i>
                                    </template>
                                    <template x-if="event.event_type.includes('brute_force')">
                                        <i class="fas fa-gavel text-xl text-orange-500"></i>
                                    </template>
                                    <template x-if="event.event_type.includes('anomaly') || event.event_type.includes('failed')">
                                        <i class="fas fa-exclamation-triangle text-xl text-red-500"></i>
                                    </template>
                                    <template x-if="event.event_type.includes('device')">
                                        <i class="fas fa-laptop text-xl text-cyan-400"></i>
                                    </template>
                                    <template x-if="!event.event_type.includes('auth') && !event.event_type.includes('vpn') && !event.event_type.includes('brute_force') && !event.event_type.includes('anomaly') && !event.event_type.includes('device')">
                                        <i class="fas fa-fingerprint text-xl text-purple-400"></i>
                                    </template>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between mb-1 gap-2">
                                        <div class="min-w-0">
                                            <span class="text-sm font-bold text-slate-800 dark:text-gray-200" x-text="event.user_name"></span>
                                            <template x-if="event.user_email">
                                                <span class="text-[10px] text-gray-500 ml-2" x-text="`(${event.user_email})`"></span>
                                            </template>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="px-2 py-0.5 text-[10px] rounded bg-slate-200 dark:bg-gray-900 border border-slate-300 dark:border-gray-700 text-cyan-700 dark:text-cyan-300 font-black uppercase tracking-wider" x-text="event.severity_label"></span>
                                            <span class="text-xs text-gray-500" x-text="event.time_diff"></span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-slate-600 dark:text-gray-400 mb-2" x-text="event.message"></div>
                                    
                                    <!-- Badges -->
                                    <div class="flex flex-wrap gap-2 items-center mb-3">
                                        <span class="px-2 py-0.5 text-[10px] rounded bg-slate-100 dark:bg-gray-900 border border-slate-300 dark:border-gray-700 text-slate-700 dark:text-gray-300 font-mono" x-text="event.ip_address"></span>
                                        <span class="px-2 py-0.5 text-[10px] rounded bg-slate-100 dark:bg-gray-900 border border-slate-300 dark:border-gray-700 text-slate-700 dark:text-gray-300 font-mono" x-text="event.event_type"></span>
                                        <template x-if="event.method && event.path">
                                            <span class="px-2 py-0.5 text-[10px] rounded bg-purple-900/30 border border-purple-800 text-purple-300 font-mono">
                                                <span x-text="event.method"></span> <span x-text="event.path"></span>
                                            </span>
                                        </template>
                                        <template x-if="event.metadata && event.metadata.browser">
                                            <span class="px-2 py-0.5 text-[10px] rounded bg-blue-900/30 border border-blue-800 text-blue-300">
                                                <i class="fab fa-chrome mr-1"></i> <span x-text="event.metadata.browser"></span>
                                            </span>
                                        </template>
                                        <template x-if="event.user_id && event.user_id !== {{ auth()->id() }}">
                                            <div class="ml-auto flex gap-2">
                                                <template x-if="!event.user_is_revoked">
                                                    <button @click="revokeAccess(event.user_id)" class="px-2 py-0.5 text-[10px] font-bold rounded bg-red-100 dark:bg-red-900/50 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300 hover:bg-red-500 hover:text-white dark:hover:bg-red-700 transition-colors cursor-pointer" title="Logout paksa user di semua perangkat">
                                                        <i class="fas fa-ban mr-1"></i> Cabut Akses
                                                    </button>
                                                </template>
                                                <template x-if="event.user_is_revoked">
                                                    <button @click="restoreAccess(event.user_id)" class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-100 dark:bg-emerald-900/50 border border-emerald-300 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-500 hover:text-white dark:hover:bg-emerald-700 transition-colors cursor-pointer" title="Pulihkan akses user untuk login kembali">
                                                        <i class="fas fa-unlock mr-1"></i> Pulihkan Akses
                                                    </button>
                                                </template>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Zero Trust Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-[10px]">
                                        <div class="rounded-lg bg-slate-100 dark:bg-gray-900/80 border border-slate-300 dark:border-gray-700 px-3 py-2">
                                            <div class="text-slate-500 dark:text-gray-500 uppercase tracking-widest font-black mb-1">Risk Score</div>
                                            <template x-if="event.risk_score !== null">
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1 h-1.5 bg-slate-300 dark:bg-gray-700 rounded-full overflow-hidden">
                                                        <div
                                                            class="h-full transition-all duration-1000"
                                                            :class="event.risk_score > 70 ? 'bg-red-500' : (event.risk_score > 30 ? 'bg-yellow-500' : 'bg-green-500')"
                                                            :style="`width: ${event.risk_score}%`"
                                                        ></div>
                                                    </div>
                                                    <span class="font-bold text-slate-800 dark:text-gray-200" x-text="event.risk_score"></span>
                                                </div>
                                            </template>
                                            <template x-if="event.risk_score === null">
                                                <span class="text-slate-400 dark:text-gray-500">Tidak tersedia</span>
                                            </template>
                                        </div>

                                        <div class="rounded-lg bg-slate-100 dark:bg-gray-900/80 border border-slate-300 dark:border-gray-700 px-3 py-2">
                                            <div class="text-gray-500 uppercase tracking-widest font-black mb-1">GPS</div>
                                            <template x-if="event.gps_label">
                                                <div class="font-mono text-emerald-600 dark:text-emerald-300 break-all" x-text="event.gps_label"></div>
                                            </template>
                                            <template x-if="!event.gps_label">
                                                <span class="text-slate-400 dark:text-gray-500">Tidak tersedia</span>
                                            </template>
                                        </div>

                                        <div class="rounded-lg bg-slate-100 dark:bg-gray-900/80 border border-slate-300 dark:border-gray-700 px-3 py-2">
                                            <div class="text-gray-500 uppercase tracking-widest font-black mb-1">Device</div>
                                            <template x-if="event.device_fingerprint">
                                                <div class="font-mono text-cyan-600 dark:text-cyan-300 break-all" :title="event.device_fingerprint" x-text="event.device_fingerprint_short"></div>
                                                <template x-if="event.device_trust_score !== null">
                                                    <div class="text-slate-500 dark:text-gray-400 mt-1">Trust: <span class="text-slate-800 dark:text-white font-bold" x-text="`${event.device_trust_score}%`"></span></div>
                                                </template>
                                            </template>
                                            <template x-if="!event.device_fingerprint">
                                                <span class="text-slate-400 dark:text-gray-500">Tidak tersedia</span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Loading State -->
                    <template x-if="loading && events.length === 0">
                        <div class="flex flex-col items-center justify-center py-12 text-slate-500 dark:text-gray-500">
                            <i class="fas fa-circle-notch animate-spin text-3xl mb-4 text-slate-400"></i>
                            <p class="text-slate-500 dark:text-gray-500">Initializing secure connection...</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #4b5563; }
    .bg-gray-750 { background-color: #2a3341; }
</style>

<script>
    function securityDashboard() {
        return {
            events: [],
            loading: true,
            countdown: 60,
            interval: null,

            init() {
                this.fetchEvents();
                
                // Start Countdown & Polling
                this.interval = setInterval(() => {
                    this.countdown--;
                    if (this.countdown <= 0) {
                        this.fetchEvents();
                        this.countdown = 60;
                    }
                }, 1000);
            },

            async fetchEvents() {
                try {
                    const response = await fetch('{{ route('admin.security.api.latest') }}');
                    const data = await response.json();
                    
                    // Simple logic to only update if IDs are different or first load
                    if (this.events.length === 0 || (data.length > 0 && data[0].id !== this.events[0].id)) {
                        this.events = data;
                    }
                } catch (error) {
                    console.error('Failed to fetch security events:', error);
                } finally {
                    this.loading = false;
                }
            },

            async revokeAccess(userId) {
                if (!confirm('Cabut akses pengguna ini?\n\nUser akan LOGOUT OTOMATIS di semua perangkat (HP, laptop, dll.) pada aktivitas berikutnya.\n\nUser masih bisa login kembali setelah itu.')) return;

                try {
                    const response = await fetch(`/admin/api/security-events/revoke/${userId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const result = await response.json();
                    
                    if (result.status === 'success') {
                        alert(result.message || 'Berhasil! User akan logout di semua perangkat pada request berikutnya.');
                        this.fetchEvents();
                    } else {
                        alert(result.message || 'Gagal mencabut akses.');
                    }
                } catch (error) {
                    console.error('Revoke access error:', error);
                    alert('Terjadi kesalahan.');
                }
            },

            async restoreAccess(userId) {
                if (!confirm('Pulihkan akses pengguna ini?\n\nUser akan diizinkan login kembali tanpa perlu reset kredensial.')) return;

                try {
                    const response = await fetch(`/admin/api/security-events/restore/${userId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const result = await response.json();
                    
                    if (result.status === 'success') {
                        alert(result.message || 'Berhasil memulihkan akses user.');
                        this.fetchEvents();
                    } else {
                        alert(result.message || 'Gagal memulihkan akses.');
                    }
                } catch (error) {
                    console.error('Restore access error:', error);
                    alert('Terjadi kesalahan saat memulihkan akses.');
                }
            }
        }
    }
</script>
</x-admin-layout>
