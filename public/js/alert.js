(function () {
    async function loadMyAlerts() {
        try {
            const res = await fetch('/user/notifications', { credentials: 'same-origin' });
            if (!res.ok) throw new Error('failed');

            const payload = await res.json();
            const items = (payload && payload.data) ? payload.data : [];
            const list = document.getElementById('personal-alerts-list');

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
                el.className = 'p-3 border border-gray-100';
                el.innerHTML = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="font-medium">${title}</div>
                            <div class="text-xs text-gray-600 mt-1">${message}</div>
                            <div class="text-xs text-gray-400 mt-2">${when}</div>
                        </div>
                        <div class="ml-3">
                            <button data-id="${id}" class="mark-read text-xs text-white px-2 py-1 bg-black">Mark read</button>
                        </div>
                    </div>
                `;
                list.appendChild(el);
            });

            // attach handlers
            list.querySelectorAll('.mark-read').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.getAttribute('data-id');
                    try {
                        const r = await fetch('/user/notifications/' + encodeURIComponent(id) + '/read', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                            }
                        });

                        if (r.ok) loadMyAlerts();
                    } catch (err) {
                        console.error(err);
                    }
                });
            });

        } catch (err) {
            const list = document.getElementById('personal-alerts-list');
            if (list) list.innerHTML = '<div class="text-xs text-red-500">Unable to load alerts.</div>';
            console.error(err);
        }
    }

    document.addEventListener('DOMContentLoaded', loadMyAlerts);

    // auto refresh every 45 seconds
    setInterval(loadMyAlerts, 45000);
})();
