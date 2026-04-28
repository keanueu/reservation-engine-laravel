
document.addEventListener('DOMContentLoaded', function () {
    // Respect reduced motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.querySelectorAll('[data-animate]').forEach(el => {
            el.classList.remove('opacity-0');
            el.classList.add('opacity-100');
        });
        return;
    }

    // On very small screens (iPhone SE and similar) some browsers/webviews
    // don't reliably paint animated elements. Reveal elements immediately
    // to avoid blank sections on these devices.
    if (window.innerWidth && window.innerWidth <= 375) {
        // Small-screen fallback: use JS-driven transition (not CSS keyframe animation)
        // to avoid paint issues while preserving an animated reveal.
        document.querySelectorAll('[data-animate]').forEach(el => {
            // remove will-change hints which can break painting in some webviews
            el.classList.remove('will-change-transform', 'will-change-opacity');

            // start from hidden / translated state
            el.style.opacity = '0';
            el.style.transform = 'translateY(96px)';

            // force style application
            void el.offsetHeight;

            // apply transition rules
            const duration = 1800; // ms - match a slower feel
            el.style.transition = `opacity ${duration}ms cubic-bezier(.22,.98,.36,.99), transform ${duration}ms cubic-bezier(.22,.98,.36,.99)`;

            // trigger the transition on next frame
            requestAnimationFrame(() => {
                el.style.opacity = '1';
                el.style.transform = 'none';
            });

            // cleanup after transition
            const onEnd = () => {
                el.classList.add('animate-done');
                // keep final inline styles to ensure visibility
                el.style.transition = '';
                el.removeEventListener('transitionend', onEnd);
            };

            el.addEventListener('transitionend', onEnd);

            // safety fallback
            setTimeout(() => {
                if (!el.classList.contains('animate-done')) onEnd();
            }, duration + 200);
        });
        return;
    }

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return; // only act on enter
            const el = entry.target;
            // reveal immediately
            el.classList.remove('opacity-0');
            el.classList.add('opacity-100');

            // add Tailwind animation class defined in tailwind.config.js
            // use the larger/slower variant for a bigger, slower fade-up
            el.classList.add('animate-fade-up-lg-slow');

            // Once animation ends, fix final styles so element doesn't revert
            const onEnd = () => {
                // ensure final visible state is persistent
                el.style.opacity = '1';
                el.style.transform = 'none';
                // mark as done to avoid re-applying animations
                el.classList.add('animate-done');
                // remove animation class to prevent replays
                el.classList.remove('animate-fade-up-lg-slow');
                // remove will-change hints (can cause paint issues on some mobile browsers)
                el.classList.remove('will-change-transform', 'will-change-opacity');
                // force a reflow to ensure the browser paints the final state
                void el.offsetHeight;
                el.removeEventListener('animationend', onEnd);
            };

            el.addEventListener('animationend', onEnd);

            // Fallback: if animationend doesn't fire (some mobile browsers), finalize after duration
            setTimeout(() => {
                if (!el.classList.contains('animate-done')) onEnd();
            }, 2300);

            // stop observing so animation runs once and entry won't be toggled
            obs.unobserve(el);
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('[data-animate]').forEach(el => observer.observe(el));
});

