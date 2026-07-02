<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight transition-colors">
                    Dasbor <span class="text-blue-600 dark:text-blue-500 transition-colors">Laporan Akhir</span>
                </h2>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest transition-colors">Periode: {{ date('F Y') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <button id="exportExcelMain" class="px-6 py-3 bg-emerald-600 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-emerald-500 transition-all shadow-[0_0_15px_rgba(16,185,129,0.2)] flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Ekspor Excel
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Laporan -->
            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</span>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['total'] }}</div>
                <div class="text-xs font-bold text-slate-500 mt-1 uppercase">Keseluruhan Tiket</div>
            </div>

            <!-- Sedang Dijalankan -->
            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aktif</span>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['in_progress'] }}</div>
                <div class="text-xs font-bold text-yellow-600 dark:text-yellow-400 mt-1 uppercase">Sedang Dikerjakan</div>
            </div>

            <!-- Laporan Baru (Open) -->
            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Baru</span>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['open'] }}</div>
                <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-1 uppercase">Menunggu Respon</div>
            </div>

            <!-- Telah Dikerjakan -->
            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Selesai</span>
                </div>
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['resolved'] }}</div>
                <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mt-1 uppercase">Telah Ditangani</div>
            </div>
        </div>

        <!-- ====== PUSAT KOMANDO KEAMANAN ====== -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 rounded-3xl p-6 sm:p-8 border border-slate-700 dark:border-slate-700/50 shadow-2xl relative overflow-hidden">
            <!-- Background Glow -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 blur-[80px] rounded-full -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-500/5 blur-[80px] rounded-full -ml-24 -mb-24"></div>

            <div class="relative z-10">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-emerald-500/20 rounded-xl flex items-center justify-center border border-emerald-500/30">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-white uppercase tracking-widest">Pusat Komando Keamanan</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Zero Trust Security Status — All Systems Operational</p>
                        </div>
                    </div>
                    <div class="hidden sm:flex items-center px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                        <span class="text-[9px] text-emerald-400 font-black uppercase tracking-widest">ONLINE</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                    <!-- 1. MFA Active -->
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </div>
                            <span class="px-1.5 py-0.5 bg-emerald-500/20 text-emerald-400 text-[8px] font-black uppercase rounded tracking-widest">ACTIVE</span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Identity Verification</div>
                        <div class="text-xs font-black text-white">MFA Active</div>
                    </div>

                    <!-- 2. Geo-Fencing -->
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <span class="px-1.5 py-0.5 bg-blue-500/20 text-blue-400 text-[8px] font-black uppercase rounded tracking-widest">ACTIVE</span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Context Analysis</div>
                        <div class="text-xs font-black text-white">Geo-Fencing</div>
                    </div>

                    <!-- 3. Device Fingerprinting -->
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" /></svg>
                            </div>
                            <span class="px-1.5 py-0.5 bg-purple-500/20 text-purple-400 text-[8px] font-black uppercase rounded tracking-widest">SECURE</span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Device Trust</div>
                        <div class="text-xs font-black text-white">Fingerprinting</div>
                    </div>

                    <!-- 4. Brute Force Protection -->
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-8 h-8 bg-orange-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                            </div>
                            <span class="px-1.5 py-0.5 bg-orange-500/20 text-orange-400 text-[8px] font-black uppercase rounded tracking-widest">5X LIMIT</span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Login Protection</div>
                        <div class="text-xs font-black text-white">Brute Force Guard</div>
                    </div>

                    <!-- 5. Session Verification -->
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-8 h-8 bg-cyan-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            </div>
                            <span class="px-1.5 py-0.5 bg-cyan-500/20 text-cyan-400 text-[8px] font-black uppercase rounded tracking-widest">3 MIN</span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Session Guard</div>
                        <div class="text-xs font-black text-white">Continuous Verify</div>
                    </div>

                    <!-- 6. VPN Block -->
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-8 h-8 bg-red-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                            </div>
                            <span class="px-1.5 py-0.5 bg-red-500/20 text-red-400 text-[8px] font-black uppercase rounded tracking-widest">BLOCKED</span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">VPN Detection</div>
                        <div class="text-xs font-black text-white">Non-Indonesia</div>
                    </div>

                    <!-- 7. RBAC & Least Privilege -->
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-8 h-8 bg-indigo-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            </div>
                            <span class="px-1.5 py-0.5 bg-indigo-500/20 text-indigo-400 text-[8px] font-black uppercase rounded tracking-widest">ENFORCED</span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Access Control</div>
                        <div class="text-xs font-black text-white">RBAC + Least Priv.</div>
                    </div>

                    <!-- 8. Access Revocation -->
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-8 h-8 bg-rose-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                            </div>
                            <span class="px-1.5 py-0.5 bg-rose-500/20 text-rose-400 text-[8px] font-black uppercase rounded tracking-widest">READY</span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Emergency</div>
                        <div class="text-xs font-black text-white">Access Revocation</div>
                    </div>

                    <!-- 9. AES-256 Encryption -->
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </div>
                            <span class="px-1.5 py-0.5 bg-amber-500/20 text-amber-400 text-[8px] font-black uppercase rounded tracking-widest">AES-256</span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Data Protection</div>
                        <div class="text-xs font-black text-white">File Encryption</div>
                    </div>

                    <!-- 10. Working Hours -->
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-8 h-8 bg-teal-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <span class="px-1.5 py-0.5 bg-teal-500/20 text-teal-400 text-[8px] font-black uppercase rounded tracking-widest">08-17</span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Time Policy</div>
                        <div class="text-xs font-black text-white">Working Hours Block</div>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <p class="text-[10px] text-slate-500 font-bold">10 komponen aktif — Verifikasi berkelanjutan tanpa kepercayaan implisit</p>
                    <a href="{{ route('admin.security.dashboard') }}" class="text-[10px] font-black text-emerald-400 hover:text-emerald-300 uppercase tracking-widest transition-colors flex items-center">
                        Buka Dashboard
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Distribution by Department -->
            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm transition-all">
                <h3 class="text-lg font-black text-slate-800 dark:text-white mb-6 uppercase tracking-tight">Distribusi Departemen</h3>
                <div class="space-y-4">
                    @foreach($departments as $dept)
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-slate-600 dark:text-slate-400 uppercase">{{ $dept->name }}</span>
                                <span class="text-slate-900 dark:text-white">{{ $dept->tickets_count }}</span>
                            </div>
                            <div class="h-2 w-full bg-slate-100 dark:bg-slate-900 rounded-full overflow-hidden">
                                @php
                                    $percent = $stats['total'] > 0 ? ($dept->tickets_count / $stats['total']) * 100 : 0;
                                @endphp
                                <div class="h-full bg-blue-500" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Performance Trend (Placeholder Chart) -->
            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm transition-all">
                <h3 class="text-lg font-black text-slate-800 dark:text-white mb-6 uppercase tracking-tight">Tren Penanganan</h3>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="performanceTrendChart"></canvas>
                </div>
            </div>
        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('performanceTrendChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Laporan Selesai',
                        data: [12, 19, 3, 5, 2, 3],
                        backgroundColor: '#3b82f6',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { font: { size: 10 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });

            // Live Cyber Security News Feed Simulation
            const newsItems = [
                {
                    title: "Potensi Serangan Distributed Denial of Service (DDoS) Terdeteksi pada Infrastruktur Cloud Pemerintah",
                    source: "CSIRT Nasional (BSSN)",
                    time: "15 menit lalu",
                    alert: "High"
                },
                {
                    title: "Critical Vulnerability (CVE-2026-1024) pada Sistem Manajemen Data Sektoral - Diperlukan Patch Segera",
                    source: "Security Alert System",
                    time: "1 jam lalu",
                    alert: "Critical"
                },
                {
                    title: "Laporan Aktivitas Mencurigakan: Upaya Bruteforce Akses Database pada Node Server Wilayah Kalimantan",
                    source: "Intrusion Detection System",
                    time: "3 jam lalu",
                    alert: "High"
                },
                {
                    title: "Analisis Kampanye Phishing Terbaru Menargetkan Kredensial ASN melalui Portal Mandiri Palsu",
                    source: "Cyber Intelligence Unit",
                    time: "5 jam lalu",
                    alert: "Medium"
                },
                {
                    title: "Update Keamanan: Penguatan Enkripsi End-to-End pada Layanan Administrasi Digital Provinsi",
                    source: "Internal Security Audit",
                    time: "8 jam lalu",
                    alert: "Low"
                }
            ];

            const container = document.getElementById('news-container');
            
            if (container) {
                function renderNews() {
                    container.innerHTML = '';
                    newsItems.forEach((item, index) => {
                        const isDark = document.documentElement.classList.contains('dark');
                        let alertColor = item.alert === 'Critical' ? 'text-red-600 dark:text-red-400 bg-red-400/10 border-red-400/30' : 
                                        item.alert === 'High' ? 'text-orange-600 dark:text-orange-400 bg-orange-400/10 border-orange-400/30' : 
                                        'text-yellow-600 dark:text-yellow-400 bg-yellow-400/10 border-yellow-400/30';
                        
                        const delay = index * 100;
                        
                        const el = document.createElement('div');
                        el.className = `p-5 border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-all animate-fade-in cursor-default`;
                        el.style.animationDelay = `${delay}ms`;
                        el.innerHTML = `
                            <div class="flex justify-between items-start mb-3">
                                <span class="text-[9px] font-black px-2 py-0.5 rounded border uppercase tracking-widest ${alertColor}">${item.alert}</span>
                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">${item.time}</span>
                            </div>
                            <h4 class="text-sm font-black text-slate-800 dark:text-slate-200 mb-2 leading-snug hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">${item.title}</h4>
                            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 flex items-center uppercase tracking-widest transition-colors">
                                <i class="fa-solid fa-satellite-dish mr-2 opacity-50"></i>
                                ${item.source}
                            </p>
                        `;
                        container.appendChild(el);
                    });
                }

                // Simulate fetching
                setTimeout(renderNews, 1000);
                
                // Add some basic animations
                const style = document.createElement('style');
                style.innerHTML = `
                    @keyframes fadeIn {
                        from { opacity: 0; transform: translateY(10px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                    .animate-fade-in {
                        animation: fadeIn 0.5s ease forwards;
                        opacity: 0;
                    }
                `;
                document.head.appendChild(style);
            }
        });
    </script>
    <script>
        document.getElementById('exportExcelMain').addEventListener('click', function() {
            // Generate simple CSV from stats
            const data = [
                ['Kategori', 'Jumlah'],
                ['Total Tiket', '{{ $stats["total"] }}'],
                ['Sedang Dikerjakan', '{{ $stats["in_progress"] }}'],
                ['Menunggu Respon', '{{ $stats["open"] }}'],
                ['Telah Ditangani', '{{ $stats["resolved"] }}']
            ];
            
            const csvContent = data.map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'Laporan_Ringkasan_CSIRT_{{ date("Y-m-d") }}.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
</x-admin-layout>
