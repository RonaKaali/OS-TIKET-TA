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

        <!-- Zero Trust Security Status -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 p-8 relative overflow-hidden group transition-all">
                        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                            <i class="fa-solid fa-server text-8xl text-emerald-500"></i>
                        </div>
                        <h3 class="text-xs font-black text-slate-900 dark:text-white flex items-center mb-6 uppercase tracking-widest transition-colors">
                            <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                            Network Health
                        </h3>
                        <div class="space-y-6 relative z-10">
                            <div>
                                <div class="flex justify-between text-[10px] text-slate-500 mb-2 font-black tracking-widest">
                                    <span>CPU LOAD</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 animate-pulse">24%</span>
                                </div>
                                <div class="h-2 w-full bg-slate-100 dark:bg-slate-900 rounded-full overflow-hidden transition-colors">
                                    <div class="h-full bg-emerald-500 w-[24%] transition-all duration-1000 shadow-[0_0_10px_rgba(16,185,129,0.3)]"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-[10px] text-slate-500 mb-2 font-black tracking-widest">
                                    <span>MEMORY</span>
                                    <span class="text-blue-600 dark:text-blue-400">42%</span>
                                </div>
                                <div class="h-2 w-full bg-slate-100 dark:bg-slate-900 rounded-full overflow-hidden transition-colors">
                                    <div class="h-full bg-blue-500 w-[42%] transition-all duration-1000 shadow-[0_0_10px_rgba(59,130,246,0.3)]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 p-8 relative overflow-hidden group transition-all">
                        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                            <i class="fa-solid fa-shield-virus text-8xl text-purple-500"></i>
                        </div>
                        <h3 class="text-xs font-black text-slate-900 dark:text-white flex items-center mb-6 uppercase tracking-widest transition-colors">
                            <div class="w-8 h-8 bg-purple-500/10 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            </div>
                            Zero Trust Policy
                        </h3>
                        <div class="flex items-center space-x-8 relative z-10">
                            <div class="flex-1">
                                <div class="text-[10px] text-slate-500 font-black tracking-widest mb-3 transition-colors">MFA ENFORCEMENT</div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-lg text-slate-900 dark:text-white font-black transition-colors">ACTIVE</span>
                                    <div class="w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="text-[10px] text-slate-500 font-black tracking-widest mb-3 transition-colors">ANOMALY DETECT</div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-lg text-slate-900 dark:text-white font-black transition-colors">SCANNING</span>
                                    <div class="w-3 h-3 rounded-full bg-blue-500 animate-ping shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 p-8 relative overflow-hidden transition-all">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center tracking-tight transition-colors">
                            <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center mr-5 transition-colors border border-emerald-500/20">
                                <i class="fa-solid fa-satellite-dish text-emerald-600 dark:text-emerald-400 text-xl"></i>
                            </div>
                            Pusat Komando Keamanan
                        </h3>
                        <div class="flex space-x-2">
                            @for($i=0; $i<5; $i++)
                            <div class="w-2 h-5 rounded-full {{ $i < 4 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-slate-200 dark:bg-slate-700' }}"></div>
                            @endfor
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 transition-colors hover:border-emerald-500/30 group">
                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest mb-4 transition-colors">Identity Verification</div>
                            <div class="flex items-center justify-between">
                                <span class="text-base font-bold text-slate-700 dark:text-slate-200 transition-colors">MFA Active</span>
                                <span class="text-[10px] font-black px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg border border-emerald-500/20 uppercase transition-colors shadow-sm">PASSED</span>
                            </div>
                        </div>
                        <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 transition-colors hover:border-emerald-500/30 group">
                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest mb-4 transition-colors">Context Analysis</div>
                            <div class="flex items-center justify-between">
                                <span class="text-base font-bold text-slate-700 dark:text-slate-200 transition-colors">Geo-Fencing</span>
                                <span class="text-[10px] font-black px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg border border-emerald-500/20 uppercase transition-colors shadow-sm">ACTIVE</span>
                            </div>
                        </div>
                        <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 transition-colors hover:border-emerald-500/30 group">
                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest mb-4 transition-colors">Device Trust</div>
                            <div class="flex items-center justify-between">
                                <span class="text-base font-bold text-slate-700 dark:text-slate-200 transition-colors">Fingerprinting</span>
                                <span class="text-[10px] font-black px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg border border-emerald-500/20 uppercase transition-colors shadow-sm">SECURE</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Threat Intel (Sidebar) -->
            <div class="bg-white dark:bg-slate-800/50 backdrop-blur-md rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col h-full transition-all">
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
                    <span class="text-[9px] text-slate-400 dark:text-slate-500 font-black uppercase animate-pulse transition-colors border border-slate-300 dark:border-slate-600 px-2 py-1 rounded">Live Feed</span>
                </div>
                <div class="p-0 flex-1 overflow-y-auto" id="news-container" style="max-height: 560px;">
                    <!-- News Items will be populated by JS -->
                    <div class="flex items-center justify-center h-40">
                        <i class="fa-solid fa-spinner fa-spin text-3xl text-slate-300 dark:text-slate-600"></i>
                    </div>
                </div>
                <div class="p-4 border-t border-slate-100 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-900/50 transition-colors">
                    <button class="w-full py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-500/50 transition-all flex items-center justify-center space-x-3 uppercase tracking-widest shadow-sm">
                        <i class="fa-solid fa-rotate"></i>
                        <span>Refresh Intel Source</span>
                    </button>
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
