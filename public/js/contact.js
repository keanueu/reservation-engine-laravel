
document.addEventListener('DOMContentLoaded', function () {
    // Respect reduced motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.querySelectorAll('[data-animate]').forEach(el => {
            el.classList.remove('opacity-0');
            el.classList.add('opacity-100');
        });
        return;
    }

    // Small-screen fallback for devices like iPhone SE
    if (window.innerWidth && window.innerWidth <= 375) {
        document.querySelectorAll('[data-animate]').forEach(el => {
            el.classList.remove('will-change-transform', 'will-change-opacity');
            el.style.opacity = '0';
            el.style.transform = 'translateY(96px)';
            void el.offsetHeight;
            const duration = 1800;
            el.style.transition = `opacity ${duration}ms cubic-bezier(.22,.98,.36,.99), transform ${duration}ms cubic-bezier(.22,.98,.36,.99)`;
            requestAnimationFrame(() => {
                el.style.opacity = '1';
                el.style.transform = 'none';
            });
            const onEnd = () => {
                el.classList.add('animate-done');
                el.style.transition = '';
                el.removeEventListener('transitionend', onEnd);
            };
            el.addEventListener('transitionend', onEnd);
            setTimeout(() => { if (!el.classList.contains('animate-done')) onEnd(); }, duration + 200);
        });
        return;
    }

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            el.classList.remove('opacity-0');
            el.classList.add('opacity-100');
            el.classList.add('animate-fade-up-lg-slow');
            const onEnd = () => {
                el.style.opacity = '1';
                el.style.transform = 'none';
                el.classList.add('animate-done');
                el.classList.remove('animate-fade-up-lg-slow');
                el.classList.remove('will-change-transform', 'will-change-opacity');
                void el.offsetHeight;
                el.removeEventListener('animationend', onEnd);
            };
            el.addEventListener('animationend', onEnd);
            setTimeout(() => { if (!el.classList.contains('animate-done')) onEnd(); }, 2300);
            obs.unobserve(el);
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('[data-animate]').forEach(el => observer.observe(el));
});

