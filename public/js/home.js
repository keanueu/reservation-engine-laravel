document.addEventListener("DOMContentLoaded", function () {
    // ---------- DATE INPUTS ----------
    const startInput = document.getElementById("startDate");
    const endInput = document.getElementById("endDate");

    if (startInput && endInput) {
        const today = new Date();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const year = today.getFullYear();
        const minDate = `${year}-${month}-${day}`;

        startInput.setAttribute("min", minDate);
        endInput.setAttribute("min", minDate);

        startInput.addEventListener("change", function () {
            const checkin = startInput.value;
            endInput.value = "";
            endInput.setAttribute("min", checkin);
        });
    }

    // ---------- ROOM NAVIGATION ----------
    window.goToRooms = function () {
        const start = startInput ? startInput.value : "";
        const end = endInput ? endInput.value : "";

        if (!start || !end) {
            alert("Please select both check-in and check-out dates.");
            return;
        }

        window.location.href = `{{ url('home/rooms') }}?startDate=${start}&endDate=${end}`;
    };

    // ---------- GUEST LIMIT MODAL ----------
    const adults = document.getElementById("adults");
    const children = document.getElementById("children");
    const form = adults ? adults.closest("form") : null;
    const maxGuests = 13;

    const modal = document.getElementById("guestLimitModal");
    const modalContent = document.getElementById("guestLimitContent");
    const closeModalBtn = document.getElementById("closeModalBtn");

    function showModal() {
        if (!modal || !modalContent) return;
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        setTimeout(() => {
            modalContent.classList.remove("opacity-0", "scale-95");
            modalContent.classList.add("opacity-100", "scale-100");
        }, 10);
    }

    function hideModal() {
        if (!modal || !modalContent) return;
        modalContent.classList.remove("opacity-100", "scale-100");
        modalContent.classList.add("opacity-0", "scale-95");
        setTimeout(() => {
            modal.classList.remove("flex");
            modal.classList.add("hidden");
        }, 300);
    }

    if (closeModalBtn) closeModalBtn.addEventListener("click", hideModal);

    function checkGuests(e) {
        const total = parseInt(adults?.value || 0) + parseInt(children?.value || 0);
        if (total > maxGuests) {
            e.preventDefault();
            showModal();
            return false;
        }
        return true;
    }

    if (form) form.addEventListener("submit", checkGuests);

    // ---------- PAGE LOADER ----------
    const loader = document.getElementById("cabanas-loader");
    const main = document.getElementById("main-content");

    if (main) main.classList.remove('opacity-0');

    setTimeout(() => {
        if (loader) {
            loader.classList.add('opacity-0');
            loader.style.pointerEvents = 'none';
            setTimeout(() => loader.classList.add('hidden'), 500);
        }
    }, 800);

    // ---------- AMENITIES DRAWER ----------
    const amenitiesBtn = document.getElementById('amenitiesBtn');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const closeDrawerBtn = document.getElementById('closeDrawerBtn');
    const drawerOverlay = document.getElementById('drawerOverlay');
    const totalPrice = document.getElementById('totalPrice');

    let total = 0;
    const amenityButtons = mobileDrawer ? mobileDrawer.querySelectorAll('button.bg-green-600') : [];

    const toggleDrawer = (open) => {
        if (!mobileDrawer || !drawerOverlay) return;
        if (open) {
            mobileDrawer.classList.remove('translate-x-full');
            mobileDrawer.classList.add('translate-x-0');
            drawerOverlay.classList.remove('hidden');
            setTimeout(() => drawerOverlay.classList.add('opacity-100'), 10);
            document.body.classList.add('overflow-hidden');
        } else {
            mobileDrawer.classList.remove('translate-x-0');
            mobileDrawer.classList.add('translate-x-full');
            drawerOverlay.classList.remove('opacity-100');
            setTimeout(() => drawerOverlay.classList.add('hidden'), 500);
            document.body.classList.remove('overflow-hidden');
        }
    };

    if (amenitiesBtn) amenitiesBtn.addEventListener('click', () => toggleDrawer(true));
    if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', () => toggleDrawer(false));
    if (drawerOverlay) drawerOverlay.addEventListener('click', () => toggleDrawer(false));

    amenityButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const match = btn.textContent.match(/\₱?(\d+)/);
            if (match) {
                total += parseInt(match[1]);
                if (totalPrice) totalPrice.textContent = `₱${total.toLocaleString()}`;
                btn.classList.replace('bg-green-600', 'bg-gray-400');
                btn.textContent = 'Added ✓';
                btn.disabled = true;
            }
        });
    });

    // ---------- POLICY MODALS ----------
    document.querySelectorAll('[data-overlay]').forEach(button => {
        const modalId = button.getAttribute('data-overlay');
        const modal = document.querySelector(modalId);
        if (!modal) return;

        button.addEventListener('click', () => {
            modal.classList.remove('hidden');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            document.body.style.overflow = 'hidden';
        });

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('opacity-100', 'pointer-events-auto');
            document.body.style.overflow = '';
        };

        modal.querySelectorAll('[data-overlay]').forEach(closeBtn => closeBtn.addEventListener('click', closeModal));

        modal.addEventListener('click', e => {
            if (e.target === modal) closeModal();
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
        });
    });

    // ---------- ROOM AVAILABILITY ----------
    const cards = Array.from(document.querySelectorAll('.room-card'));
    if (!cards.length) return;

    const startTimeEl = document.getElementById('room_checkin_time');
    const endTimeEl = document.getElementById('room_checkout_time');
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let abortControllers = [];
    let index = 0;
    const concurrency = 8;

    function makeBadge(text, color) {
        const banner = document.createElement('div');
        banner.className = 'room-badge';
        banner.style.backgroundColor = color;
        const textSpan = document.createElement('span');
        textSpan.innerText = text;
        banner.appendChild(textSpan);
        return banner;
    }

    function clearBadges() {
        cards.forEach(card => {
            const container = card.querySelector('.room-image-container');
            container?.querySelector('.room-badge')?.remove();
            const btn = card.querySelector('.book-now-btn');
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('opacity-70', 'cursor-not-allowed', 'bg-gray-400');
                btn.classList.add('bg-orange-600');
            }
        });
    }

    function inputsReady() {
        return Boolean(
            startInput?.value &&
            endInput?.value &&
            startTimeEl?.value &&
            endTimeEl?.value &&
            adults?.value !== null &&
            children?.value !== null
        );
    }

    function showAvailabilityLoader() {
        const el = document.getElementById('cabanas-loader');
        if (!el) return;
        el.classList.remove('hidden', 'opacity-0');
        el.style.pointerEvents = 'auto';
    }

    function hideAvailabilityLoader() {
        const el = document.getElementById('cabanas-loader');
        if (!el) return;
        el.classList.add('opacity-0');
        el.style.pointerEvents = 'none';
        setTimeout(() => el.classList.add('hidden'), 400);
    }

    function workerRun(startDateVal, endDateVal) {
        if (index >= cards.length) return Promise.resolve();
        const card = cards[index++];
        const roomId = card.getAttribute('data-room-id') || card.querySelector('.book-now-btn')?.dataset.roomId;
        if (!roomId) return workerRun(startDateVal, endDateVal);

        const controller = new AbortController();
        abortControllers.push(controller);

        return fetch('/check-room-availability', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                room_id: roomId,
                start_date: startDateVal,
                end_date: endDateVal,
                start_time: startTimeEl?.value,
                end_time: endTimeEl?.value
            }),
            signal: controller.signal
        })
            .then(r => r.json())
            .then(data => {
                const btn = card.querySelector('.book-now-btn');
                const container = card.querySelector('.room-image-container');
                if (!container) return;
                container.querySelector('.room-badge')?.remove();
                if (data?.available) {
                    container.prepend(makeBadge('Available', '#16a34a'));
                    if (btn) {
                        btn.disabled = false;
                        btn.classList.remove('opacity-70', 'cursor-not-allowed', 'bg-gray-400');
                        btn.classList.add('bg-orange-600');
                    }
                } else {
                    container.prepend(makeBadge('Fully booked', '#dc2626'));
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.add('opacity-70', 'cursor-not-allowed');
                        btn.classList.remove('bg-orange-600');
                        btn.classList.add('bg-gray-400');
                    }
                }
            })
            .catch(err => {
                if (err.name === 'AbortError') return;
                const container = card.querySelector('.room-image-container');
                container?.querySelector('.room-badge')?.remove();
            })
            .then(() => workerRun(startDateVal, endDateVal));
    }

    function startChecks(startDateVal, endDateVal) {
        abortControllers.forEach(c => c.abort());
        abortControllers = [];
        index = 0;
        const workers = Array(concurrency).fill(null).map(() => workerRun(startDateVal, endDateVal));
        return Promise.all(workers);
    }

    if (inputsReady()) {
        showAvailabilityLoader();
        startChecks(startInput.value, endInput.value).then(() => hideAvailabilityLoader()).catch(() => hideAvailabilityLoader());
    } else {
        clearBadges();
        hideAvailabilityLoader();
    }

    // Debounced re-checks on input changes
    let debounceTimer;
    const triggerInputs = [startInput, endInput, startTimeEl, endTimeEl, adults, children];
    triggerInputs.forEach(inp => {
        if (!inp) return;
        inp.addEventListener('change', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                if (inputsReady()) {
                    clearBadges();
                    showAvailabilityLoader();
                    startChecks(startInput.value, endInput.value).then(() => hideAvailabilityLoader()).catch(() => hideAvailabilityLoader());
                } else {
                    abortControllers.forEach(c => c.abort());
                    abortControllers = [];
                    clearBadges();
                    hideAvailabilityLoader();
                }
            }, 300);
        });
    });
});
