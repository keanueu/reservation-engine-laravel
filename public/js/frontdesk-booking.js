
(function () {
    // Poll every 12 seconds for pending extensions
    const POLL_INTERVAL = 12000;

    // read deposit percent from server-provided global (injected by Blade). Fallback to 50%.
    const DEPOSIT_PERCENT = (typeof window.DEPOSIT_PERCENT !== 'undefined') ? Number(window.DEPOSIT_PERCENT) : 50;

    async function fetchPending() {
        try {
            const res = await fetch('/admin/api/pending-extensions');
            if (!res.ok) return;
            const payload = await res.json();
            // payload may be { data: [...] } or an array directly
            const data = payload.data || payload;
            // data: array of extensions with id, booking_id, status, hours
            const pendingMap = new Map();
            (data || []).forEach(e => pendingMap.set(String(e.id), e));

            // Update concise indicators (right-most column)
            document.querySelectorAll('.latest-extension-indicator').forEach(el => {
                const extId = el.getAttribute('data-extension-id');
                const hours = el.getAttribute('data-hours') || '';
                const price = parseFloat(el.getAttribute('data-price') || 0);
                // If this extension is no longer in pending list, assume paid and update concise indicator
                if (!pendingMap.has(String(extId))) {
                    const isPaid = el.querySelector('.bg-green-50');
                    if (!isPaid) {
                        el.innerHTML = '<div class="bg-green-50 text-green-800">Extension paid: <strong>' + hours + 'h</strong></div>';
                    }
                }
            });

            // Update extension rows in the expanded details: mark paid, update status text and remove approve buttons
            document.querySelectorAll('.extension-row').forEach(row => {
                const extId = row.getAttribute('data-extension-id');
                if (!pendingMap.has(String(extId))) {
                    // extension no longer pending -> mark as paid if not already
                    const statusEl = row.querySelector('.ext-status');
                    if (statusEl && statusEl.innerText.trim() !== 'paid') {
                        statusEl.innerText = 'paid';
                        // add a subtle green style to status
                        statusEl.classList.add('text-green-800');
                        // Remove approve actions (if any)
                        const actions = row.querySelector('.ext-actions');
                        if (actions) {
                            actions.innerHTML = ''; // remove approve form/button
                        }
                        // Update booking totals only once per extension
                        if (!row.dataset.credited || row.dataset.credited !== 'true') {
                            const price = parseFloat(row.getAttribute('data-price') || 0);
                            const bookingId = row.getAttribute('data-booking-id');
                            if (!isNaN(price) && bookingId) {
                                // Update displayed booking total
                                const totalEl = document.querySelector('.booking-total[data-booking-id="' + bookingId + '"]');
                                const depositEl = document.querySelector('.booking-deposit[data-booking-id="' + bookingId + '"]');
                                const remainingEl = document.querySelector('.booking-remaining[data-booking-id="' + bookingId + '"]');
                                if (totalEl) {
                                    // parse current total (remove commas)
                                    const currentTotal = parseFloat(String(totalEl.innerText).replace(/,/g, '')) || 0;
                                    const newTotal = currentTotal + price;
                                    totalEl.innerText = Number(newTotal).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    // recalc deposit and remaining
                                    const depositAmount = (newTotal * (DEPOSIT_PERCENT / 100));
                                    const remaining = newTotal - depositAmount;
                                    if (depositEl) depositEl.innerText = Number(depositAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    if (remainingEl) remainingEl.innerText = Number(remaining).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                }
                            }
                            row.dataset.credited = 'true';
                        }
                    }
                }
            });

            // attach handlers for manual refresh buttons (frontdesk)
            document.querySelectorAll('.ext-refresh-btn').forEach(btn => {
                btn.removeEventListener('click', window.__extRefreshHandler);
                const handler = async (ev) => {
                    const extId = btn.getAttribute('data-ext-id');
                    if (!extId) return;
                    btn.disabled = true; btn.innerText = 'Checking...';
                    try {
                        const r = await fetch('/admin/api/extensions/' + extId + '/refresh');
                        if (!r.ok) throw new Error('Refresh failed');
                        const json = await r.json();
                        if (json.paid) {
                            // update concise indicator and rows similar to poll
                            const row = document.querySelector('.extension-row[data-extension-id="' + extId + '"]');
                            if (row) {
                                const statusEl = row.querySelector('.ext-status');
                                if (statusEl) { statusEl.innerText = 'paid'; statusEl.classList.add('text-green-800'); }
                                const actions = row.querySelector('.ext-actions'); if (actions) actions.innerHTML = '';
                                // also update totals using the same logic
                                const price = parseFloat(row.getAttribute('data-price') || 0);
                                const bookingId = row.getAttribute('data-booking-id');
                                const totalEl = document.querySelector('.booking-total[data-booking-id="' + bookingId + '"]');
                                const depositEl = document.querySelector('.booking-deposit[data-booking-id="' + bookingId + '"]');
                                const remainingEl = document.querySelector('.booking-remaining[data-booking-id="' + bookingId + '"]');
                                if (totalEl && !row.dataset.credited) {
                                    const currentTotal = parseFloat(String(totalEl.innerText).replace(/,/g, '')) || 0;
                                    const newTotal = currentTotal + price;
                                    totalEl.innerText = Number(newTotal).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    const depositAmount = (newTotal * (DEPOSIT_PERCENT / 100));
                                    const remaining = newTotal - depositAmount;
                                    if (depositEl) depositEl.innerText = Number(depositAmount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    if (remainingEl) remainingEl.innerText = Number(remaining).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    row.dataset.credited = 'true';
                                }
                            }
                            // update concise indicator if present
                            const indicator = document.querySelector('.latest-extension-indicator[data-extension-id="' + extId + '"]');
                            if (indicator) indicator.innerHTML = '<div class="bg-green-50 text-green-800">Extension paid</div>';
                        }
                    } catch (err) {
                        console.error('Refresh failed', err);
                        alert('Failed to refresh payment. Try again.');
                    } finally {
                        btn.disabled = false; btn.innerText = 'Check Payment';
                    }
                };
                window.__extRefreshHandler = handler;
                btn.addEventListener('click', handler);
            });
        } catch (err) {
            // silent fail; will retry on next interval
            console.error('Error fetching pending extensions', err);
        }
    }

    // Start polling when page loaded
    document.addEventListener('DOMContentLoaded', function () {
        // Only poll if there are any indicators rendered
        if (document.querySelectorAll('.latest-extension-indicator').length > 0) {
            fetchPending();
            setInterval(fetchPending, POLL_INTERVAL);
        }
    });
})();

