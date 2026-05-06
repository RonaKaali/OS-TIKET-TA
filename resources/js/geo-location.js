/**
 * Zero Trust - GPS + GeoIP integration
 *
 * Mengambil lokasi GPS (jika user mengizinkan) dan mengirimkannya
 * ke backend untuk digabungkan dengan GeoIP dalam risk scoring.
 */

(function () {
    "use strict";

    console.log("[ZeroTrust] geo-location.js loaded");

    // Hanya jalan di browser yang mendukung geolocation
    if (!("geolocation" in navigator)) {
        return;
    }

    const CSRF_TOKEN = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    if (!CSRF_TOKEN) {
        return;
    }

    // Jangan spam server: kirim kalau belum pernah kirim
    // atau data terakhir sudah lebih dari N menit.
    const STORAGE_KEY = "zt_gps_last_sent";
    const MAX_AGE_MINUTES = 10;

    function shouldSend() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return true;
            const last = JSON.parse(raw);
            if (!last || !last.timestamp) return true;
            const diffMs = Date.now() - new Date(last.timestamp).getTime();
            const diffMinutes = diffMs / 60000;
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

        // Jangan kirim jika koordinat tidak valid
        if (
            typeof payload.latitude !== "number" ||
            typeof payload.longitude !== "number"
        ) {
            return;
        }

        console.log("[ZeroTrust] sending GPS payload", payload);

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
                console.log("[ZeroTrust] GPS update response", res.status);
                // Hanya tandai sukses kalau status 2xx
                if (res.ok) {
                    saveSent(payload);
                }
            })
            .catch((err) => {
                console.log("[ZeroTrust] GPS update failed", err);
            });
    }

    function requestGps() {
        if (!shouldSend()) {
            return;
        }

        navigator.geolocation.getCurrentPosition(
            sendGps,
            function () {
                // user menolak / error -> diam saja
            },
            {
                enableHighAccuracy: false,
                maximumAge: 5 * 60 * 1000, // boleh pakai data cache 5 menit
                timeout: 10000,
            },
        );
    }

    // Jalankan setelah halaman siap
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", requestGps);
    } else {
        requestGps();
    }
})();
