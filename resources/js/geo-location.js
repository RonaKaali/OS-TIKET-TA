/**
 * Zero Trust - GPS integration
 *
 * Mengirim koordinat ke backend (hanya saat user sudah login).
 * GPS disimpan di session + database agar tetap tersedia di Vercel serverless.
 */

(function () {
    "use strict";

    if (!("geolocation" in navigator)) {
        return;
    }

    const CSRF_TOKEN = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    const USER_ID = document
        .querySelector('meta[name="user-id"]')
        ?.getAttribute("content");

    if (!CSRF_TOKEN || !USER_ID) {
        return;
    }

    const STORAGE_KEY = "zt_gps_last_sent";
    const SESSION_KEY = "zt_gps_session_" + USER_ID;

    if (window.__ztGpsBootstrapped) {
        sessionStorage.setItem(SESSION_KEY, "1");
    }
    const MAX_AGE_MINUTES = 10;
    const MAX_RETRIES = 4;
    const isMobile = /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent);
    let sending = false;

    function shouldSend() {
        if (!sessionStorage.getItem(SESSION_KEY)) {
            return true;
        }

        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) {
                return true;
            }

            const last = JSON.parse(raw);
            if (!last || last.userId !== USER_ID) {
                return true;
            }
            if (!last.timestamp) {
                return true;
            }

            const diffMinutes =
                (Date.now() - new Date(last.timestamp).getTime()) / 60000;
            return diffMinutes >= MAX_AGE_MINUTES;
        } catch (e) {
            return true;
        }
    }

    function saveSent(data) {
        sessionStorage.setItem(SESSION_KEY, "1");

        try {
            localStorage.setItem(
                STORAGE_KEY,
                JSON.stringify({
                    userId: USER_ID,
                    timestamp: new Date().toISOString(),
                    data,
                }),
            );
        } catch (e) {
            // ignore
        }
    }

    function sendGps(position) {
        if (sending) {
            return;
        }

        const coords = position.coords || {};
        const payload = {
            latitude: coords.latitude,
            longitude: coords.longitude,
            accuracy: coords.accuracy,
        };

        if (
            typeof payload.latitude !== "number" ||
            typeof payload.longitude !== "number"
        ) {
            return;
        }

        sending = true;

        fetch("/zero-trust/gps", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": CSRF_TOKEN,
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
            credentials: "same-origin",
            body: JSON.stringify(payload),
        })
            .then((res) => {
                if (res.ok) {
                    saveSent(payload);
                }
            })
            .catch(() => {
                // retry ditangani oleh requestGps
            })
            .finally(() => {
                sending = false;
            });
    }

    function requestGps(attempt = 0, force = false) {
        if (!force && !shouldSend()) {
            return;
        }

        navigator.geolocation.getCurrentPosition(
            sendGps,
            function () {
                if (attempt + 1 < MAX_RETRIES) {
                    setTimeout(
                        () => requestGps(attempt + 1, true),
                        isMobile ? 5000 : 2500,
                    );
                }
            },
            {
                enableHighAccuracy: isMobile,
                maximumAge: isMobile ? 60000 : 5 * 60 * 1000,
                timeout: isMobile ? 30000 : 12000,
            },
        );
    }

    function init() {
        requestGps(0, true);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }

    window.addEventListener("pageshow", () => requestGps(0, true));

    document.addEventListener("visibilitychange", () => {
        if (document.visibilityState === "visible") {
            requestGps(0, true);
        }
    });
})();
