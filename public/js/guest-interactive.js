document.addEventListener('DOMContentLoaded', () => {
    // 1. Counter Animation for Stats
    const stats = document.querySelectorAll('.stat-value');
    const obsOptions = { threshold: 0.5 };
    
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const endValue = parseInt(target.getAttribute('data-value'));
                let startValue = 0;
                const duration = 2000;
                const startTime = performance.now();

                function updateCounter(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const currentValue = Math.floor(progress * endValue);
                    
                    target.textContent = currentValue + (target.getAttribute('data-suffix') || '');
                    
                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    }
                }
                requestAnimationFrame(updateCounter);
                statsObserver.unobserve(target);
            }
        });
    }, obsOptions);

    stats.forEach(stat => statsObserver.observe(stat));

    // 2. Magnetic Hover Effect for Square Buttons
    const magneticButtons = document.querySelectorAll('.magnetic-btn');
    magneticButtons.forEach(btn => {
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            btn.style.transform = `translate(${x * 0.3}px, ${y * 0.5}px)`;
        });

        btn.addEventListener('mouseleave', () => {
            btn.style.transform = 'translate(0px, 0px)';
        });
    });

    // 3. Parallax Image Effect
    const parallaxImages = document.querySelectorAll('.parallax-img');
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        parallaxImages.forEach(img => {
            const speed = img.getAttribute('data-speed') || 0.1;
            const offset = scrolled * speed;
            img.style.transform = `translateY(${offset}px)`;
        });
    });

    // 4. Reveal Animations (handled by data-reveal in app.blade.php but enhanced here)
    const reveals = document.querySelectorAll('[data-reveal]');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, { threshold: 0.1 });

    reveals.forEach(el => revealObserver.observe(el));
});
