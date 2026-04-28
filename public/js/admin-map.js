
(function () {
    const card = document.getElementById('typhoon-card');
    const body = document.getElementById('typhoon-body');
    const sub = document.getElementById('typhoon-sub');
    const icon = document.getElementById('typhoon-icon');
    const times = document.getElementById('typhoon-times');
    const refresh = document.getElementById('typhoon-refresh');
    const detailsLink = document.getElementById('typhoon-details-link');

    if (!card || !body || !sub || !icon || !times || !refresh || !detailsLink) return;

    function formatDate(dStr) {
        if (!dStr) return '—';
        try {
            let s = dStr;
            if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/.test(s)) s = s.replace(' ', 'T');
            const d = new Date(s);
            return d.toLocaleString(undefined, { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
        } catch (e) { return dStr; }
    }

    function setSeverity(sev) {
        // sev could be 'warning','watch','advisory','none' or numeric scale; normalize
        const s = (sev || '').toString().toLowerCase();
        // normalize by clearing known classes then applying appropriate color
        card.classList.remove('border-red-400', 'border-orange-300', 'border-yellow-300', 'border-green-300');
        icon.classList.remove('bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500');
        if (s.includes('warning') || s.includes('severe') || s.includes('red')) {
            icon.classList.add('bg-red-600'); card.classList.add('border-red-400');
        } else if (s.includes('watch') || s.includes('orange')) {
            icon.classList.add('bg-orange-500'); card.classList.add('border-orange-300');
        } else if (s.includes('advisory') || s.includes('yellow')) {
            icon.classList.add('bg-yellow-500'); card.classList.add('border-yellow-300');
        } else {
            // clear/normal/ok
            icon.classList.add('bg-green-600'); card.classList.add('border-green-300');
        }
        // update status pill if present
        try {
            const pill = document.getElementById('typhoon-status-pill');
            if (pill) {
                pill.className = 'ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium';
                if (s.includes('warning') || s.includes('severe') || s.includes('red')) {
                    pill.classList.add('bg-red-100', 'text-red-800'); pill.textContent = 'Warning';
                } else if (s.includes('watch') || s.includes('orange')) {
                    pill.classList.add('bg-orange-100', 'text-orange-800'); pill.textContent = 'Watch';
                } else if (s.includes('advisory') || s.includes('yellow')) {
                    pill.classList.add('bg-yellow-100', 'text-yellow-800'); pill.textContent = 'Advisory';
                } else if (s.includes('clear') || s === '') {
                    pill.classList.add('bg-green-100', 'text-green-800'); pill.textContent = 'Clear';
                } else {
                    // fallback
                    pill.classList.add('bg-gray-100', 'text-gray-800'); pill.textContent = s.charAt(0).toUpperCase() + s.slice(1);
                }
                pill.classList.remove('hidden');
            }
        } catch (e) { /* ignore */ }
    }

    async function load() {
        body.innerHTML = '<div class="typhoon-loading flex items-center gap-2 text-gray-600">\n      <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">\n        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>\n        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>\n      </svg>\n      Fetching latest advisory…\n    </div>';
        sub.textContent = 'Loading latest advisory…';
        times.textContent = '—';
        detailsLink.classList.add('hidden');

        try {
            // First try weather/third-party feed
            let data = null;
            try {
                const res = await fetch('/check-typhoon-status', { credentials: 'same-origin' });
                if (res.ok) data = await res.json();
            } catch (e) { data = null; }

            // Then try admin-created alerts (prefer DB alerts if present)
            try {
                const r2 = await fetch('/api/alerts/current', { credentials: 'same-origin' });
                if (r2.ok) {
                    const alertsPayload = await r2.json();
                    // expected shapes supported: { data: [...] } or { alerts: [...] } or an array
                    let alerts = [];
                    if (alertsPayload) {
                        if (Array.isArray(alertsPayload)) alerts = alertsPayload;
                        else if (Array.isArray(alertsPayload.data)) alerts = alertsPayload.data;
                        else if (Array.isArray(alertsPayload.alerts)) alerts = alertsPayload.alerts;
                    }
                    if (alerts && alerts.length) {
                        // prefer the most recent alert
                        data = alerts[0];
                    }
                }
            } catch (e) {
                // ignore - keep whatever data we have from weather endpoint or null
            }

            renderFromData(data);

        } catch (err) {
            // Fallback: display the sample JSON provided by user when fetch fails
            const sample = { "status": "clear", "location": "Tambobong, Dasol, Pangasinan", "message": "No immediate severe weather reported near Cabanas Beach Resort (Current: Clouds)." };
            setSeverity(sample.status || 'clear');
            sub.textContent = sample.location || 'Tambobong, Dasol, Pangasinan';
            body.innerHTML = `<div class="text-sm text-gray-700 dark:text-gray-200">${sample.message}</div>`;
            times.textContent = 'Last update: —';
            detailsLink.href = '/check-typhoon-status';
            detailsLink.classList.remove('hidden');
            console.error('Typhoon fetch failed, showing sample', err);
        }
    }

    // Fetch only the weather endpoint and render it immediately (used by Refresh button)
    async function loadWeatherOnly() {
        try {
            const res = await fetch('/check-typhoon-status', { credentials: 'same-origin' });
            if (!res.ok) throw new Error('Fetch failed');
            const weatherData = await res.json();
            renderFromData(weatherData);
        } catch (err) {
            console.error('Weather-only fetch failed', err);
            // fall back to normal load which may show sample
            load();
        }
    }

    function renderFromData(data) {
        // Map common fields from endpoint or DB alert to our display model
        // expected shape (flexible): { status, location, message, event, sender, description, start, end }
        const statusVal = data && (data.status || data.severity) ? (data.status || data.severity) : '';
        const location = data && (data.location || data.area) ? (data.location || data.area) : '';
        const message = data && (data.message || data.description || data.details) ? (data.message || data.description || data.details) : '';
        const eventTitle = data && (data.event || data.title) ? (data.event || data.title) : '';
        const start = data && (data.start || data.from) ? (data.start || data.from) : null;
        const end = data && (data.end || data.until) ? (data.end || data.until) : null;

        setSeverity(statusVal || 'clear');
        sub.textContent = location ? `${location}` : (eventTitle || 'Typhoon update');

        const short = message && message.length > 240 ? message.substring(0, 240) + '…' : message;
        body.innerHTML = `
      ${eventTitle ? `<div class="font-medium text-sm text-gray-800 dark:text-gray-100">${eventTitle}</div>` : ''}
      ${short ? `<div class="text-sm text-gray-700 dark:text-gray-200 mt-1">${short}</div>` : '<div class="text-sm text-gray-600">No immediate advisory.</div>'}
    `;

        times.textContent = (start || end) ? `From: ${formatDate(start)} · Until: ${formatDate(end)}` : `Location: ${location || '—'}`;
        detailsLink.href = data && data.link ? data.link : '/check-typhoon-status';
        detailsLink.classList.remove('hidden');

        // update OpenWeather status note if present
        try {
            const note = document.getElementById('openweather-note');
            if (note) {
                const shortMsg = message ? (message.length > 220 ? message.substring(0, 220) + '…' : message) : 'No immediate severe weather reported.';
                note.textContent = `Status: ${statusVal || 'clear'} · ${location || 'Tambobong, Dasol, Pangasinan'} — ${shortMsg}`;
            }
        } catch (e) { /* ignore */ }
    }

    refresh.addEventListener('click', (e) => { e.preventDefault(); loadWeatherOnly(); });

    // initial load
    document.addEventListener('DOMContentLoaded', load);
    if (document.readyState === 'complete' || document.readyState === 'interactive') load();

    // AUTO-POLLING: refresh every minute and visually indicate changes
    const POLL_MS = 60 * 1000; // 60s
    let lastSnapshot = null; // store last known important fields to detect changes

    function snapshotFrom(data) {
        if (!data) return null;
        return (data.status || '') + '|' + (data.location || data.area || '') + '|' + (data.message || data.description || data.details || '');
    }

    async function loadAndDetect() {
        if (document.hidden) return;

        try {
            // fetch but do not trip the UI loading spinner; reuse load() which handles UI
            const res = await fetch('/check-typhoon-status', { credentials: 'same-origin' });
            if (!res.ok) throw new Error('Fetch failed');
            const data = await res.json();
            const snap = snapshotFrom(data);
            if (lastSnapshot && snap !== lastSnapshot) {
                // visual pulse to show update
                try {
                    const c = document.getElementById('typhoon-card');
                    if (c) {
                        c.classList.add('ring-4', 'ring-offset-2');
                        setTimeout(() => c.classList.remove('ring-4', 'ring-offset-2'), 1800);
                    }
                } catch (e) { /* ignore */ }
            }
            lastSnapshot = snap;
            // call the normal loader which will re-render the UI
            load();
        } catch (err) {
            // if fetch fails, still run load() which shows fallback/sample
            load();
        }
    }

    // start polling after initial load has settled
    setTimeout(() => {
        // ensure lastSnapshot seeded
        (async () => {
            try { const r = await fetch('/check-typhoon-status', { credentials: 'same-origin' }); const d = r.ok ? await r.json() : null; lastSnapshot = snapshotFrom(d); } catch (e) { lastSnapshot = null; }
        })();
        setInterval(loadAndDetect, POLL_MS);
    }, 2000);

    // Also listen for broadcasted alerts in real-time (requires Laravel Echo configured)
    try {
        if (window.Echo) {
            window.Echo.channel('typhoon-alerts').listen('AlertCreated', (e) => {
                try {
                    if (e && e.alert) {
                        // update UI immediately with the broadcast payload
                        const payload = e.alert;
                        // set severity and content
                        setSeverity(payload.severity || 'clear');
                        sub.textContent = payload.location || payload.title || 'Typhoon update';
                        body.innerHTML = `<div class="font-medium text-sm text-gray-800 dark:text-gray-100">${payload.title || ''}</div><div class="text-sm text-gray-700 dark:text-gray-200 mt-1">${payload.message || ''}</div>`;
                        times.textContent = payload.starts_at ? `From: ${formatDate(payload.starts_at)}` : '';
                        // visual pulse for real-time arrival
                        const c = document.getElementById('typhoon-card');
                        if (c) { c.classList.add('ring-4', 'ring-offset-2'); setTimeout(() => c.classList.remove('ring-4', 'ring-offset-2'), 1600); }
                    }
                } catch (inner) { console.error('AlertCreated handler error', inner); }
            });
        }
    } catch (err) { /* echo not available */ }
})();

