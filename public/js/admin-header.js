
// Dropdown functionality
document.querySelectorAll('button[aria-controls]').forEach(button => {
    button.addEventListener('click', () => {
        const isExpanded = button.getAttribute('aria-expanded') === 'true';
        const dropdownContent = document.getElementById(button.getAttribute('aria-controls'));

        button.setAttribute('aria-expanded', !isExpanded);
        dropdownContent.classList.toggle('hidden');
        button.querySelector('svg:last-child').classList.toggle('rotate-180');
    });
});


// Admin notifications: fetch recent chat/messages and render dropdown
(function () {
    const btn = document.getElementById('admin-notif-btn');
    const dropdown = document.getElementById('admin-notif-dropdown');
    const list = document.getElementById('admin-notif-list');
    const count = document.getElementById('admin-notif-count');

    if (!btn || !dropdown || !list || !count) return;

    const colors = ['bg-indigo-500', 'bg-green-500', 'bg-blue-500', 'bg-pink-500', 'bg-yellow-500', 'bg-red-500', 'bg-purple-500', 'bg-teal-500'];

    function colorClassFor(initial) {
        if (!initial) return colors[0];
        const code = initial.toUpperCase().charCodeAt(0) || 0;
        return colors[code % colors.length];
    }

    function renderItems(items) {
        list.innerHTML = '';
        if (!items || !items.length) {
            list.innerHTML = '<div class="p-3 text-sm text-gray-500">No messages</div>';
            count.classList.add('hidden');
            return;
        }

        count.textContent = items.length;
        count.classList.remove('hidden');

        items.forEach(i => {
            const initial = (i.name || i.email || 'C').charAt(0).toUpperCase();
            const avatar = document.createElement('div');
            avatar.className = `w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-medium mr-3 ${colorClassFor(initial)}`;
            avatar.textContent = initial;

            const title = document.createElement('div');
            title.className = 'font-medium text-sm text-gray-800 dark:text-gray-100';
            title.textContent = i.name || i.email || 'Chat';

            const subtitle = document.createElement('div');
            subtitle.className = 'text-xs text-gray-500 dark:text-gray-400';
            subtitle.textContent = i.email || '';

            const msg = document.createElement('div');
            msg.className = 'text-xs text-gray-600 dark:text-gray-300 mt-1';
            msg.textContent = i.message || '';

            const wrapper = document.createElement('a');
            wrapper.href = '/admin/chat';
            wrapper.className = 'block px-3 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 flex items-start';

            const left = document.createElement('div');
            left.className = 'flex-shrink-0';
            left.appendChild(avatar);

            const right = document.createElement('div');
            right.className = 'flex-1';
            right.appendChild(title);
            right.appendChild(subtitle);
            right.appendChild(msg);

            wrapper.appendChild(left);
            wrapper.appendChild(right);

            list.appendChild(wrapper);
        });
    }

    async function fetchNotifications() {
        try {
            const res = await fetch('/notifications/messages', { cache: 'no-store' });
            if (!res.ok) throw new Error('Network error');
            const data = await res.json();
            renderItems(data);
        } catch (err) {
            console.error('Failed to fetch admin notifications', err);
        }
    }

    btn.addEventListener('click', (e) => {
        const isOpen = !dropdown.classList.contains('hidden');
        if (isOpen) {
            dropdown.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        } else {
            dropdown.classList.remove('hidden');
            btn.setAttribute('aria-expanded', 'true');
            fetchNotifications();
        }
    });

    document.addEventListener('click', (e) => {
        if (!document.getElementById('admin-notifications').contains(e.target)) {
            dropdown.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        }
    });

    setInterval(() => {
        if (!document.hidden) {
            fetchNotifications();
        }
    }, 30000);
    fetchNotifications();
})();


// Admin chat bubble: poll unread sessions/users count
(function () {
    const badge = document.getElementById('admin-chat-bubble-count');

    if (!badge) return;

    async function fetchUnreadUsers() {
        try {
            const res = await fetch('/notifications/unread-users', { cache: 'no-store' });
            if (!res.ok) throw new Error('Network');
            const data = await res.json();
            const c = Number(data.count || 0);
            if (c > 0) {
                badge.textContent = c;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        } catch (err) {
            console.error('Failed to fetch unread users count', err);
        }
    }

    fetchUnreadUsers();
    setInterval(() => {
        if (!document.hidden) {
            fetchUnreadUsers();
        }
    }, 15000);
})();


document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-input');
    const resultsBox = document.getElementById('search-results');
    let searchTimer = null;
    let searchController = null;

    if (!searchInput || !resultsBox) return;

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();

        clearTimeout(searchTimer);

        if (searchController) {
            searchController.abort();
            searchController = null;
        }

        if (query.length < 2) {
            resultsBox.innerHTML = '';
            resultsBox.classList.add('hidden');
            return;
        }

        searchTimer = setTimeout(async () => {
            searchController = new AbortController();

            try {
                const response = await fetch(`/search?q=${encodeURIComponent(query)}`, {
                    signal: searchController.signal,
                });
                const data = await response.json();

                let html = '';

                if ((data.bookings && data.bookings.length === 0) && (data.guests && data.guests.length === 0)) {
                    html = `<div class="px-4 py-2 text-gray-500 text-sm">No results found</div>`;
                } else {
                    if (data.bookings && data.bookings.length) {
                        html += `<div class="px-4 py-1 text-xs text-gray-400">Bookings</div>`;
                        data.bookings.forEach(b => {
                            // Link to admin dashboard anchor for the booking so it scrolls into view
                            html += `<a href="/admin/dashboard#booking-${b.id}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">` +
                                `<span class="font-medium text-sm">${b.name}</span> <br>` +
                                `<span class="text-xs text-gray-500">${b.email || ''}</span>` +
                                `</a>`;
                        });
                    }

                    if (data.guests && data.guests.length) {
                        html += `<div class="px-4 py-1 text-xs text-gray-400">Guests</div>`;
                        data.guests.forEach(g => {
                            // Link to admin user edit page
                            html += `<a href="/admin/users/${g.id}/edit" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">` +
                                `<span class="text-sm font-medium">${g.name}</span>` +
                                `</a>`;
                        });
                    }
                }

                resultsBox.innerHTML = html;
                resultsBox.classList.remove('hidden');
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error(error);
                }
            } finally {
                searchController = null;
            }
        }, 250);
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#search-results') && !e.target.closest('#search-input')) {
            resultsBox.classList.add('hidden');
        }
    });
});
