(() => {
    const init = () => {
        const button = document.getElementById('jt-selfcheckin-button');
        const form = document.getElementById('jt-selfcheckin-form');
        const status = document.getElementById('jt-selfcheckin-status');
        if (!button || !form || button.dataset.jtBound === '1') return;
        button.dataset.jtBound = '1';

        const message = (key, fallback) => button.dataset[key] || fallback;
        const setStatus = (text, isError = false) => {
            if (!status) return;
            status.textContent = text;
            status.classList.toggle('text-danger', isError);
        };

        button.addEventListener('click', () => {
            if (!window.isSecureContext) {
                setStatus(message('insecure', 'Standortbestimmung ist nur über HTTPS möglich.'), true);
                return;
            }
            if (!('geolocation' in navigator)) {
                setStatus(message('unavailable', 'Dein Browser unterstützt keine Standortbestimmung.'), true);
                return;
            }

            button.disabled = true;
            setStatus(message('locating', 'Standort wird ermittelt …'));
            navigator.geolocation.getCurrentPosition((position) => {
                const lat = document.getElementById('jt-selfcheckin-latitude');
                const lon = document.getElementById('jt-selfcheckin-longitude');
                if (!lat || !lon) {
                    button.disabled = false;
                    setStatus(message('error', 'Der Standort konnte nicht verarbeitet werden.'), true);
                    return;
                }
                lat.value = String(position.coords.latitude);
                lon.value = String(position.coords.longitude);
                form.submit();
            }, (error) => {
                button.disabled = false;
                let text = message('error', 'Der Standort konnte nicht ermittelt werden.');
                if (error.code === 1) text = message('denied', 'Standortzugriff wurde verweigert. Bitte erlaube ihn in den Browser-Einstellungen.');
                if (error.code === 2) text = message('positionUnavailable', 'Deine Position ist derzeit nicht verfügbar.');
                if (error.code === 3) text = message('timeout', 'Die Standortbestimmung hat zu lange gedauert. Bitte erneut versuchen.');
                setStatus(text, true);
            }, {enableHighAccuracy: true, timeout: 15000, maximumAge: 0});
        });
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
