<x-agent-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 flex items-center transition-colors">
                    <svg class="w-7 h-7 mr-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    Pusat Komando CSIRT
                </h2>
                <p class="text-sm font-bold text-slate-500 dark:text-slate-400 mt-1 transition-colors">Sistem Pemantauan Insiden Siber Terpadu</p>
            </div>
            <div class="flex items-center space-x-3">
                @hasrole('Super Admin')
                <a href="{{ route('admin.security.dashboard') }}" id="tour-sa-zerotrust" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-xs font-black rounded-lg shadow-[0_0_15px_rgba(16,185,129,0.5)] border border-emerald-400/50 flex items-center tracking-widest uppercase transition-all hover:scale-105 mr-4">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    Ruang Zero Trust
                </a>
                @endhasrole
                <span class="flex h-3 w-3 relative">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-emerald-600 dark:text-emerald-400 text-xs font-black tracking-widest transition-colors uppercase">Sistem Aman</span>
            </div>
        </div>
    </x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8" id="tour-sa-stats">
        <!-- Open Tickets -->
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] hover:shadow-md dark:hover:shadow-[0_0_20px_rgba(59,130,246,0.3)] transition-all transform hover:-translate-y-1 border border-slate-200 dark:border-slate-700 overflow-hidden relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 dark:from-blue-600/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-widest transition-colors">Laporan Aktif</p>
                        <p class="text-3xl font-black text-slate-900 dark:text-white transition-colors">{{ $stats['open'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-500/20 border border-blue-200 dark:border-blue-500/50 rounded-xl flex items-center justify-center shadow-inner transition-all">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                    <a href="{{ route('agent.tickets.index', ['status' => 'open']) }}"
                        class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-bold flex items-center uppercase tracking-widest transition-colors">
                        Investigasi Sekarang
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Answered Tickets -->
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] hover:shadow-md dark:hover:shadow-[0_0_20px_rgba(234,179,8,0.3)] transition-all transform hover:-translate-y-1 border border-slate-200 dark:border-slate-700 overflow-hidden relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/5 dark:from-yellow-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-widest transition-colors">Menunggu Info</p>
                        <p class="text-3xl font-black text-slate-900 dark:text-white transition-colors">{{ $stats['answered'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-50 dark:bg-yellow-500/20 border border-yellow-200 dark:border-yellow-500/50 rounded-xl flex items-center justify-center shadow-inner transition-all">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                    <a href="{{ route('agent.tickets.index', ['status' => 'answered']) }}"
                        class="text-xs text-yellow-600 dark:text-yellow-400 hover:text-yellow-700 dark:hover:text-yellow-300 font-bold flex items-center uppercase tracking-widest transition-colors">
                        Pantau Status
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Overdue Tickets -->
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] hover:shadow-md dark:hover:shadow-[0_0_20px_rgba(239,68,68,0.3)] transition-all transform hover:-translate-y-1 border border-slate-200 dark:border-slate-700 overflow-hidden relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 dark:from-red-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-widest transition-colors">Status Kritis</p>
                        <p class="text-3xl font-black text-slate-900 dark:text-white transition-colors">{{ $stats['overdue'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-50 dark:bg-red-500/20 border border-red-200 dark:border-red-500/50 rounded-xl flex items-center justify-center shadow-inner transition-all">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                    <a href="{{ route('agent.tickets.index') }}"
                        class="text-xs text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-bold flex items-center uppercase tracking-widest transition-colors">
                        Tindakan Darurat
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Closed Tickets -->
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] hover:shadow-md dark:hover:shadow-[0_0_20px_rgba(16,185,129,0.3)] transition-all transform hover:-translate-y-1 border border-slate-200 dark:border-slate-700 overflow-hidden relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 dark:from-emerald-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="p-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-widest transition-colors">Telah Ditangani</p>
                        <p class="text-3xl font-black text-slate-900 dark:text-white transition-colors">{{ $stats['closed'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/50 rounded-xl flex items-center justify-center shadow-inner transition-all">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                    <a href="{{ route('agent.tickets.index', ['status' => 'closed']) }}"
                        class="text-xs text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-bold flex items-center uppercase tracking-widest transition-colors">
                        Arsip Laporan
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content Area: Charts and Data -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Analytics Chart Section -->
            <div id="tour-sa-chart" class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-slate-700 p-8 relative overflow-hidden group transition-colors">
                <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                    <svg class="w-40 h-40 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center tracking-tight transition-colors">
                        <div class="w-10 h-10 bg-indigo-500/10 rounded-xl flex items-center justify-center mr-4 transition-colors">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                        </div>
                        Analitik Insiden Siber <span class="ml-2 text-slate-500 text-sm font-bold tracking-normal transition-colors">(Trend Bulanan)</span>
                    </h3>
                    <div class="flex items-center space-x-3">
                        <span class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] transition-colors">Real-time Data</span>
                        <div class="px-3 py-1 bg-slate-100 dark:bg-slate-700/50 rounded-full border border-slate-200 dark:border-slate-600 text-[10px] text-emerald-600 dark:text-emerald-400 font-black uppercase tracking-widest transition-colors">
                            FY {{ date('Y') }}
                        </div>
                    </div>
                </div>
                <div class="relative h-80 w-full">
                    <canvas id="monthlyReportChart"></canvas>
                </div>
            </div>

            <!-- Zero Trust Security Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-slate-700 p-6 relative overflow-hidden group transition-colors">
                    <h3 class="text-xs font-black text-slate-900 dark:text-white flex items-center mb-6 uppercase tracking-widest transition-colors">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        Network Health
                    </h3>
                    <div class="space-y-5">
                        <div>
                            <div class="flex justify-between text-[10px] text-slate-500 mb-2 font-black tracking-widest">
                                <span>CPU LOAD</span>
                                <span class="text-emerald-600 dark:text-emerald-400 animate-pulse">24%</span>
                            </div>
                            <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-900 rounded-full overflow-hidden transition-colors">
                                <div class="h-full bg-emerald-500 w-[24%] transition-all duration-1000 shadow-[0_0_10px_rgba(16,185,129,0.3)]"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-[10px] text-slate-500 mb-2 font-black tracking-widest">
                                <span>MEMORY</span>
                                <span class="text-blue-600 dark:text-blue-400">42%</span>
                            </div>
                            <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-900 rounded-full overflow-hidden transition-colors">
                                <div class="h-full bg-blue-500 w-[42%] transition-all duration-1000 shadow-[0_0_10px_rgba(59,130,246,0.3)]"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-slate-700 p-6 relative overflow-hidden group transition-colors">
                    <h3 class="text-xs font-black text-slate-900 dark:text-white flex items-center mb-6 uppercase tracking-widest transition-colors">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        Zero Trust Policy
                    </h3>
                    <div class="flex items-center space-x-6">
                        <div class="flex-1">
                            <div class="text-[10px] text-slate-500 font-black tracking-widest mb-2 transition-colors">MFA ENFORCEMENT</div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-slate-900 dark:text-white font-black transition-colors">ACTIVE</span>
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="text-[10px] text-slate-500 font-black tracking-widest mb-2 transition-colors">ANOMALY DETECT</div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-slate-900 dark:text-white font-black transition-colors">SCANNING</span>
                                <div class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-ping shadow-[0_0_8px_rgba(59,130,246,0.5)]"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-slate-700 p-8 relative overflow-hidden transition-colors">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center tracking-tight transition-colors">
                        <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center mr-4 transition-colors">
                            <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        Pusat Komando Keamanan
                    </h3>
                    <div class="flex space-x-1.5">
                        @for($i=0; $i<5; $i++)
                        <div class="w-1.5 h-4 rounded-full {{ $i < 4 ? 'bg-emerald-500 shadow-[0_0_5px_rgba(16,185,129,0.5)]' : 'bg-slate-200 dark:bg-slate-700' }}"></div>
                        @endfor
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <!-- 1. MFA Active -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest transition-colors">Identity Verification</div>
                            <span class="text-[8px] font-black px-1.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded border border-emerald-500/20 uppercase transition-colors">PASSED</span>
                        </div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 transition-colors block">MFA Active</span>
                    </div>
                    <!-- 2. Geo-Fencing -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest transition-colors">Context Analysis</div>
                            <span class="text-[8px] font-black px-1.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded border border-emerald-500/20 uppercase transition-colors">ACTIVE</span>
                        </div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 transition-colors block">Geo-Fencing</span>
                    </div>
                    <!-- 3. Device Fingerprinting -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest transition-colors">Device Trust</div>
                            <span class="text-[8px] font-black px-1.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded border border-emerald-500/20 uppercase transition-colors">SECURE</span>
                        </div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 transition-colors block">Fingerprinting</span>
                    </div>
                    <!-- 4. Brute Force -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest transition-colors">Login Protection</div>
                            <span class="text-[8px] font-black px-1.5 py-0.5 bg-orange-500/10 text-orange-600 dark:text-orange-400 rounded border border-orange-500/20 uppercase transition-colors">5X LIMIT</span>
                        </div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 transition-colors block">Brute Force Guard</span>
                    </div>
                    <!-- 5. Session Verify -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest transition-colors">Session Guard</div>
                            <span class="text-[8px] font-black px-1.5 py-0.5 bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 rounded border border-cyan-500/20 uppercase transition-colors">3 MIN</span>
                        </div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 transition-colors block">Continuous Verify</span>
                    </div>
                    <!-- 6. VPN Block -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest transition-colors">VPN Detection</div>
                            <span class="text-[8px] font-black px-1.5 py-0.5 bg-red-500/10 text-red-600 dark:text-red-400 rounded border border-red-500/20 uppercase transition-colors">BLOCKED</span>
                        </div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 transition-colors block">Non-Indonesia</span>
                    </div>
                    <!-- 7. RBAC -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest transition-colors">Access Control</div>
                            <span class="text-[8px] font-black px-1.5 py-0.5 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded border border-indigo-500/20 uppercase transition-colors">ENFORCED</span>
                        </div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 transition-colors block">RBAC + Least Priv.</span>
                    </div>
                    <!-- 8. Access Revocation -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest transition-colors">Emergency</div>
                            <span class="text-[8px] font-black px-1.5 py-0.5 bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded border border-rose-500/20 uppercase transition-colors">READY</span>
                        </div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 transition-colors block">Access Revocation</span>
                    </div>
                    <!-- 9. AES-256 -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest transition-colors">Data Protection</div>
                            <span class="text-[8px] font-black px-1.5 py-0.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded border border-amber-500/20 uppercase transition-colors">AES-256</span>
                        </div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 transition-colors block">File Encryption</span>
                    </div>
                    <!-- 10. Working Hours -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 hover:border-emerald-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest transition-colors">Time Policy</div>
                            <span class="text-[8px] font-black px-1.5 py-0.5 bg-teal-500/10 text-teal-600 dark:text-teal-400 rounded border border-teal-500/20 uppercase transition-colors">08-17</span>
                        </div>
                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200 transition-colors block">Working Hours</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Area: Live News -->
        <div class="space-y-8">
            <div id="tour-sa-news" class="bg-white dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-sm dark:shadow-[0_0_15px_rgba(0,0,0,0.5)] border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col h-full transition-colors">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900/50 flex justify-between items-center transition-colors">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center tracking-widest transition-colors uppercase">
                        <div class="w-8 h-8 bg-red-500/10 rounded-lg flex items-center justify-center mr-4 transition-colors">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                            </span>
                        </div>
                        Global Threat Intel
                    </h3>
                    <span class="text-[9px] text-slate-400 dark:text-slate-500 font-black uppercase animate-pulse transition-colors">Live Feed</span>
                </div>
                <div class="p-0 flex-1 overflow-y-auto" id="news-container" style="max-height: 560px;">
                    <!-- News Items will be populated by JS -->
                    <div class="flex items-center justify-center h-40">
                        <svg class="w-8 h-8 text-slate-300 dark:text-slate-600 animate-spin transition-colors" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>
                <div class="p-4 border-t border-slate-100 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900/50 transition-colors">
                    <button class="w-full py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/50 transition-all flex items-center justify-center space-x-3 uppercase tracking-widest shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        <span>Refresh Intel Source</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart.js Configuration
            const ctx = document.getElementById('monthlyReportChart').getContext('2d');
            
            // Gradient for chart
            const gradientBlue = ctx.createLinearGradient(0, 0, 0, 400);
            gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.5)');
            gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
            
            const gradientRed = ctx.createLinearGradient(0, 0, 0, 400);
            gradientRed.addColorStop(0, 'rgba(239, 68, 68, 0.5)');
            gradientRed.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

            const myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [
                        {
                            label: 'Laporan Selesai',
                            data: [12, 19, 15, 25, 22, 30, 28, 35, 40, 38, 45, 50],
                            borderColor: '#3b82f6',
                            backgroundColor: gradientBlue,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#3b82f6',
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Insiden Kritis',
                            data: [3, 5, 2, 8, 4, 7, 5, 10, 6, 8, 5, 7],
                            borderColor: '#ef4444',
                            backgroundColor: gradientRed,
                            borderWidth: 2,
                            borderDash: [5, 5],
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#ef4444'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: { color: '#94a3b8', font: { size: 10, weight: 'bold' } }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            padding: 12,
                            borderColor: 'rgba(16, 185, 129, 0.3)',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: document.documentElement.classList.contains('dark') ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                            ticks: { color: document.documentElement.classList.contains('dark') ? '#64748b' : '#94a3b8', font: { size: 11, weight: 'bold' } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: document.documentElement.classList.contains('dark') ? '#64748b' : '#94a3b8', font: { size: 11, weight: 'bold' } }
                        }
                    }
                }
            });

            // Hyper-active simulation
            setInterval(() => {
                myChart.data.datasets.forEach(dataset => {
                    dataset.data = dataset.data.map(val => {
                        const change = Math.floor(Math.random() * 7) - 3;
                        return Math.max(2, val + change);
                    });
                });
                myChart.update('none');
            }, 3000);

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
                            <svg class="w-3.5 h-3.5 mr-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
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
        });
    </script>
</x-agent-layout>