
(function () {
    const endpoint = window.adminRecentBookingsEndpoint || '/admin/api/recent-bookings';

    function renderStatusBadge(status) {
        if (!status) return '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Waiting</span>';
        status = status.toLowerCase();
        if (status === 'approve' || status === 'approved') {
            return '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approved</span>';
        }
        if (status === 'rejected') {
            return '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>';
        }
        if (status === 'checked-in' || status === 'checkedin') {
            return '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">Checked-in</span>';
        }
        if (status === 'checked-out' || status === 'checkedout') {
            return '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Checked-out</span>';
        }
        // default
        return '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Waiting</span>';
    }

    function refreshStatuses() {
        if (document.hidden) return;

        const rows = document.querySelectorAll('[id^="booking-"]');
        if (!rows.length) return;

        fetch(endpoint, { credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                if (!data || !data.bookings) return;
                data.bookings.forEach(b => {
                    const row = document.getElementById('booking-' + b.id);
                    if (!row) return;
                    // status cell is the 5th td (0-based index 4)
                    const tds = row.querySelectorAll('td');
                    if (tds && tds.length >= 5) {
                        tds[4].innerHTML = renderStatusBadge(b.status);
                    }
                });
            })
            .catch(() => {
                // silent
            });
    }

    // initial refresh then poll every 30 seconds while the tab is visible
    document.addEventListener('DOMContentLoaded', function () {
        refreshStatuses();
        setInterval(refreshStatuses, 30000);
    });
})();
