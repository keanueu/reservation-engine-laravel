
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-open-refund]').forEach(btn => {
        const id = btn.getAttribute('data-open-refund');
        btn.addEventListener('click', function () {
            const modal = document.getElementById('refund-modal-' + id);
            if (modal) modal.classList.remove('hidden');
        });
    });
    document.querySelectorAll('[data-close-refund]').forEach(btn => {
        const id = btn.getAttribute('data-close-refund');
        btn.addEventListener('click', function () {
            const modal = document.getElementById('refund-modal-' + id);
            if (modal) modal.classList.add('hidden');
        });
    });
});
