/**
 * alert.js — Global alert banner status loader
 * Personal notifications are handled by alert-v2.js
 */
(function () {
    async function loadGlobalAlertStatus() {
        try {
            const res = await fetch('/admin/alerts', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;

            const contentType = res.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) return;

            const payload = await res.json();
            const alerts  = Array.isArray(payload) ? payload : (payload.data || []);
            if (!alerts.length) return;

            // Find the most recent active alert
            const latest = alerts[0];
            if (!latest) return;

            // Update the Alpine.js global banner if it exists
            const banner = document.getElementById('global-alert-banner');
            if (!banner) return;

            const severity = (latest.severity || 'normal').toLowerCase();
            let status = 'Normal';
            if (severity === 'warning' || severity === 'advisory') status = 'Advisory';
            if (severity === 'danger'  || severity === 'critical')  status = 'Immediate Danger';

            // Dispatch to Alpine component
            banner.dispatchEvent(new CustomEvent('update-status', {
                detail: { status, message: latest.message || '' }
            }));

        } catch (err) {
            // Non-critical — silently fail
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadGlobalAlertStatus();
        setInterval(loadGlobalAlertStatus, 60000);
    });
})();
