<style>
    /* Premium Hotel Motion System: Clean, smooth, elegant transitions */
    #hero-section { height: 92vh; min-height: 600px; position: relative; overflow: hidden; background: #000; }
    
    .hero-slide { 
        position: absolute; 
        inset: 0; 
        visibility: hidden;
        opacity: 0;
        overflow: hidden;
        z-index: 1;
        pointer-events: none;
        transition:
            opacity 1500ms ease-in-out,
            visibility 0s linear 1500ms;
        will-change: opacity;
        contain: paint;
    }

    .hero-slide.active { 
        visibility: visible;
        opacity: 1;
        z-index: 10;
        pointer-events: auto;
        transition:
            opacity 1500ms ease-in-out,
            visibility 0s linear 0s;
    }

    /* Subtle, continuous slow pan/zoom */
    .hero-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scale(1.05);
        transition: transform 8000ms ease-out;
        will-change: transform;
        backface-visibility: hidden;
    }

    .hero-slide.active img {
        transform: scale(1);
    }

    .hero-h2, .hero-p, .hero-btn { opacity: 1; transform: none; }

    @media (prefers-reduced-motion: reduce) {
        .hero-slide,
        .hero-slide img {
            transition: none;
        }
    }
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
                        <h2 class="hero-h2 text-4xl lg:text-6xl font-extrabold mb-4 leading-relaxed text-white">
                            {!! preg_replace('/(\S+)$/', '<span style="color:white;">$1</span>', $slide['label']) !!}
                        </h2>
                        <p class="hero-p text-white font-medium mb-8 leading-relaxed text-lg">
                            {{ $slide['sub'] }}
                        </p>
                        <div class="hero-btn">
                            <a href="{{ url('/home/rooms') }}" class="inline-block px-8 py-4 font-semibold bg-white text-black text-lg hover:bg-[#63360D] hover:text-white transition shadow-2xl">
                                Explore Accommodations
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<script>
    (() => {
        const intervalMs = 6500;
        const transitionMs = 1200;
        let currentIdx = 0;
        let heroSlides = [];
        let timer = null;
        let isAnimating = false;

        function goToSlide(idx) {
            if (isAnimating || heroSlides.length < 2) return;

            const nextIdx = (idx + heroSlides.length) % heroSlides.length;
            if (nextIdx === currentIdx) return;

            isAnimating = true;
            heroSlides[nextIdx].classList.add('active');
            heroSlides[currentIdx].classList.remove('active');
            currentIdx = nextIdx;

            window.setTimeout(() => {
                isAnimating = false;
            }, transitionMs);
        }

        function stopCarousel() {
            if (!timer) return;
            window.clearInterval(timer);
            timer = null;
        }

        function startCarousel() {
            stopCarousel();
            timer = window.setInterval(() => {
                if (!document.hidden) goToSlide(currentIdx + 1);
            }, intervalMs);
        }

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopCarousel();
                return;
            }

            startCarousel();
        });

        document.addEventListener('DOMContentLoaded', () => {
            heroSlides = Array.from(document.querySelectorAll('#hero-section .hero-slide'));
            heroSlides.forEach((slide, index) => {
                slide.classList.toggle('active', index === 0);
            });

            startCarousel();
        });
    })();
</script>
