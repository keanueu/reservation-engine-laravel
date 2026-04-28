

(function () {
    const modal = document.getElementById('extension-generic-modal');
    const bookingIdInput = document.getElementById('extension-booking-id');
    const hoursSelect = document.getElementById('extension-hours');
    const paymentSelect = document.getElementById('extension-payment');
    const feedback = document.getElementById('extension-feedback');
    const submitBtn = document.getElementById('extension-submit');
    const cancelBtn = document.getElementById('extension-cancel');

    function openModalForBooking(id) {
        bookingIdInput.value = id;
        feedback.classList.add('hidden'); feedback.textContent = '';
        modal.classList.remove('hidden'); modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden'); modal.classList.remove('flex');
    }

    // Listen for custom event from My Bookings modal
    document.addEventListener('openExtensionModal', (e) => {
        const bookingId = e.detail.bookingId;
        openModalForBooking(bookingId);
    });

    // Also allow direct open-buttons (if any) to use data-open-extension attribute
    document.addEventListener('click', (e) => {
        const el = e.target.closest('[data-open-extension]');
        if (!el) return;
        const id = el.getAttribute('data-booking-id') || el.dataset.bookingId;
        if (id) openModalForBooking(id);
    });

    cancelBtn.addEventListener('click', closeModal);

    submitBtn.addEventListener('click', async () => {
        feedback.classList.add('hidden'); feedback.textContent = '';
        const bookingId = bookingIdInput.value;
        const hours = hoursSelect.value;
        const payment_method = paymentSelect.value;

        try {
            const res = await fetch(`/bookings/${bookingId}/extension`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') },
                body: JSON.stringify({ hours, payment_method })
            });
            const data = await res.json();
            if (!res.ok) throw data;

            // If gateway URL returned, redirect immediately
            if (data.pay_url) {
                window.location.href = data.pay_url;
                return;
            }

            // If extension object returned and contains id, and payment_method is online, redirect to pay wrapper
            if (payment_method === 'online' && data.extension && data.extension.id) {
                window.location.href = `/booking-extensions/${data.extension.id}/pay`;
                return;
            }

            // Otherwise show success and close
            feedback.classList.remove('hidden'); feedback.classList.remove('text-red-600'); feedback.classList.add('text-green-700');
            feedback.textContent = 'Extension request submitted.';
            setTimeout(() => { closeModal(); window.location.reload(); }, 1000);

        } catch (err) {
            feedback.classList.remove('hidden'); feedback.classList.add('text-red-600');
            console.error(err);
            // If server responded with JSON error message
            if (err && err.message) feedback.textContent = err.message; else feedback.textContent = 'Failed to create extension';
        }
    });

    // close on Esc
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
})();

