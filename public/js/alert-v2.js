
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('alerts-sidebar');
    const toggleButton = document.getElementById('alerts-toggle-button');
    const menuIcon = document.getElementById('alerts-menu-icon');
    const closeIcon = document.getElementById('alerts-close-icon');
    const sidebarClose = document.getElementById('alerts-sidebar-close');

    const SIDEBAR_WIDTH = '256px'; // w-64 = 16rem = 256px
    const BUTTON_CLOSED_LEFT = '0px';
    const BUTTON_OPEN_LEFT = SIDEBAR_WIDTH;

    function initializeButtonState() {
        toggleButton.style.left = BUTTON_CLOSED_LEFT;
        menuIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
    }

    function toggleSidebar() {
        const isOpen = sidebar.classList.contains('translate-x-0');
        if (isOpen) {
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            toggleButton.style.left = BUTTON_CLOSED_LEFT;
            menuIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
            toggleButton.setAttribute('aria-expanded', 'false');
        } else {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            toggleButton.style.left = BUTTON_OPEN_LEFT;
            menuIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
            toggleButton.setAttribute('aria-expanded', 'true');
        }
    }

    toggleButton.addEventListener('click', toggleSidebar);
    if (sidebarClose) sidebarClose.addEventListener('click', toggleSidebar);

    window.addEventListener('resize', () => {
        const isOpen = sidebar.classList.contains('translate-x-0');
        toggleButton.style.left = isOpen ? BUTTON_OPEN_LEFT : BUTTON_CLOSED_LEFT;
    });

    initializeButtonState();

    // Notification loading (ported from previous drawer)
    async function loadMyAlerts() {
        try {
            const res = await fetch('/api/user/notifications', { credentials: 'same-origin' });
            if (!res.ok) throw new Error('failed');
            // check content-type to avoid trying to parse HTML as JSON (which caused unexpected token '<')
            const contentType = res.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                throw new Error('Unexpected response content-type: ' + contentType);
            }
            const payload = await res.json();
            const items = (payload && payload.data) ? payload.data : [];
            const list = document.getElementById('personal-alerts-list');
            if (!list) return;
            list.innerHTML = '';
            if (!items.length) {
                list.innerHTML = '<div class="text-xs text-gray-500">No unread alerts.</div>';
                return;
            }
            items.forEach(n => {
                const data = n.data || {};
                const id = n.id || '';
                const title = data.title || (data.alert_id ? 'Alert' : 'Untitled');
                const message = data.message || '';
                const when = new Date(n.created_at).toLocaleString();
                const el = document.createElement('div');
                el.className = 'p-3  bg-gray-200 text-gray-100';
                el.innerHTML = `<div class="flex items-start justify-between"><div class="flex-1"><div class="font-medium text-gray-100">${title}</div><div class="text-xs text-gray-600 mt-1">${message}</div><div class="text-xs text-gray-400 mt-2">${when}</div></div><div class="ml-3"><button data-id="${id}" class="mark-read text-xs px-2 py-1 bg-black text-white ">Mark read</button></div></div>`;
                list.appendChild(el);
            });

            // attach handlers
            list.querySelectorAll('.mark-read').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const id = btn.getAttribute('data-id');
                    try {
                        const r = await fetch('/api/user/notifications/' + encodeURIComponent(id) + '/read', { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') } });
                        if (r.ok) loadMyAlerts();
                    } catch (err) { console.error(err); }
                });
            });
        } catch (err) {
            const list = document.getElementById('personal-alerts-list');
            if (list) list.innerHTML = '<div class="text-xs text-red-500">Unable to load alerts.</div>';
            console.error(err);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // load alerts when the document is ready
        loadMyAlerts();
    });
    // attempt refresh every 45s
    setInterval(loadMyAlerts, 45000);
});

