/**
 * Zero Trust - GPS integration
 *
 * Mengirim koordinat GPS ke backend (hanya saat user sudah login).
 * Throttle diselaraskan dengan user-id agar session baru setelah login
 * tetap memicu pengiriman ulang.
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
    const MAX_AGE_MINUTES = 10;
    const MAX_RETRIES = 3;
    const isMobile = /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent);

    function shouldSend() {
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

        fetch("/zero-trust/gps", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": CSRF_TOKEN,
                "X-Requested-With": "XMLHttpRequest",
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
                // diam, retry ditangani oleh requestGps
            });
    }

    function requestGps(attempt = 0) {
        if (!shouldSend()) {
            return;
        }

        navigator.geolocation.getCurrentPosition(
            sendGps,
            function () {
                if (attempt + 1 < MAX_RETRIES) {
                    setTimeout(
                        () => requestGps(attempt + 1),
                        isMobile ? 4000 : 2000,
                    );
                }
            },
            {
                enableHighAccuracy: isMobile,
                maximumAge: isMobile ? 0 : 5 * 60 * 1000,
                timeout: isMobile ? 25000 : 10000,
            },
        );
    }

    function init() {
        requestGps();
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }

    document.addEventListener("visibilitychange", () => {
        if (document.visibilityState === "visible") {
            requestGps();
        }
    });
})();
