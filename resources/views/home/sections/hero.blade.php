<style>
    /* Senior Frontend: Performance-Optimized Motion System */
    #hero-section { height: 92vh; min-height: 600px; position: relative; overflow: hidden; background: #000; }
    
    .hero-slide { 
        position: absolute; 
        inset: 0; 
        visibility: hidden; /* Use visibility instead of display:none for better performance */
        overflow: hidden;
        z-index: 1;
        will-change: transform; /* Hardware acceleration hint */
    }

    /* Hardware Accelerated Slides */
    .hero-slide.active { 
        visibility: visible;
        z-index: 10;
        animation: revealDown 1.4s cubic-bezier(0.77, 0, 0.175, 1) forwards;
    }

    .hero-slide.active img {
        will-change: transform;
        animation: imageCounter 1.4s cubic-bezier(0.77, 0, 0.175, 1) forwards;
    }

    @keyframes revealDown {
        0% { transform: translate3d(0, -100%, 0); }
        100% { transform: translate3d(0, 0, 0); }
    }

    @keyframes imageCounter {
        0% { transform: translate3d(0, 100%, 0) scale(1.1); }
        100% { transform: translate3d(0, 0, 0) scale(1); }
    }

    /* Staggered Text Reveal - REMOVED for instant change */
    .hero-h2, .hero-p, .hero-btn { opacity: 1; transform: none; }

    /* Basic Styles */
    .hero-slide img { width: 100%; height: 100%; object-fit: cover; }
</style>

<section id="hero-section" aria-label="Hero">
    <div id="hero-track" class="absolute inset-0 z-0">
        @php
            $slides = [
                ['img'=>asset('hero/a.png'), 'label'=>'Welcome to Cabanas', 'sub'=>'Experience luxury and comfort in our beautiful beachfront cabanas, where the turquoise sea meets pure serenity.'],
                ['img'=>asset('hero/b.png'), 'label'=>'Beachfront Bliss', 'sub'=>'Wake up to the gentle sound of waves and step directly onto the pristine white sands of Dasol Bay.'],
                ['img'=>asset('hero/c.png'), 'label'=>'Island Adventures', 'sub'=>'Set sail on a private tour through the hidden gems of Pangasinan, exploring vibrant coral reefs and lagoons.'],
                ['img'=>asset('hero/d.png'), 'label'=>'Luxury Kubo Suites', 'sub'=>'Discover the perfect blend of traditional Filipino architecture and modern luxury in our curated Kubo suites.'],
                ['img'=>asset('hero/e.png'), 'label'=>'Penthouse Horizons', 'sub'=>'Elevate your stay in our premier penthouse, featuring panoramic views of the South China Sea and private balconies.'],
            ];
        @endphp
        @foreach($slides as $i => $slide)
            <div class="hero-slide {{ $i===0?'active':'' }}" data-index="{{ $i }}">
                <img src="{{ $slide['img'] }}"
                     onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80'"
                     alt="{{ $slide['label'] }}">
                <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(0,0,0,.75) 0%, rgba(0,0,0,.25) 55%, rgba(0,0,0,.1) 100%);"></div>
                
                {{-- Slide Content --}}
                <div class="absolute inset-0 z-10 flex justify-end items-center px-6 max-w-7xl mx-auto w-full">
                    <div class="w-full lg:max-w-2xl text-white p-8 lg:p-12 bg-black/70 shadow-2xl">
                        <h2 class="hero-h2 text-4xl lg:text-5xl font-black mb-4 leading-tight text-white">
                            {!! preg_replace('/(\S+)$/', '<span style="color:white;">$1</span>', $slide['label']) !!}
                        </h2>
                        <p class="hero-p text-white/90 mb-8 leading-relaxed text-lg">
                            {{ $slide['sub'] }}
                        </p>
                        <div class="hero-btn">
                            <a href="{{ url('/home/rooms') }}" class="inline-block px-10 py-3 font-semibold bg-white text-black text-sm uppercase tracking-wide hover:bg-gray-200 transition">
                                View More
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<script>
    let currentIdx = 0;
    let heroSlides;
    let isAnimating = false;

    function goToSlide(idx) {
        if (isAnimating || !heroSlides) return;
        if (idx === currentIdx) return;

        isAnimating = true;
        const nextIdx = (idx + heroSlides.length) % heroSlides.length;
        const oldSlide = heroSlides[currentIdx];
        const nextSlide = heroSlides[nextIdx];

        // Prepare next slide (it's hidden by visibility:hidden)
        nextSlide.classList.remove('active'); // reset class if needed
        void nextSlide.offsetWidth; // force reflow once
        
        // Switch slides
        oldSlide.style.zIndex = "5";
        nextSlide.style.zIndex = "10";
        nextSlide.classList.add('active');

        setTimeout(() => {
            oldSlide.classList.remove('active');
            oldSlide.style.zIndex = "1";
            currentIdx = nextIdx;
            isAnimating = false;
        }, 1400); 
    }

    document.addEventListener('DOMContentLoaded', () => {
        heroSlides = document.querySelectorAll('.hero-slide');
        setInterval(() => {
            if (!document.hidden) goToSlide(currentIdx + 1);
        }, 6500);
    });
</script>