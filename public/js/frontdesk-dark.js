
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    const darkModeToggle = document.getElementById('darkModeToggle');
    const themeIconLight = document.getElementById('theme-icon-light');
    const themeIconDark = document.getElementById('theme-icon-dark');
    const html = document.documentElement;

    // --- Sidebar Toggle ---
    function toggleSidebar() {
        if (!sidebar) return;
        sidebar.classList.toggle('-translate-x-full');
        sidebar.classList.toggle('translate-x-0');
        if (sidebarOverlay) sidebarOverlay.classList.toggle('hidden');
    }

    if (sidebarToggle && sidebar) sidebarToggle.addEventListener('click', toggleSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', () => {
        // ensure sidebar closes when overlay is clicked
        if (!sidebar.classList.contains('translate-x-0')) return;
        toggleSidebar();
    });

    // Close sidebar on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('translate-x-0')) {
            toggleSidebar();
        }
    });

    // Ensure sidebar is hidden when resizing to large screens
    window.addEventListener('resize', () => {
        try {
            if (window.innerWidth >= 1024 && sidebar && !sidebar.classList.contains('lg:translate-x-0')) {
                // remove mobile-open classes if any
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        } catch (err) {
            // ignore
        }
    });

    // --- Dark Mode Icons ---
    function updateThemeIcons(isDark) {
        themeIconLight.classList.toggle('hidden', isDark);
        themeIconDark.classList.toggle('hidden', !isDark);
    }

    // --- Initial Dark Mode Setup ---
    if (
        localStorage.theme === 'dark' ||
        (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
    ) {
        html.classList.add('dark');
        updateThemeIcons(true);
    } else {
        html.classList.remove('dark');
        updateThemeIcons(false);
    }

    // --- Chart.js Setup ---
    let bookingsChart;
    function createBookingsChart() {
        const ctx = document.getElementById('bookingsChart');
        if (!ctx) return;

        const isDark = html.classList.contains('dark');
        const textColor = isDark ? '#E5E7EB' : '#374151';
        const gridColor = isDark ? 'rgba(75,85,99,0.4)' : 'rgba(229,231,235,0.6)';

        bookingsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Oct 1', 'Oct 5', 'Oct 10', 'Oct 15', 'Oct 20', 'Oct 25', 'Oct 29'],
                datasets: [
                    {
                        label: 'Room Bookings',
                        data: [40, 55, 60, 75, 70, 95, 110],
                        borderColor: '#6366F1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true,
                    },
                    {
                        label: 'Boat Bookings',
                        data: [20, 25, 40, 45, 60, 70, 80],
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                    }
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: textColor, font: { size: 12 } },
                    },
                },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: textColor } },
                    y: { grid: { color: gridColor }, ticks: { color: textColor } },
                },
            },
        });
    }

    createBookingsChart();

    // --- Dark Mode Toggle ---
    darkModeToggle.addEventListener('click', () => {
        html.classList.toggle('dark');
        const isDark = html.classList.contains('dark');
        localStorage.theme = isDark ? 'dark' : 'light';
        updateThemeIcons(isDark);

        bookingsChart.destroy();
        createBookingsChart();
    });
});

