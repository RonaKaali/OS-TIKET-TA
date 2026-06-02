@extends('layouts.admin')

@section('title', 'Zero Trust Security Dashboard')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Zero Trust Security Dashboard') }}
    </h2>
@endsection

@section('content')
<div class="py-12" x-data="securityDashboard()" x-init="init()">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                <div class="text-sm text-gray-500 dark:text-gray-400 uppercase font-bold">Total Security Events</div>
                <div class="text-2xl font-bold dark:text-white">{{ $stats['total_events'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-cyan-500">
                <div class="text-sm text-gray-500 dark:text-gray-400 uppercase font-bold">Events Today</div>
                <div class="text-2xl font-bold dark:text-white">{{ $stats['today_events'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                <div class="text-sm text-gray-500 dark:text-gray-400 uppercase font-bold">Critical Anomalies</div>
                <div class="text-2xl font-bold dark:text-white">{{ $stats['high_risk_count'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                <div class="text-sm text-gray-500 dark:text-gray-400 uppercase font-bold">Avg Device Trust Score</div>
                <div class="text-2xl font-bold dark:text-white">{{ $stats['avg_trust_score'] }}%</div>
            </div>
        </div>

        <!-- Live Monitoring Section -->
        <div class="bg-gray-900 overflow-hidden shadow-xl sm:rounded-lg border border-gray-700">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-cyan-400 flex items-center">
                        <span class="relative flex h-3 w-3 mr-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-cyan-500"></span>
                        </span>
                        LIVE SECURITY MONITORING
                    </h3>
                    <div class="text-xs text-gray-500 uppercase tracking-widest bg-gray-800 px-3 py-1 rounded-full border border-gray-700">
                        Polling: <span x-text="countdown">60</span>s
                    </div>
                </div>

                <!-- Feed Container -->
                <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    <template x-for="event in events" :key="event.id">
                        <div 
                            class="relative p-4 rounded-lg border bg-gray-800 transition-all duration-500 hover:bg-gray-750"
                            :class="{
                                'border-red-900 bg-red-950/20': event.severity === 'critical' || event.severity === 'high',
                                'border-yellow-900 bg-yellow-950/20': event.severity === 'medium',
                                'border-gray-700': event.severity === 'low'
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
                                    <template x-if="event.event_type.includes('anomaly') || event.event_type.includes('failed')">
                                        <i class="fas fa-exclamation-triangle text-xl text-red-500"></i>
                                    </template>
                                    <template x-if="event.event_type.includes('device')">
                                        <i class="fas fa-laptop text-xl text-cyan-400"></i>
                                    </template>
                                    <template x-if="!event.event_type.includes('auth') && !event.event_type.includes('anomaly') && !event.event_type.includes('device')">
                                        <i class="fas fa-fingerprint text-xl text-purple-400"></i>
                                    </template>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-bold text-gray-200" x-text="event.user_name"></span>
                                        <span class="text-xs text-gray-500" x-text="event.time_diff"></span>
                                    </div>
                                    <div class="text-sm text-gray-400 mb-2" x-text="event.message"></div>
                                    
                                    <!-- Badges -->
                                    <div class="flex flex-wrap gap-2">
                                        <span class="px-2 py-0.5 text-[10px] rounded bg-gray-900 border border-gray-700 text-gray-300 font-mono" x-text="event.ip_address"></span>
                                        <span class="px-2 py-0.5 text-[10px] rounded bg-gray-900 border border-gray-700 text-gray-300 font-mono" x-text="event.event_type"></span>
                                        
                                        <template x-if="event.metadata && event.metadata.browser">
                                            <span class="px-2 py-0.5 text-[10px] rounded bg-blue-900/30 border border-blue-800 text-blue-300">
                                                <i class="fab fa-chrome mr-1"></i> <span x-text="event.metadata.browser"></span>
                                            </span>
                                        </template>

                                            <div class="flex items-center gap-2 ml-auto">
                                                <span class="text-[10px] text-gray-500 uppercase">Risk Score</span>
                                                <div class="w-20 h-1.5 bg-gray-700 rounded-full overflow-hidden">
                                                    <div 
                                                        class="h-full transition-all duration-1000" 
                                                        :class="event.risk_score > 70 ? 'bg-red-500' : (event.risk_score > 30 ? 'bg-yellow-500' : 'bg-green-500')"
                                                        :style="`width: ${event.risk_score}%`"
                                                    ></div>
                                                </div>
                                                <span class="text-[10px] font-bold" :class="event.risk_score > 70 ? 'text-red-500' : 'text-gray-400'" x-text="event.risk_score"></span>
                                            </div>
                                        </template>

                                        <template x-if="event.user_id">
                                            <button @click="revokeAccess(event.user_id)" class="ml-2 px-2 py-0.5 text-[10px] font-bold rounded bg-red-900/50 border border-red-700 text-red-300 hover:bg-red-700 hover:text-white transition-colors cursor-pointer">
                                                <i class="fas fa-ban mr-1"></i> Cabut Akses
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Loading State -->
                    <template x-if="loading && events.length === 0">
                        <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <i class="fas fa-circle-notch animate-spin text-3xl mb-4"></i>
                            <p>Initializing secure connection...</p>
                        </div>
                    </template>
                </div>
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
                    if (this.events.length === 0 || data[0].id !== this.events[0].id) {
                        this.events = data;
                    }
                } catch (error) {
                    console.error('Failed to fetch security events:', error);
                } finally {
                    this.loading = false;
                }
            },

            async revokeAccess(userId) {
                if (!confirm('Apakah Anda yakin ingin mematikan semua sesi pengguna ini (Force Logout)?')) return;

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
                        alert('Berhasil! Akses pengguna telah dicabut.');
                        this.fetchEvents();
                    } else {
                        alert('Gagal mencabut akses.');
                    }
                } catch (error) {
                    console.error('Revoke access error:', error);
                    alert('Terjadi kesalahan.');
                }
            }
        }
    }
</script>
@endsection
