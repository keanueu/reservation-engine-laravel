document.addEventListener('DOMContentLoaded', () => {

    const sidebar      = document.getElementById('alerts-sidebar');
    const toggleButton = document.getElementById('alerts-toggle-button');
    const menuIcon     = document.getElementById('alerts-menu-icon');
    const closeIcon    = document.getElementById('alerts-close-icon');
    const sidebarClose = document.getElementById('alerts-sidebar-close');

    if (!sidebar || !toggleButton) return; // guard: elements may not exist on every page

    const SIDEBAR_WIDTH  = '256px';
    const BUTTON_CLOSED  = '0px';
    const BUTTON_OPEN    = SIDEBAR_WIDTH;

    // Ensure the toggle button is absolutely positioned on the left edge
    toggleButton.style.position = 'fixed';
    toggleButton.style.left     = BUTTON_CLOSED;
    toggleButton.style.top      = '25%';

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        toggleButton.style.left = BUTTON_OPEN;
        menuIcon.classList.add('hidden');
        closeIcon.classList.remove('hidden');
        toggleButton.setAttribute('aria-expanded', 'true');
    }

    function closeSidebar() {
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
        toggleButton.style.left = BUTTON_CLOSED;
        menuIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
        toggleButton.setAttribute('aria-expanded', 'false');
    }

    function toggleSidebar() {
        sidebar.classList.contains('translate-x-0') ? closeSidebar() : openSidebar();
    }

    toggleButton.addEventListener('click', toggleSidebar);
    if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);

    // Keep button position correct on resize
    window.addEventListener('resize', () => {
        const isOpen = sidebar.classList.contains('translate-x-0');
        toggleButton.style.left = isOpen ? BUTTON_OPEN : BUTTON_CLOSED;
    });

    // -------------------------------------------------------
    // Load personal alerts from API
    // -------------------------------------------------------
    async function loadMyAlerts() {
        const list = document.getElementById('personal-alerts-list');
        if (!list) return;

        try {
            const res = await fetch('/api/user/notifications', { credentials: 'same-origin' });
            if (!res.ok) throw new Error('HTTP ' + res.status);

            const contentType = res.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) throw new Error('Non-JSON response');

            const payload = await res.json();
            const items   = Array.isArray(payload.data) ? payload.data : [];

            list.innerHTML = '';

            if (!items.length) {
                list.innerHTML = '<div class="text-xs text-gray-500">No unread alerts.</div>';
                updateBadge(0);
                return;
            }

            updateBadge(items.length);

            items.forEach(n => {
                const data    = n.data || {};
                const id      = n.id   || '';
                const title   = data.title   || 'Alert';
                const message = data.message || '';
                const when    = new Date(n.created_at).toLocaleString();

                const el = document.createElement('div');
                el.className = 'p-3 bg-gray-100 text-gray-900 mb-2';
                el.innerHTML = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">${title}</div>
                            <div class="text-xs text-gray-600 mt-1">${message}</div>
                            <div class="text-xs text-gray-400 mt-2">${when}</div>
                        </div>
                        <div class="ml-3 flex-shrink-0">
                            <button data-id="${id}" class="mark-read text-xs px-2 py-1 bg-black text-white">Mark read</button>
                        </div>
                    </div>
                `;
                list.appendChild(el);
            });

            list.querySelectorAll('.mark-read').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const nid = btn.getAttribute('data-id');
                    try {
                        const r = await fetch('/api/user/notifications/' + encodeURIComponent(nid) + '/read', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                        });
                        if (r.ok) loadMyAlerts();
                    } catch (err) { console.error(err); }
                });
            });

        } catch (err) {
            const list = document.getElementById('personal-alerts-list');
            if (list) list.innerHTML = '<div class="text-xs text-red-500">Unable to load alerts.</div>';
            console.error('alert-v2.js:', err);
        }
    }

    function updateBadge(count) {
        const badge = document.getElementById('alerts-unread-badge');
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    // Initial load + auto-refresh every 45s
    loadMyAlerts();
    setInterval(loadMyAlerts, 45000);
});
