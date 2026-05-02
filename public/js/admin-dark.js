
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
        document.body.classList.toggle('overflow-hidden');
    }

    if (sidebarToggle && sidebar) sidebarToggle.addEventListener('click', toggleSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', () => {
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
        if (window.innerWidth >= 1024 && sidebar && sidebar.classList.contains('translate-x-0')) {
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    });

    // --- Dark Mode Icons ---
    function updateThemeIcons(isDark) {
        if (themeIconLight && themeIconDark) {
            themeIconLight.classList.toggle('hidden', isDark);
            themeIconDark.classList.toggle('hidden', !isDark);
        }
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

    // --- Dark Mode Toggle ---
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            const isDark = html.classList.contains('dark');
            localStorage.theme = isDark ? 'dark' : 'light';
            updateThemeIcons(isDark);

            // Refresh charts if they exist
            if (typeof bookingsChart !== 'undefined' && bookingsChart) {
                bookingsChart.destroy();
                if (typeof createBookingsChart === 'function') {
                    createBookingsChart();
                }
            }
        });
    }
});

