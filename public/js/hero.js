
// Mobile Menu Toggle - scope and guard to avoid global redeclarations
document.addEventListener('DOMContentLoaded', () => {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    let isMenuOpen = false;

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            isMenuOpen = !isMenuOpen;
            if (isMenuOpen) {
                mobileMenu.classList.remove('hidden');
                mobileMenuBtn.innerHTML = '<i class="fa-solid fa-xmark text-2xl"></i>';
            } else {
                mobileMenu.classList.add('hidden');
                mobileMenuBtn.innerHTML = '<i class="fa-solid fa-bars-staggered text-2xl"></i>';
            }
        });
    }
});

// Hero Image Slider Logic - Uses the new .carousel-slide class
function startHeroSlider() {
    const slides = document.querySelectorAll('.carousel-slide');
    if (!slides || slides.length === 0) return;
    let currentSlide = 0;
    const slideInterval = 5000; // 5 seconds

    setInterval(() => {
        // Fade out current
        slides[currentSlide].classList.remove('opacity-100');
        slides[currentSlide].classList.add('opacity-0');
        slides[currentSlide].dataset.active = 'false';

        // Calculate next
        currentSlide = (currentSlide + 1) % slides.length;

        // Fade in next
        slides[currentSlide].classList.remove('opacity-0');
        slides[currentSlide].classList.add('opacity-100');
        slides[currentSlide].dataset.active = 'true';
    }, slideInterval);
}

// Initialize Slider on Load
document.addEventListener('DOMContentLoaded', startHeroSlider);

// Booking Logic (for toast message)
function handleBooking(event) {
    event.preventDefault();
    // Find the submit button, whether it's the target or a child
    const searchButton = event.target.querySelector('button[type="submit"]') || event.target;
    const originalContent = searchButton.innerHTML;

    searchButton.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> SEARCH';

    // Simulating a search delay
    setTimeout(() => {
        // In a real application, you'd show a success/failure message here.
        // For demonstration, we just reset the button text.
        searchButton.innerHTML = originalContent;
        alert('Search submitted for booking.');
    }, 1500);
}





tailwind.config = {
    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', 'sans-serif'],
            },
            colors: {
                brand: {
                    600: '#ea580c', // Secondary Orange
                },
                // Custom colors inferred from the design image
                'btn-yellow': '#FBBd00', // Gold/Mustard color for buttons
                'accent-dark': '#1e3a46', // Dark Teal/Navy for menu highlight/footer
                'text-gold': '#cdac6e', // Subtle gold text color
            },
            animation: {
                'fade-in-down': 'fadeInDown 0.5s ease-out forwards',
            },
            keyframes: {
                fadeInDown: {
                    '0%': { opacity: '0', transform: 'translateY(-10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                }
            }
        }
    }
}
