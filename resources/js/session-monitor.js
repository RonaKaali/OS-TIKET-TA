/**
 * Session Monitor - Auto logout dan reload jika session expired
 */
(function () {
    'use strict';

    // Konfigurasi
    const CHECK_INTERVAL = 30000; // Cek setiap 30 detik (30000 ms)
    let checkInterval = null;
    let isChecking = false;

    /**
     * Cek apakah user sudah login dengan melihat path atau elemen tertentu
     */
    function isAuthenticated() {
        const path = window.location.pathname;
        // Skip jika sudah di halaman login, register, atau welcome
        if (path === '/login' || path === '/register' || path === '/' || path.startsWith('/login') || path.startsWith('/register')) {
            return false;
        }
        // Jika ada meta tag atau attribute yang menunjukkan user sudah login
        return true;
    }

    /**
     * Cek status session dari server
     */
    async function checkSession() {
        // Jangan cek jika sedang ada request yang berjalan
        if (isChecking) {
            return;
        }

        // Skip jika tidak di halaman yang memerlukan auth
        if (!isAuthenticated()) {
            return;
        }

        try {
            isChecking = true;

            const response = await fetch('/session/check', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin'
            });

            if (response.status === 401) {
                let message = null;
                try {
                    const data = await response.json();
                    if (data.revoked) {
                        message = data.message || 'Akses Anda telah dicabut oleh administrator.';
                    } else if (data.expired) {
                        message = 'Session Anda telah berakhir karena tidak ada aktivitas.';
                    }
                } catch (e) {
                    // ignore parse error
                }
                handleSessionExpired(message);
                return;
            }

            if (!response.ok) {
                return;
            }

            const data = await response.json();

            if (!data.authenticated || data.expired) {
                handleSessionExpired(data.expired
                    ? 'Session Anda telah berakhir karena tidak ada aktivitas.'
                    : null);
                return;
            }

        } catch (error) {
            console.error('Error checking session:', error);
            // Jika error network, jangan langsung redirect
            // Hanya redirect jika jelas session expired
            if (error.message && error.message.includes('401')) {
                handleSessionExpired();
            }
        } finally {
            isChecking = false;
        }
    }

    /**
     * Handle session expired - redirect ke login
     */
    function handleSessionExpired(customMessage) {
        // Hentikan interval checking
        if (checkInterval) {
            clearInterval(checkInterval);
            checkInterval = null;
        }

        // Cek jika sudah di halaman login, jangan redirect lagi
        if (window.location.pathname === '/login' || window.location.pathname.startsWith('/login')) {
            return;
        }

        alert(customMessage || 'Session Anda telah berakhir. Anda akan diarahkan ke halaman login.');
        window.location.href = '/login';
    }

    /**
     * Initialize session monitoring
     */
    function initSessionMonitor() {
        // Cek apakah user sudah login
        if (!isAuthenticated()) {
            return;
        }

        // Mulai interval checking session setiap 30 detik
        checkInterval = setInterval(() => {
            checkSession();
        }, CHECK_INTERVAL);

        // Cek session pertama kali setelah 2 menit 30 detik
        // (agar lebih cepat mendeteksi expired session)
        setTimeout(() => {
            checkSession();
        }, 150000); // 2.5 menit
    }

    // Initialize ketika DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSessionMonitor);
    } else {
        // DOM sudah ready
        initSessionMonitor();
    }

    // Cleanup ketika page unload
    window.addEventListener('beforeunload', () => {
        if (checkInterval) {
            clearInterval(checkInterval);
        }
    });

})();

