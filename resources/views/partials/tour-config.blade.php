{{-- 
    Tour Configuration for CSIRT Kalselprov
    Include this in layouts that need the guided tour.
    Usage: @include('partials.tour-config')
    
    Requires: shepherd.min.js and shepherd-csirt-theme.css to be loaded first.
    The JS variable `csirtTourRole` should be set before this partial.
    If not set, tour will not initialize.
--}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Only init if role is set
    if (typeof window.csirtTourRole === 'undefined' || !window.csirtTourRole) return;

    const role = window.csirtTourRole;
    const storageKey = 'csirt_tour_completed_' + role;

    // Tour trigger button handler
    function bindTriggerButtons() {
        document.querySelectorAll('.csirt-tour-trigger').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                localStorage.removeItem(storageKey);
                startTour();
            });
        });
    }

    // Define steps per role
    function getSteps(role) {
        const commonIntro = {
            title: '📖 Selamat Datang di CSIRT Kalselprov',
            text: 'Panduan singkat ini akan membantu Anda memahami cara menggunakan sistem pelaporan insiden siber.',
            attachTo: { element: 'body', on: 'center' },
            classes: 'csirt-step-intro',
            buttons: [
                { text: ' Lewati Semua', classes: 'csirt-skip', action: function() { this.cancel(); } },
                { text: ' Mulai →', classes: 'shepherd-button-primary', action: function() { this.next(); } }
            ],
            id: 'tour-welcome'
        };

        switch(role) {
            case 'visitor':
                return [
                    {
                        title: '📖 Panduan Portal CSIRT',
                        text: 'Selamat datang di Portal Pelaporan Insiden Siber Pemprov Kalsel. Mari kita kenali fitur-fitur utama.',
                        attachTo: { element: '#tour-brand', on: 'bottom' },
                        buttons: [
                            { text: ' Lewati', classes: 'csirt-skip', action: function() { this.cancel(); } },
                            { text: ' Mulai →', classes: 'shepherd-button-primary', action: function() { this.next(); } }
                        ],
                        id: 'tour-v-0'
                    },
                    {
                        title: '🚨 Laporkan Insiden Siber',
                        text: 'Klik tombol ini untuk <strong>melaporkan insiden siber</strong>. Anda akan diarahkan ke formulir pelaporan resmi CSIRT.',
                        attachTo: { element: '#tour-cta-report', on: 'bottom' },
                        buttons: [
                            { text: '← Kembali', action: function() { this.back(); } },
                            { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }
                        ],
                        id: 'tour-v-1'
                    },
                    {
                        title: '🔍 Lacak Laporan',
                        text: 'Sudah punya nomor tiket? Gunakan tombol ini untuk <strong>melacak status laporan</strong> Anda secara real-time.',
                        attachTo: { element: '#tour-cta-track', on: 'bottom' },
                        buttons: [
                            { text: '← Kembali', action: function() { this.back(); } },
                            { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }
                        ],
                        id: 'tour-v-2'
                    },
                    {
                        title: '📊 Statistik Insiden',
                        text: 'Pantau <strong>statistik insiden siber</strong> secara real-time di dashboard ini. Data diperbarui otomatis.',
                        attachTo: { element: '#tour-stats', on: 'top' },
                        buttons: [
                            { text: '← Kembali', action: function() { this.back(); } },
                            { text: ' Selesai ✓', classes: 'shepherd-button-primary', action: function() { this.complete(); } }
                        ],
                        id: 'tour-v-3'
                    }
                ];

            case 'portal':
                return [
                    {
                        title: '📖 Portal Pelaporan',
                        text: 'Anda sudah login. Berikut panduan cepat untuk melaporkan insiden siber.',
                        attachTo: { element: '#tour-brand', on: 'bottom' },
                        buttons: [
                            { text: ' Lewati', classes: 'csirt-skip', action: function() { this.cancel(); } },
                            { text: ' Mulai →', classes: 'shepherd-button-primary', action: function() { this.next(); } }
                        ],
                        id: 'tour-p-0'
                    },
                    {
                        title: '🚨 Buat Laporan Baru',
                        text: 'Klik tombol ini untuk <strong>membuat laporan insiden baru</strong>. Isi formulir dengan detail insiden yang Anda alami.',
                        attachTo: { element: '#tour-cta-report', on: 'bottom' },
                        buttons: [
                            { text: '← Kembali', action: function() { this.back(); } },
                            { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }
                        ],
                        id: 'tour-p-1'
                    },
                    {
                        title: '🔍 Lacak Status',
                        text: 'Setelah mengirim laporan, Anda akan mendapat nomor tiket. Gunakan tombol ini untuk <strong>melacak progres</strong> penanganan.',
                        attachTo: { element: '#tour-cta-track', on: 'bottom' },
                        buttons: [
                            { text: '← Kembali', action: function() { this.back(); } },
                            { text: ' Selesai ✓', classes: 'shepherd-button-primary', action: function() { this.complete(); } }
                        ],
                        id: 'tour-p-2'
                    }
                ];

            case 'agent':
                return [
                    {
                        title: '📖 Panel Agen CSIRT',
                        text: 'Selamat datang, Agen! Berikut panduan untuk mengelola tiket insiden siber yang ditugaskan.',
                        attachTo: { element: '#tour-agent-stats', on: 'bottom' },
                        buttons: [
                            { text: ' Lewati', classes: 'csirt-skip', action: function() { this.cancel(); } },
                            { text: ' Mulai →', classes: 'shepherd-button-primary', action: function() { this.next(); } }
                        ],
                        id: 'tour-a-0'
                    },
                    {
                        title: '📊 Statistik Tiket',
                        text: 'Dashboard ini menampilkan <strong>ringkasan tiket</strong>: ditugaskan, dalam proses, selesai, dan surat tugas baru yang perlu dikonfirmasi.',
                        attachTo: { element: '#tour-agent-stats', on: 'bottom' },
                        buttons: [
                            { text: '← Kembali', action: function() { this.back(); } },
                            { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }
                        ],
                        id: 'tour-a-1'
                    },
                    {
                        title: '📋 Tugas Aktif',
                        text: 'Di sini Anda melihat daftar <strong>tiket yang ditugaskan</strong> kepada Anda. Klik tiket untuk melihat detail dan mulai bekerja.',
                        attachTo: { element: '#tour-agent-tasks', on: 'top' },
                        buttons: [
                            { text: '← Kembali', action: function() { this.back(); } },
                            { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }
                        ],
                        id: 'tour-a-2'
                    },
                    {
                        title: '✅ Selesaikan Tiket',
                        text: 'Setelah menangani insiden, klik tombol ini untuk <strong>menandai tiket selesai</strong>. Pelapor akan mendapat notifikasi.',
                        attachTo: { element: '#tour-agent-tasks', on: 'top' },
                        buttons: [
                            { text: '← Kembali', action: function() { this.back(); } },
                            { text: ' Selesai ✓', classes: 'shepherd-button-primary', action: function() { this.complete(); } }
                        ],
                        id: 'tour-a-3'
                    }
                ];

            case 'admin':
                return [
                    {
                        title: '📖 Panel Admin CSIRT',
                        text: 'Selamat datang, Admin! Berikut panduan mengelola tiket dan penugasan analis.',
                        attachTo: { element: '#tour-admin-stats', on: 'bottom' },
                        buttons: [
                            { text: ' Lewati', classes: 'csirt-skip', action: function() { this.cancel(); } },
                            { text: ' Mulai →', classes: 'shepherd-button-primary', action: function() { this.next(); } }
                        ],
                        id: 'tour-ad-0'
                    },
                    {
                        title: '📊 Overview Tiket',
                        text: 'Dashboard menampilkan <strong>statistik real-time</strong>: tiket aktif, menunggu info, belum ditugaskan, kritis, dan selesai.',
                        attachTo: { element: '#tour-admin-stats', on: 'bottom' },
                        buttons: [
                            { text: '← Kembali', action: function() { this.back(); } },
                            { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }
                        ],
                        id: 'tour-ad-1'
                    },
                    {
                        title: '🎯 Penugasan Tiket',
                        text: 'Tiket yang <strong>belum ditugaskan</strong> muncul di sini. Klik tiket lalu gunakan panel "Penugasan Analis" untuk menugaskan agen yang tepat.',
                        attachTo: { element: '#tour-admin-assign', on: 'top' },
                        buttons: [
                            { text: '← Kembali', action: function() { this.back(); } },
                            { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }
                        ],
                        id: 'tour-ad-2'
                    },
                    {
                        title: '⚡ Tiket Kritis',
                        text: 'Tiket berlabel <strong>"Kritis"</strong> adalah tiket yang sudah melewati batas waktu SLA. Prioritaskan penanganan tiket ini.',
                        attachTo: { element: '#tour-admin-stats', on: 'bottom' },
                        buttons: [
                            { text: '← Kembali', action: function() { this.back(); } },
                            { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }
                        ],
                        id: 'tour-ad-3'
                    },
                    {
                        title: '📋 Semua Tiket',
                        text: 'Klik tombol ini untuk melihat <strong>daftar lengkap semua tiket</strong> dan mengelola penugasan secara detail.',
                        attachTo: { element: '#tour-admin-alltickets', on: 'bottom' },
                        buttons: [
                            { text: '← Kembali', action: function() { this.back(); } },
                            { text: ' Selesai ✓', classes: 'shepherd-button-primary', action: function() { this.complete(); } }
                        ],
                        id: 'tour-ad-4'
                    }
                ];

            default:
                return [];
        }
    }

    function startTour() {
        // Destroy existing tour completely
        if (window._csirtTour) {
            try { window._csirtTour.complete(); } catch(e) {}
            try { window._csirtTour.destroy(); } catch(e) {}
            window._csirtTour = null;
            // Force remove any leftover shepherd elements from DOM
            document.querySelectorAll('.shepherd-element, .shepherd-modal-overlay-container').forEach(el => el.remove());
        }

        const steps = getSteps(role);
        if (steps.length === 0) return;

        const tour = new Shepherd.Tour({
            defaultStepOptions: {
                cancelIcon: { enabled: true },
                scrollTo: { behavior: 'smooth', block: 'center' },
                overlay: true,
                popperOptions: {
                    modifiers: [{ name: 'offset', options: { offset: [0, 12] } }]
                }
            },
            useModalOverlay: false,
            exitOnEsc: true
        });

        steps.forEach(step => {
            tour.addStep(step);
        });

        tour.on('complete', function() {
            localStorage.setItem(storageKey, 'true');
        });

        tour.on('cancel', function() {
            localStorage.setItem(storageKey, 'true');
        });

        window._csirtTour = tour;
        tour.start();
    }

    // Global function for Tutorial button onclick
    window.startCsirtTour = function() {
        localStorage.removeItem('csirt_tour_completed_' + window.csirtTourRole);
        startTour();
    };

    // Bind trigger buttons
    bindTriggerButtons();
});
</script>
