function getTextColor() {
    return document.documentElement.classList.contains('dark') ? '#ffffff' : '#1f2937';
}
function getGridColor() {
    return document.documentElement.classList.contains('dark') ? 'rgba(255,255,255,0.1)' : '#e5e7eb';
}

function createBookingsChart() {
    const ctxElement = document.getElementById('bookingsChart');
    const ctx = ctxElement?.getContext && ctxElement.getContext('2d');
    if (!ctx) return null;

    // If Chart.js isn't loaded yet, retry shortly (handles race with CDN or other loaders)
    if (typeof window.Chart === 'undefined') {
        setTimeout(() => createBookingsChart(), 150);
        return null;
    }

    // destroy existing instance if present
    if (window.bookingsChart && typeof window.bookingsChart.destroy === 'function') {
        try { window.bookingsChart.destroy(); } catch (e) { /* ignore */ }
    }

    // create and expose
    window.bookingsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: window.dashboardLabels || [],
            datasets: [
                {
                    label: 'Room Bookings',
                    data: window.roomChartData || [],
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)'
                },
                {
                    label: 'Boat Bookings',
                    data: window.boatChartData || [],
                    borderColor: 'rgba(16, 185, 129, 1)',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(16, 185, 129, 1)'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: getTextColor(),
                        font: { weight: '500' }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: document.documentElement.classList.contains('dark') ? '#374151' : '#f9fafb',
                    titleColor: getTextColor(),
                    bodyColor: getTextColor(),
                    titleFont: { weight: 'bold', size: 13 },
                    bodyFont: { size: 13 },
                    borderColor: document.documentElement.classList.contains('dark') ? '#4b5563' : '#d1d5db',
                    borderWidth: 1,
                }
            },
            scales: {
                x: {
                    ticks: { color: getTextColor(), font: { weight: '500' } },
                    grid: { color: getGridColor(), drawBorder: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: getTextColor(), stepSize: 1 },
                    grid: { color: getGridColor(), drawBorder: false }
                }
            }
        }
    });

    return window.bookingsChart;
}

// expose for external callers (inline scripts, dark mode toggle)
window.createBookingsChart = createBookingsChart;

document.addEventListener('DOMContentLoaded', () => {
    // Try to create immediately (handles normal loads)
    createBookingsChart();

    // If the bookings canvas is added later (e.g., Livewire) observe body and recreate when it appears
    const bodyObserver = new MutationObserver((mutations, obs) => {
        if (document.getElementById('bookingsChart')) {
            createBookingsChart();
            obs.disconnect();
        }
    });
    bodyObserver.observe(document.body, { childList: true, subtree: true });

    // Watch for dark mode changes to update colors without recreating chart
    const classObserver = new MutationObserver(() => {
        const ch = window.bookingsChart;
        if (!ch) return;
        ch.options.plugins.legend.labels.color = getTextColor();
        ch.options.plugins.tooltip.titleColor = getTextColor();
        ch.options.plugins.tooltip.bodyColor = getTextColor();
        ch.options.plugins.tooltip.backgroundColor = document.documentElement.classList.contains('dark') ? '#374151' : '#f9fafb';
        ch.options.plugins.tooltip.borderColor = document.documentElement.classList.contains('dark') ? '#4b5563' : '#d1d5db';
        ch.options.scales.x.ticks.color = getTextColor();
        ch.options.scales.y.ticks.color = getTextColor();
        ch.options.scales.x.grid.color = getGridColor();
        ch.options.scales.y.grid.color = getGridColor();
        ch.update();
    });

    classObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});
