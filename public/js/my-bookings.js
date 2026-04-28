(function () {
    const modal       = document.getElementById('my-bookings-modal');
    const content     = document.getElementById('my-bookings-content');
    const openButtons = document.querySelectorAll('[data-open-mybookings]');
    const closeButtons = modal ? modal.querySelectorAll('[data-close-mybookings]') : [];

    if (!modal) return;

    /* ── Floater management ── */
    function setFloatersDisabled(disabled) {
        const selectors = ['#chatbot', '#alerts-drawer', '.floating-notification', '.floater', '.floating'];
        document.querySelectorAll(selectors.join(',')).forEach(el => {
            if (!el) return;
            if (disabled) {
                el.dataset._prevPointer = el.style.pointerEvents || '';
                el.dataset._prevOpacity = el.style.opacity || '';
                el.dataset._wasHidden   = el.classList.contains('hidden') ? '1' : '0';
                el.setAttribute('aria-hidden', 'true');
                el.style.pointerEvents  = 'none';
                el.style.opacity        = '0.45';
                if (!el.classList.contains('hidden')) el.classList.add('hidden');
            } else {
                el.removeAttribute('aria-hidden');
                el.style.pointerEvents = el.dataset._prevPointer || '';
                el.style.opacity       = el.dataset._prevOpacity || '';
                try { if (el.dataset._wasHidden === '0') el.classList.remove('hidden'); } catch(e){}
                delete el.dataset._prevPointer;
                delete el.dataset._prevOpacity;
                delete el.dataset._wasHidden;
            }
        });
    }

    /* ── Helpers ── */
    function statusBadge(status) {
        const s = (status || '').toLowerCase();
        let cls = 'background:#f3f4f6;color:#374151;';
        if (s === 'approve' || s === 'approved')                cls = 'background:#dcfce7;color:#15803d;';
        else if (s === 'checked-in')                            cls = 'background:#dbeafe;color:#1d4ed8;';
        else if (s === 'checked-out')                           cls = 'background:#e0e7ff;color:#4338ca;';
        else if (s === 'waiting' || s === 'pending')            cls = 'background:#fef9c3;color:#a16207;';
        else if (s === 'rejected' || s === 'cancelled')         cls = 'background:#fee2e2;color:#b91c1c;';
        const label = status ? status.replace(/-/g,' ').replace(/\b\w/g, c => c.toUpperCase()) : 'N/A';
        return `<span style="display:inline-block;padding:2px 10px;font-size:.68rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;${cls}">${label}</span>`;
    }

    function paymentBadge(status) {
        const s = (status || '').toLowerCase();
        let cls = 'background:#f3f4f6;color:#374151;';
        if (s === 'paid')    cls = 'background:#dcfce7;color:#15803d;';
        else if (s === 'pending') cls = 'background:#fef9c3;color:#a16207;';
        else if (s === 'failed')  cls = 'background:#fee2e2;color:#b91c1c;';
        const label = status ? status.replace(/\b\w/g, c => c.toUpperCase()) : 'Pending';
        return `<span style="display:inline-block;padding:2px 10px;font-size:.68rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;${cls}">${label}</span>`;
    }

    function fmt(dtStr) {
        if (!dtStr) return '—';
        try {
            let s = dtStr;
            if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/.test(s)) s = s.replace(' ', 'T');
            const d = new Date(s);
            if (isNaN(d.getTime())) return dtStr;
            return d.toLocaleString(undefined, { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' });
        } catch(e) { return dtStr; }
    }

    function fmtDate(dtStr) {
        if (!dtStr) return '—';
        try {
            let s = dtStr;
            if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/.test(s)) s = s.replace(' ', 'T');
            const d = new Date(s);
            if (isNaN(d.getTime())) return dtStr;
            return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
        } catch(e) { return dtStr; }
    }

    function php(n) { return 'PHP ' + Number(n || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 }); }

    function row(label, value) {
        return `<div style="display:flex;justify-content:space-between;align-items:flex-start;padding:8px 0;border-bottom:1px solid #f3f4f6;">
            <span style="font-size:.75rem;color:#6b7280;font-weight:600;">${label}</span>
            <span style="font-size:.75rem;color:#111827;font-weight:700;text-align:right;max-width:60%;">${value}</span>
        </div>`;
    }

    /* ── Build one booking card ── */
    function buildCard(b) {
        const card = document.createElement('div');
        card.style.cssText = 'background:#fff;border:1px solid #e5e7eb;margin-bottom:16px;overflow:hidden;';

        /* ── 1. Card header ── */
        const checkinDate  = fmtDate(b.scheduled_checkin_at  || b.start_date);
        const checkoutDate = fmtDate(b.scheduled_checkout_at || b.end_date);
        const nights       = b.nights || '—';

        card.innerHTML = `
        <!-- Header -->
        <div style="background:#964B00;padding:14px 16px;display:flex;justify-content:space-between;align-items:flex-start;">
            <div>
                <p style="font-size:.65rem;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:rgba(255,255,255,.65);margin-bottom:3px;">Booking #${b.id}</p>
                <p style="font-size:.95rem;font-weight:800;color:#fff;line-height:1.3;">${b.room_name || 'Room'}</p>
            </div>
            <div style="text-align:right;">
                ${statusBadge(b.status)}
                <div style="margin-top:4px;">${paymentBadge(b.payment_status)}</div>
            </div>
        </div>

        <!-- Date strip -->
        <div style="display:grid;grid-template-columns:1fr auto 1fr;align-items:center;padding:12px 16px;background:#faf9f7;border-bottom:1px solid #e5e7eb;">
            <div>
                <p style="font-size:.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#9ca3af;">Check-in</p>
                <p style="font-size:.8rem;font-weight:700;color:#111827;margin-top:2px;">${checkinDate}</p>
                <p style="font-size:.7rem;color:#6b7280;">${b.scheduled_checkin_at ? fmt(b.scheduled_checkin_at).split(',').slice(-1)[0].trim() : '—'}</p>
            </div>
            <div style="text-align:center;padding:0 12px;">
                <p style="font-size:.65rem;font-weight:700;color:#964B00;">${nights} night${nights > 1 ? 's' : ''}</p>
                <div style="height:1px;background:#d1d5db;margin:4px 0;"></div>
                <svg style="width:16px;height:16px;color:#9ca3af;margin:0 auto;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </div>
            <div style="text-align:right;">
                <p style="font-size:.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#9ca3af;">Check-out</p>
                <p style="font-size:.8rem;font-weight:700;color:#111827;margin-top:2px;">${checkoutDate}</p>
                <p style="font-size:.7rem;color:#6b7280;">${b.scheduled_checkout_at ? fmt(b.scheduled_checkout_at).split(',').slice(-1)[0].trim() : '—'}</p>
            </div>
        </div>

        <!-- Pricing -->
        <div style="padding:12px 16px;border-bottom:1px solid #e5e7eb;">
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#9ca3af;margin-bottom:8px;">Pricing</p>
            ${row('Total Amount', `<span style="color:#964B00;font-weight:800;">${php(b.total_amount)}</span>`)}
            ${row('Deposit Paid', php(b.deposit_amount))}
            ${b.paid_amount ? row('Amount Paid', `<span style="color:#15803d;">${php(b.paid_amount)}</span>`) : ''}
        </div>

        <!-- Refund status (read-only, shown if exists) -->
        ${b.refund_status ? buildRefundStatus(b) : ''}

        <!-- Extensions list (read-only) -->
        ${b.extensions && b.extensions.length ? buildExtensionsList(b.extensions) : ''}

        <!-- Action buttons -->
        <div style="padding:12px 16px;display:flex;gap:8px;background:#faf9f7;">
            <button data-open-extension data-booking-id="${b.id}"
                style="flex:1;padding:9px 0;font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;background:#111827;color:#fff;border:none;cursor:pointer;transition:background .2s;"
                onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                + Request Extension
            </button>
            <button data-open-refund data-booking-id="${b.id}"
                style="flex:1;padding:9px 0;font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;background:#964B00;color:#fff;border:none;cursor:pointer;transition:background .2s;"
                onmouseover="this.style.background='#6b3500'" onmouseout="this.style.background='#964B00'">
                ↩ Request Refund
            </button>
        </div>

        <!-- Inline extension form (hidden by default) -->
        <div id="extension-form-${b.id}" class="extension-form"
             style="display:none;padding:16px;background:#f0f9ff;border-top:2px solid #111827;">
            <p style="font-size:.7rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#111827;margin-bottom:12px;">Request Extension</p>
            <div style="margin-bottom:10px;">
                <label style="display:block;font-size:.7rem;font-weight:700;color:#374151;margin-bottom:4px;">Additional Hours</label>
                <select name="ext_hours"
                        style="width:100%;border:1px solid #d1d5db;padding:8px 10px;font-size:.8rem;outline:none;box-sizing:border-box;background:#fff;">
                    <option value="1">1 hour</option>
                    <option value="2">2 hours</option>
                    <option value="5">5 hours</option>
                </select>
            </div>
            <div style="margin-bottom:10px;">
                <label style="display:block;font-size:.7rem;font-weight:700;color:#374151;margin-bottom:4px;">Payment Method</label>
                <select name="ext_payment"
                        style="width:100%;border:1px solid #d1d5db;padding:8px 10px;font-size:.8rem;outline:none;box-sizing:border-box;background:#fff;">
                    <option value="online">Pay Online</option>
                    <option value="frontdesk">Pay at Frontdesk</option>
                </select>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button class="ext-cancel"
                        style="padding:7px 16px;font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;background:#f3f4f6;color:#374151;border:none;cursor:pointer;">
                    Cancel
                </button>
                <button class="ext-submit"
                        style="padding:7px 16px;font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;background:#111827;color:#fff;border:none;cursor:pointer;">
                    Submit
                </button>
            </div>
            <div class="ext-feedback" style="display:none;margin-top:8px;font-size:.72rem;font-weight:600;"></div>
        </div>

        <!-- Inline refund form (hidden by default) -->
        <div id="refund-form-${b.id}" class="refund-form"
             data-deposit="${Number(b.deposit_amount||0).toFixed(2)}"
             data-group-deposit="${Number(b.group_deposit_total||0).toFixed(2)}"
             data-group-has-refund="${b.group_has_refund ? '1' : '0'}"
             style="display:none;padding:16px;background:#fff8f5;border-top:2px solid #964B00;">
            <p style="font-size:.7rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#964B00;margin-bottom:12px;">Refund Request</p>
            <div style="margin-bottom:10px;">
                <label style="display:block;font-size:.7rem;font-weight:700;color:#374151;margin-bottom:4px;">Amount <span style="color:#9ca3af;font-weight:400;">(optional — leave blank for full deposit)</span></label>
                <input type="number" step="0.01" name="amount"
                       style="width:100%;border:1px solid #d1d5db;padding:8px 10px;font-size:.8rem;outline:none;box-sizing:border-box;"
                       placeholder="PHP 0.00">
                <p style="font-size:.68rem;color:#6b7280;margin-top:3px;">Max refundable: <strong>${php(b.group_deposit_total)}</strong></p>
            </div>
            ${b.include_refund_fee_in_form ? `
            <div style="margin-bottom:10px;padding:8px 10px;background:#fef9c3;border:1px solid #fde047;">
                <p style="font-size:.7rem;color:#a16207;font-weight:700;">A ${Number(b.refund_fee_percentage||0).toFixed(0)}% refund fee will be applied.</p>
                <label style="display:flex;align-items:center;gap:6px;margin-top:6px;font-size:.7rem;color:#374151;cursor:pointer;">
                    <input type="checkbox" name="ack_refund_fee" style="width:14px;height:14px;">
                    I acknowledge the refund fee
                </label>
            </div>` : ''}
            <div style="margin-bottom:10px;">
                <label style="display:block;font-size:.7rem;font-weight:700;color:#374151;margin-bottom:4px;">Reason <span style="color:#9ca3af;font-weight:400;">(optional)</span></label>
                <textarea name="reason" rows="2"
                          style="width:100%;border:1px solid #d1d5db;padding:8px 10px;font-size:.8rem;outline:none;resize:none;box-sizing:border-box;"
                          placeholder="e.g. Change of plans, emergency..."></textarea>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button class="refund-cancel"
                        style="padding:7px 16px;font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;background:#f3f4f6;color:#374151;border:none;cursor:pointer;">
                    Cancel
                </button>
                <button class="refund-submit"
                        style="padding:7px 16px;font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;background:#964B00;color:#fff;border:none;cursor:pointer;">
                    Submit Request
                </button>
            </div>
            <div class="refund-feedback" style="display:none;margin-top:8px;font-size:.72rem;font-weight:600;"></div>
        </div>
        `;

        return card;
    }

    function buildRefundStatus(b) {
        const rs = (b.refund_status || '').toLowerCase();
        let icon = '', color = '', title = '', detail = '';

        if (rs === 'refunded') {
            icon  = '✓';
            color = '#15803d';
            title = 'Refund Processed';
            detail = `${php(b.refund_amount)} refunded${b.refund_fee ? ` (fee: ${php(b.refund_fee)})` : ''}`;
        } else if (rs === 'requested') {
            icon  = '⏳';
            color = '#a16207';
            title = 'Refund Requested';
            detail = `${php(b.refund_requested_amount)} — awaiting approval`;
        } else if (rs === 'rejected') {
            icon  = '✕';
            color = '#b91c1c';
            title = 'Refund Rejected';
            detail = 'Your refund request was not approved.';
        } else { return ''; }

        return `
        <div style="padding:10px 16px;border-bottom:1px solid #e5e7eb;display:flex;align-items:flex-start;gap:10px;background:#fafafa;">
            <span style="font-size:1rem;color:${color};flex-shrink:0;">${icon}</span>
            <div>
                <p style="font-size:.72rem;font-weight:800;color:${color};">${title}</p>
                <p style="font-size:.7rem;color:#6b7280;margin-top:2px;">${detail}</p>
            </div>
        </div>`;
    }

    function buildExtensionsList(extensions) {
        const items = extensions.map(e => {
            const s = (e.status || '').toLowerCase();
            let badgeStyle = 'background:#f3f4f6;color:#374151;';
            if (s === 'approved' || s === 'paid') badgeStyle = 'background:#dcfce7;color:#15803d;';
            else if (s === 'pending' || s === 'pending_payment') badgeStyle = 'background:#fef9c3;color:#a16207;';
            else if (s === 'rejected') badgeStyle = 'background:#fee2e2;color:#b91c1c;';
            const label = (e.status || 'Pending').replace(/_/g,' ').replace(/\b\w/g, c => c.toUpperCase());
            const checkBtn = s === 'pending_payment'
                ? `<button data-ext-id="${e.id}" class="my-ext-refresh"
                       style="padding:3px 8px;font-size:.65rem;font-weight:700;background:#964B00;color:#fff;border:none;cursor:pointer;margin-left:6px;">
                       Check
                   </button>` : '';
            return `
            <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #f3f4f6;">
                <div>
                    <span style="font-size:.72rem;font-weight:700;color:#374151;">${e.hours}h Extension</span>
                    ${e.price ? `<span style="font-size:.68rem;color:#6b7280;margin-left:6px;">${php(e.price)}</span>` : ''}
                </div>
                <div style="display:flex;align-items:center;">
                    <span style="font-size:.65rem;font-weight:700;padding:2px 8px;${badgeStyle}">${label}</span>
                    ${checkBtn}
                </div>
            </div>`;
        }).join('');

        return `
        <div style="padding:10px 16px;border-bottom:1px solid #e5e7eb;">
            <p style="font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#9ca3af;margin-bottom:6px;">
                Extensions (${extensions.length})
            </p>
            ${items}
        </div>`;
    }

    /* ── Load & render ── */
    async function loadBookings() {
        content.innerHTML = `
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 0;text-align:center;">
            <svg style="width:32px;height:32px;color:#964B00;animation:spin 1s linear infinite;margin-bottom:12px;" fill="none" viewBox="0 0 24 24">
                <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
            </svg>
            <p style="font-size:.8rem;font-weight:600;color:#6b7280;">Loading your bookings…</p>
        </div>
        <style>@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}</style>`;

        try {
            const res  = await fetch('/api/my-bookings', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error('Failed');
            const json = await res.json();
            const bookings = json.data || [];

            if (!bookings.length) {
                content.innerHTML = `
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 16px;text-align:center;">
                    <svg style="width:48px;height:48px;color:#d1d5db;margin-bottom:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p style="font-size:.95rem;font-weight:700;color:#374151;margin-bottom:6px;">No bookings yet</p>
                    <p style="font-size:.78rem;color:#9ca3af;">Start planning your stay at Cabanas!</p>
                </div>`;
                return;
            }

            content.innerHTML = '';
            bookings.forEach(b => content.appendChild(buildCard(b)));
            attachListeners();

        } catch(err) {
            console.error(err);
            content.innerHTML = `
            <div style="padding:24px;background:#fef2f2;border:1px solid #fecaca;margin:16px;">
                <p style="font-size:.78rem;font-weight:700;color:#b91c1c;">Failed to load bookings.</p>
                <p style="font-size:.72rem;color:#ef4444;margin-top:4px;">Please check your connection and try again.</p>
            </div>`;
        }
    }

    /* ── Event listeners on dynamic cards ── */
    function attachListeners() {

        /* Extension toggle (collapsible inline form) */
        content.querySelectorAll('[data-open-extension]').forEach(btn => {
            btn.addEventListener('click', () => {
                const id   = btn.getAttribute('data-booking-id');
                const form = document.getElementById('extension-form-' + id);
                if (!form) return;
                const isOpen = form.style.display !== 'none';
                // close refund form if open
                const refundForm = document.getElementById('refund-form-' + id);
                if (refundForm) refundForm.style.display = 'none';
                form.style.display = isOpen ? 'none' : 'block';
            });
        });

        /* Extension form cancel / submit */
        content.querySelectorAll('.extension-form').forEach(formWrap => {
            const cancelBtn  = formWrap.querySelector('.ext-cancel');
            const submitBtn  = formWrap.querySelector('.ext-submit');
            const feedbackEl = formWrap.querySelector('.ext-feedback');

            if (cancelBtn) cancelBtn.addEventListener('click', () => {
                formWrap.style.display = 'none';
                if (feedbackEl) { feedbackEl.style.display = 'none'; feedbackEl.textContent = ''; }
            });

            if (submitBtn) submitBtn.addEventListener('click', async () => {
                const bookingId = formWrap.id.replace('extension-form-', '');
                const hours     = formWrap.querySelector('select[name=ext_hours]').value;
                const payment   = formWrap.querySelector('select[name=ext_payment]').value;

                const showErr = msg => {
                    if (!feedbackEl) return;
                    feedbackEl.style.display = 'block';
                    feedbackEl.style.color   = '#b91c1c';
                    feedbackEl.textContent   = msg;
                };

                if (!hours)   { showErr('Please select hours.'); return; }
                if (!payment) { showErr('Please select a payment method.'); return; }

                if (feedbackEl) feedbackEl.style.display = 'none';
                submitBtn.disabled    = true;
                submitBtn.textContent = 'Submitting…';

                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res  = await fetch('/bookings/' + bookingId + '/extension', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({ hours: parseInt(hours), payment_method: payment })
                    });
                    const ct      = (res.headers.get('content-type') || '').toLowerCase();
                    const payload = ct.includes('application/json') ? await res.json() : { message: await res.text() };

                    if (!res.ok) throw new Error(payload.message || 'Request failed');

                    // If online payment, redirect to checkout URL
                    if (payload.checkout_url) {
                        window.location.href = payload.checkout_url;
                        return;
                    }

                    if (feedbackEl) {
                        feedbackEl.style.display = 'block';
                        feedbackEl.style.color   = '#15803d';
                        feedbackEl.textContent   = payload.message || 'Extension request submitted!';
                    }
                    setTimeout(() => loadBookings(), 1200);
                } catch(err) {
                    showErr(err.message || 'Failed to submit extension.');
                } finally {
                    submitBtn.disabled    = false;
                    submitBtn.textContent = 'Submit';
                }
            });
        });

        /* Refund toggle */
        content.querySelectorAll('[data-open-refund]').forEach(btn => {
            btn.addEventListener('click', () => {
                const id   = btn.getAttribute('data-booking-id');
                const form = document.getElementById('refund-form-' + id);
                if (!form) return;
                const isOpen = form.style.display !== 'none';
                // close extension form if open
                const extForm = document.getElementById('extension-form-' + id);
                if (extForm) extForm.style.display = 'none';
                form.style.display = isOpen ? 'none' : 'block';
            });
        });

        /* Refund form cancel / submit */
        content.querySelectorAll('.refund-form').forEach(formWrap => {
            const cancelBtn   = formWrap.querySelector('.refund-cancel');
            const submitBtn   = formWrap.querySelector('.refund-submit');
            const feedbackEl  = formWrap.querySelector('.refund-feedback');

            if (cancelBtn) cancelBtn.addEventListener('click', () => {
                formWrap.style.display = 'none';
                if (feedbackEl) { feedbackEl.style.display = 'none'; feedbackEl.textContent = ''; }
            });

            if (submitBtn) submitBtn.addEventListener('click', async () => {
                const bookingId  = formWrap.id.replace('refund-form-', '');
                const rawAmount  = formWrap.querySelector('input[name=amount]').value;
                const amount     = rawAmount === '' ? null : parseFloat(rawAmount);
                const ackBox     = formWrap.querySelector('input[name=ack_refund_fee]');
                const reason     = formWrap.querySelector('textarea[name=reason]').value;

                const showErr = msg => {
                    if (!feedbackEl) return;
                    feedbackEl.style.display = 'block';
                    feedbackEl.style.color   = '#b91c1c';
                    feedbackEl.textContent   = msg;
                };

                if (ackBox && !ackBox.checked) { showErr('Please acknowledge the refund fee.'); return; }
                if (formWrap.dataset.groupHasRefund === '1') { showErr('A refund was already requested for this booking group.'); return; }

                const cap = parseFloat(formWrap.dataset.groupDeposit || 0);
                if (amount !== null && !isNaN(amount) && amount > cap) {
                    showErr(`Amount cannot exceed ${php(cap)}.`); return;
                }

                if (feedbackEl) feedbackEl.style.display = 'none';
                submitBtn.disabled    = true;
                submitBtn.textContent = 'Submitting…';

                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res  = await fetch('/bookings/' + bookingId + '/request-refund', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({ amount: amount || null, reason: reason || null })
                    });
                    const ct      = (res.headers.get('content-type') || '').toLowerCase();
                    const payload = ct.includes('application/json') ? await res.json() : { message: await res.text() };
                    if (!res.ok) throw new Error(payload.message || 'Request failed');

                    if (feedbackEl) {
                        feedbackEl.style.display = 'block';
                        feedbackEl.style.color   = '#15803d';
                        feedbackEl.textContent   = payload.message || 'Refund request submitted!';
                    }
                    setTimeout(() => window.location.reload(), 1200);
                } catch(err) {
                    showErr(err.message || 'Failed to submit refund.');
                } finally {
                    submitBtn.disabled    = false;
                    submitBtn.textContent = 'Submit Request';
                }
            });
        });

        /* Extension payment check */
        content.querySelectorAll('.my-ext-refresh').forEach(btn => {
            btn.addEventListener('click', async () => {
                const extId = btn.getAttribute('data-ext-id');
                btn.disabled = true; btn.textContent = '…';
                try {
                    const res  = await fetch('/admin/api/extensions/' + extId + '/refresh');
                    const json = await res.json();
                    if (json.paid) {
                        btn.textContent = 'Paid ✓';
                        btn.style.background = '#15803d';
                        setTimeout(() => loadBookings(), 800);
                    } else {
                        btn.textContent = 'Not paid';
                        setTimeout(() => { btn.disabled = false; btn.textContent = 'Check'; }, 1500);
                    }
                } catch(e) {
                    btn.disabled = false; btn.textContent = 'Check';
                }
            });
        });
    }

    /* ── Modal open / close ── */
    function openModal() {
        setFloatersDisabled(true);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        const panel = document.getElementById('my-bookings-panel');
        if (panel) {
            panel.style.transition = 'none';
            panel.style.transform  = 'translateX(100%)';
            requestAnimationFrame(() => requestAnimationFrame(() => {
                panel.style.transition = 'transform .35s cubic-bezier(.4,0,.2,1)';
                panel.style.transform  = 'translateX(0)';
            }));
        }
    }

    function closeModal() {
        const panel = document.getElementById('my-bookings-panel');
        if (panel) {
            panel.style.transition = 'transform .3s cubic-bezier(.4,0,.2,1)';
            panel.style.transform  = 'translateX(100%)';
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                panel.style.transform = '';
            }, 300);
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        setFloatersDisabled(false);
    }

    openButtons.forEach(b => b.addEventListener('click', async () => {
        openModal();
        await loadBookings();
    }));

    closeButtons.forEach(b => b.addEventListener('click', closeModal));

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

})();
