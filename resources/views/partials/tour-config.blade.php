{{-- 
    Tour Configuration for CSIRT Kalselprov — COMPREHENSIVE VERSION
    
    Multi-page tour: each page has its own tour steps.
    The JS variable `csirtTourRole` and `csirtTourPage` must be set before this partial.
    
    Usage in layouts:
    @php
        $tourRole = 'visitor'; // or 'portal', 'agent', 'admin'
        $tourPage = 'dashboard'; // or 'tickets', 'ticket-show', etc.
    @endphp
    <script>
        window.csirtTourRole = '{{ $tourRole }}';
        window.csirtTourPage = '{{ $tourPage }}';
    </script>
    @include('partials.tour-config')
--}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.csirtTourRole === 'undefined' || !window.csirtTourRole) return;

    const role = window.csirtTourRole;
    const page = window.csirtTourPage || 'default';
    const storageKey = 'csirt_tour_completed_' + role + '_' + page;

    function getSteps(role, page) {
        switch(role + '|' + page) {

        // ============================
        // VISITOR — Landing Page
        // ============================
        case 'visitor|welcome':
            return [
                { title: '📖 Selamat Datang', text: 'Ini adalah <strong>Portal Pelaporan Insiden Siber</strong> CSIRT Kalselprov. Mari kita kenali fitur-fitur utama.', attachTo: { element: '#tour-brand', on: 'bottom' }, buttons: [{ text: ' Lewati', classes: 'csirt-skip', action: function() { this.cancel(); } }, { text: ' Mulai →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🚨 Laporkan Insiden', text: 'Tombol utama ini mengarahkan Anda ke <strong>formulir pelaporan insiden siber</strong>. Isi detail insiden: jenis serangan, dampak, dan bukti pendukung.', attachTo: { element: '#tour-cta-report', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🔍 Lacak Laporan', text: 'Punya nomor tiket? Masukkan di sini untuk <strong>melacak status</strong> penanganan insiden secara real-time tanpa perlu login.', attachTo: { element: '#tour-cta-track', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📊 Statistik Insiden', text: 'Grafik ini menampilkan <strong>tren insiden siber</strong> beberapa bulan terakhir. Data ini membantu memahami pola serangan di Kalsel.', attachTo: { element: '#tour-stats', on: 'top' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🛡️ Zero Trust Security', text: 'Portal ini dilindungi <strong>Zero Trust Architecture</strong> — verifikasi berlapis, fingerprint perangkat, dan deteksi anomali. Data Anda aman.', attachTo: { element: '#tour-features', on: 'top' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '✅ Siap Melapor!', text: 'Untuk melapor, klik tombol <strong>"Laporkan Insiden"</strong> atau <strong>"Masuk"</strong> jika sudah punya akun. Jika butuh bantuan, gunakan tombol <strong>"Tutorial"</strong> kapan saja.', attachTo: { element: '#tour-nav', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: ' Selesai ✓', classes: 'shepherd-button-primary', action: function() { this.complete(); } }] }
            ];

        // ============================
        // PORTAL USER — Landing Page (after login)
        // ============================
        case 'portal|welcome':
            return [
                { title: '📖 Portal Pelaporan', text: 'Anda sudah login sebagai <strong>Pelapor</strong>. Berikut panduan cara membuat dan melacak laporan insiden.', attachTo: { element: '#tour-brand', on: 'bottom' }, buttons: [{ text: ' Lewati', classes: 'csirt-skip', action: function() { this.cancel(); } }, { text: ' Mulai →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🚨 Buat Laporan Baru', text: 'Klik tombol ini untuk mengisi <strong>formulir pelaporan</strong>. Sertakan: jenis insiden, departemen terdampak, kronologi, dan bukti.', attachTo: { element: '#tour-cta-report', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🔍 Lacak Status', text: 'Setelah melapor, Anda mendapat <strong>nomor tiket</strong>. Gunakan tombol ini untuk cek progres penanganan kapan saja.', attachTo: { element: '#tour-cta-track', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '⚙️ Profil Saya', text: 'Klik menu <strong>Profil</strong> untuk mengatur akun, mengaktifkan <strong>MFA (Multi-Factor Authentication)</strong>, dan mengelola keamanan akun.', attachTo: { element: '#tour-nav', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: ' Selesai ✓', classes: 'shepherd-button-primary', action: function() { this.complete(); } }] }
            ];

        // ============================
        // AGENT — Dashboard
        // ============================
        case 'agent|dashboard':
            return [
                { title: '📖 Dashboard Agen', text: 'Ini adalah <strong>pusat komando</strong> Anda. Semua tiket dan tugas ditampilkan di sini.', attachTo: { element: '#tour-agent-stats', on: 'bottom' }, buttons: [{ text: ' Lewati', classes: 'csirt-skip', action: function() { this.cancel(); } }, { text: ' Mulai →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📊 Kartu Statistik', text: '<strong>"Ditugaskan"</strong>: tiket aktif yang harus Anda kerjakan. <strong>"Dalam Proses"</strong>: sedang dikerjakan. <strong>"Selesai"</strong>: sudah ditutup.', attachTo: { element: '#tour-agent-stats', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📬 Surat Tugas Baru', text: 'Angka <strong>"Surat Tugas Baru"</strong> menunjukkan tugas yang belum Anda konfirmasi. Klik untuk <strong>menerima atau menolak</strong> penugasan.', attachTo: { element: '#tour-agent-tasks', on: 'top' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📋 Daftar Tugas Aktif', text: 'Ini daftar tiket yang harus Anda tangani. Klik tiket untuk melihat <strong>detail, merespon, dan menyelesaikan</strong>.', attachTo: { element: '#tour-agent-tasks', on: 'top' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🔔 Notifikasi', text: 'Bell notifikasi menampilkan <strong>tugas baru</strong> yang masuk. Klik untuk melihat detail.', attachTo: { element: '#tour-notification', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🧭 Navigasi', text: '<strong>"Tiket Saya"</strong> untuk daftar lengkap. <strong>"Tutorial"</strong> untuk mengulang panduan ini. Menu navigasi di atas untuk berpindah halaman.', attachTo: { element: '#tour-nav', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: ' Selesai ✓', classes: 'shepherd-button-primary', action: function() { this.complete(); } }] }
            ];

        // ============================
        // AGENT — Ticket List (index)
        // ============================
        case 'agent|tickets':
            return [
                { title: '📋 Daftar Laporan', text: 'Halaman ini menampilkan <strong>semua tiket</strong> yang bisa Anda akses. Gunakan filter untuk menemukan tiket tertentu.', attachTo: { element: '#tour-ticket-filters', on: 'bottom' }, buttons: [{ text: ' Lewati', classes: 'csirt-skip', action: function() { this.cancel(); } }, { text: ' Mulai →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🔍 Filter Status', text: 'Pilih status untuk menyaring tiket: <strong>Terbuka, Menunggu Pelapor, Ditugaskan, atau Tertutup</strong>.', attachTo: { element: '#tour-filter-status', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🔎 Pencarian Cepat', text: 'Ketik nomor laporan, subjek, atau email pelapor untuk <strong>mencari tiket spesifik</strong> secara instan.', attachTo: { element: '#tour-filter-search', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📊 Tabel Tiket', text: 'Setiap baris menampilkan: <strong>nomor laporan, subjek, pelapor, status, prioritas, dan waktu</strong>. Klik "LIHAT" untuk detail.', attachTo: { element: '#tour-ticket-table', on: 'top' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🔴 Status & Warning', text: 'Badge warna menunjukkan status: <strong>Biru</strong>=Terbuka, <strong>Kuning</strong>=Menunggu, <strong>Merah</strong>=Terlambat. Tiket terlambat ditandai <strong>⚠️</strong>.', attachTo: { element: '#tour-ticket-table', on: 'top' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📄 Pagination', text: 'Jika tiket banyak, gunakan <strong>navigasi halaman</strong> di bawah untuk berpindah.', attachTo: { element: '#tour-pagination', on: 'top' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: ' Selesai ✓', classes: 'shepherd-button-primary', action: function() { this.complete(); } }] }
            ];

        // ============================
        // AGENT — Ticket Detail (show)
        // ============================
        case 'agent|ticket-show':
            return [
                { title: '📄 Detail Tiket', text: 'Halaman ini berisi <strong>seluruh informasi</strong> insiden siber yang ditugaskan kepada Anda. Mari kita kenali setiap bagian.', attachTo: { element: '#tour-surat-tugas', on: 'bottom' }, buttons: [{ text: ' Lewati', classes: 'csirt-skip', action: function() { this.cancel(); } }, { text: ' Mulai →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📋 Surat Tugas', text: 'Bagian ini adalah <strong>surat tugas resmi</strong> berisi: identitas analis, detail insiden, nomor tiket, dan deadline. Klik <strong>"Cetak/Download PDF"</strong> untuk versi cetak.', attachTo: { element: '#tour-surat-tugas', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '💬 Balasan & Kronologi', text: 'Gulir ke bawah untuk melihat <strong>kronologi percakapan</strong> dengan pelapor. Kirim respon via form "Kirim Respon Analisis".', attachTo: { element: '#tour-reply-form', on: 'top' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📎 Lampiran', text: 'Saat membalas, Anda bisa <strong>melampirkan bukti</strong>: gambar, PDF, atau dokumen. Pelapor juga bisa mengirim lampiran.', attachTo: { element: '#tour-reply-form', on: 'top' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '⚡ Aksi Tiket', text: 'Panel di sidebar untuk <strong>mengubah status</strong> tiket. Pilih status baru lalu klik "Konfirmasi Perubahan".', attachTo: { element: '#tour-aksi-tiket', on: 'left' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '✅ Selesaikan Tiket', text: 'Setelah insiden tertangani, klik tombol ini untuk <strong>menandai tiket selesai</strong>. Pelapor akan mendapat notifikasi email.', attachTo: { element: '#tour-selesaikan', on: 'left' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📝 Catatan Internal', text: 'Gunakan area ini untuk <strong>menulis catatan</strong> yang hanya terlihat oleh tim analis. Berguna untuk dokumentasi internal.', attachTo: { element: '#tour-catatan', on: 'left' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: ' Selesai ✓', classes: 'shepherd-button-primary', action: function() { this.complete(); } }] }
            ];

        // ============================
        // SUPER ADMIN — Dashboard
        // ============================
        case 'super-admin|dashboard':
            return [
                { title: '📖 Dashboard Super Admin', text: 'Selamat datang, <strong>Super Admin</strong>! Ini adalah <strong>pusat komando CSIRT</strong> — Anda memiliki akses penuh ke seluruh sistem.', attachTo: { element: '#tour-sa-stats', on: 'bottom' }, buttons: [{ text: ' Lewati', classes: 'csirt-skip', action: function() { this.cancel(); } }, { text: ' Mulai →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📊 Statistik Laporan', text: '<strong>Laporan Aktif</strong>: insiden yang sedang ditangani. <strong>Menunggu Info</strong>: butuh respon. <strong>Status Kritis</strong>: terlambat SLA. <strong>Telah Ditangani</strong>: sudah selesai.', attachTo: { element: '#tour-sa-stats', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📈 Analitik Insiden', text: 'Grafik <strong>Trend Bulanan</strong> ini menampilkan data laporan selesai vs insiden kritis. Gunakan untuk <strong>analisis pola serangan</strong> dan pengambilan keputusan.', attachTo: { element: '#tour-sa-chart', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🛡️ Zero Trust Security', text: 'Klik tombol ini untuk masuk ke <strong>Ruang Zero Trust</strong> — panel keamanan lanjutan: manajemen perangkat, fingerprint, dan deteksi anomali.', attachTo: { element: '#tour-sa-zerotrust', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🔴 Global Threat Intel', text: 'Panel sebelah kanan menampilkan <strong>umpan berita ancaman siber global</strong> secara real-time. Tetap pantau untuk ancaman terbaru.', attachTo: { element: '#tour-sa-news', on: 'left' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🧭 Navigasi', text: '<strong>Dashboard</strong>: halaman ini. <strong>Tiket Laporan</strong>: daftar semua tiket. <strong>Panel Admin</strong>: pengelolaan pengguna & departemen. <strong>Zero Trust</strong>: keamanan.', attachTo: { element: '#tour-sa-nav', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: ' Selesai ✓', classes: 'shepherd-button-primary', action: function() { this.complete(); } }] }
            ];

        // ============================
        // ADMIN — Dashboard
        // ============================
        case 'admin|dashboard':
            return [
                { title: '📖 Dashboard Admin', text: 'Selamat datang, Admin! Ini adalah <strong>pusat penugasan</strong> — Anda mengelola tiket dan menugaskan analis.', attachTo: { element: '#tour-admin-stats', on: 'bottom' }, buttons: [{ text: ' Lewati', classes: 'csirt-skip', action: function() { this.cancel(); } }, { text: ' Mulai →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📊 Statistik Tiket', text: '<strong>Aktif</strong>: sedang ditangani. <strong>Menunggu Info</strong>: butuh respon pelapor. <strong>Belum Ditugaskan</strong>: belum ada analis. <strong>Kritis</strong>: lewat deadline SLA.', attachTo: { element: '#tour-admin-stats', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '⚠️ Perhatikan: Belum Ditugaskan', text: 'Angka merah ini kritis — <strong>tiket baru yang belum ada analis</strong>. Klik tiket di bawah untuk langsung menugaskan.', attachTo: { element: '#tour-admin-assign', on: 'top' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🎯 Cara Menugaskan Analis', text: '1️⃣ Klik "Tugaskan Agen" → 2️⃣ Di halaman detail, pilih analis di panel "Penugasan Analis" → 3️⃣ Pilih urgensi → 4️⃣ Klik "Tugaskan Analis".', attachTo: { element: '#tour-admin-assign', on: 'top' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📋 Semua Tiket', text: 'Klik tombol ini untuk melihat <strong>daftar lengkap semua tiket</strong> — filter, cari, dan kelola dari sini.', attachTo: { element: '#tour-admin-alltickets', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🔔 Notifikasi', text: 'Bell notifikasi menampilkan <strong>tugas baru</strong> yang perlu direspons. Cek secara berkala.', attachTo: { element: '#tour-notification', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selesai ✓', classes: 'shepherd-button-primary', action: function() { this.complete(); } }] }
            ];

        // ============================
        // ADMIN — Ticket Detail
        // ============================
        case 'admin|ticket-show':
            return [
                { title: '📄 Detail Tiket', text: 'Anda melihat detail tiket sebagai Admin. Tugas utama: <strong>menugaskan analis</strong> untuk menangani insiden ini.', attachTo: { element: '#tour-surat-tugas', on: 'bottom' }, buttons: [{ text: ' Lewati', classes: 'csirt-skip', action: function() { this.cancel(); } }, { text: ' Mulai →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📋 Surat Tugas', text: 'Informasi lengkap insiden: <strong>nomor tiket, departemen, prioritas, analis, dan deadline</strong>. Cetak PDF untuk arsip.', attachTo: { element: '#tour-surat-tugas', on: 'bottom' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '👥 Penugasan Analis', text: 'Panel ini <strong>hanya muncul untuk Admin</strong>. Pilih analis dari dropdown, atur urgensi, lalu klik <strong>"Tugaskan Analis"</strong>.', attachTo: { element: '#tour-penugasan', on: 'left' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '🔄 Kembalikan ke Super Admin', text: 'Jika tiket perlu eskalasi, klik tombol ini untuk <strong>mengembalikan ke Super Admin</strong>. Status akan direset.', attachTo: { element: '#tour-aksi-tiket', on: 'left' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: 'Selanjutnya →', classes: 'shepherd-button-primary', action: function() { this.next(); } }] },
                { title: '📝 Catatan Internal', text: 'Tulis catatan <strong>hanya untuk tim analis</strong>. Berguna untuk koordinasi internal tanpa diketahui pelapor.', attachTo: { element: '#tour-catatan', on: 'left' }, buttons: [{ text: '← Kembali', action: function() { this.back(); } }, { text: ' Selesai ✓', classes: 'shepherd-button-primary', action: function() { this.complete(); } }] }
            ];

        // Default empty tour
        default:
            return [];
        }
    }

    function startTour() {
        if (window._csirtTour) {
            try { window._csirtTour.complete(); } catch(e) {}
            try { window._csirtTour.destroy(); } catch(e) {}
            window._csirtTour = null;
            document.querySelectorAll('.shepherd-element, .shepherd-modal-overlay-container').forEach(el => el.remove());
        }

        const steps = getSteps(role, page);
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
            step.buttons = step.buttons.map(b => {
                if (b.text) b.text = b.text.trim();
                return b;
            });
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

    window.startCsirtTour = function() {
        localStorage.removeItem(storageKey);
        startTour();
    };

    document.querySelectorAll('.csirt-tour-trigger').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            startCsirtTour();
        });
    });
});
</script>
