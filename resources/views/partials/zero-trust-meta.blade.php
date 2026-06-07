@auth
<meta name="user-id" content="{{ auth()->id() }}">
<script>
(function () {
    if (!('geolocation' in navigator) || window.__ztGpsBootstrapped) {
        return;
    }
    window.__ztGpsBootstrapped = true;

    var tokenEl = document.querySelector('meta[name="csrf-token"]');
    if (!tokenEl) {
        return;
    }

    var csrf = tokenEl.getAttribute('content');
    var isMobile = /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent);

    navigator.geolocation.getCurrentPosition(function (pos) {
        var c = pos.coords || {};
        if (typeof c.latitude !== 'number' || typeof c.longitude !== 'number') {
            return;
        }

        fetch('/zero-trust/gps', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                latitude: c.latitude,
                longitude: c.longitude,
                accuracy: c.accuracy,
            }),
        });
    }, function () {}, {
        enableHighAccuracy: isMobile,
        timeout: isMobile ? 30000 : 12000,
        maximumAge: isMobile ? 60000 : 300000,
    });
})();
</script>
@endauth
