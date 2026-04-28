
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


    // Frontdesk notifications: fetch recent contact messages and render dropdown
    (function () {
        const btn = document.getElementById('frontdesk-notif-btn');
        const dropdown = document.getElementById('frontdesk-notif-dropdown');
        const list = document.getElementById('frontdesk-notif-list');
        const count = document.getElementById('frontdesk-notif-count');

        const colors = ['bg-indigo-500','bg-green-500','bg-blue-500','bg-pink-500','bg-yellow-500','bg-red-500','bg-purple-500','bg-teal-500'];

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
                const initial = (i.name || i.email || 'U').charAt(0).toUpperCase();
                const avatar = document.createElement('div');
                avatar.className = `w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-medium mr-3 ${colorClassFor(initial)}`;
                avatar.textContent = initial;

                const title = document.createElement('div');
                title.className = 'font-medium text-sm text-gray-800 dark:text-gray-100';
                title.textContent = i.name || i.email || 'Unknown';

                const subtitle = document.createElement('div');
                subtitle.className = 'text-xs text-gray-500 dark:text-gray-400';
                subtitle.textContent = i.email || '';

                const msg = document.createElement('div');
                msg.className = 'text-xs text-gray-600 dark:text-gray-300 mt-1';
                msg.textContent = i.message || '';

                const wrapper = document.createElement('a');
                wrapper.href = '/send_email/' + i.id;
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
                const res = await fetch('/notifications/contacts', { cache: 'no-store' });
                if (!res.ok) throw new Error('Network error');
                const data = await res.json();
                renderItems(data);
            } catch (err) {
                console.error('Failed to fetch notifications', err);
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
                // load notifications on open
                fetchNotifications();
            }
        });

        // close when clicking outside
        document.addEventListener('click', (e) => {
            if (!document.getElementById('frontdesk-notifications').contains(e.target)) {
                dropdown.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            }
        });

        // poll every 30s for badge count updates
        setInterval(fetchNotifications, 30000);
        // initial fetch for badge
        fetchNotifications();
    })();
