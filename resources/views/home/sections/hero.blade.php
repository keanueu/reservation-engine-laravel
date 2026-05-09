<style>
    #hero-section { height: 92vh; min-height: 600px; position: relative; overflow: hidden; }

    .hero-slide { position:absolute; inset:0; opacity:0; transition:opacity 1.8s cubic-bezier(.4,0,.2,1), transform 2.2s cubic-bezier(.4,0,.2,1); transform:scale(1.05); will-change:opacity,transform; }
    .hero-slide.active { opacity:1; transform:scale(1); z-index:2; }
    .hero-slide img { width:100%; height:100%; object-fit:cover; }

    /* Search bar */
    .search-bar { background:rgba(255,255,255,0.97); box-shadow:0 8px 40px rgba(0,0,0,0.18); }
    .search-input { border:none; outline:none; background:transparent; font-family:'Inter',sans-serif; font-size:.875rem; color:#111827; width:100%; }
    .search-input::placeholder { color:#9ca3af; }
    .search-divider { width:1px; height:36px; background:#e5e7eb; flex-shrink:0; }

    /* Mode tabs */
    .mode-tab { padding:.5rem 1.25rem; font-size:.75rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; cursor:pointer; transition:all .2s; color:rgba(255,255,255,.6); border-bottom:2px solid transparent; }
    .mode-tab.active { color:#fff; border-bottom-color:#fff; }

    /* Slide dots */
    .slide-dot { width:6px; height:6px; background:rgba(255,255,255,.4); transition:all .3s; cursor:pointer; }
    .slide-dot.active { width:24px; background:#fff; }

    /* Wave */
    @keyframes waveScroll { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
    .wave-scroll { animation: waveScroll 14s linear infinite; }

    /* Scroll cue */
    @keyframes bounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(6px)} }
    .scroll-cue { animation: bounce 2s ease-in-out infinite; }
</style>

<section id="hero-section" aria-label="Hero">

    {{-- Slide track --}}
    <div id="hero-track" class="absolute inset-0 z-0">
        @php
            $slides = [
                ['img'=>asset('hero/a.png'), 'label'=>'Welcome to Cabanas',      'sub'=>'Where the sea meets serenity'],
                ['img'=>asset('hero/b.png'), 'label'=>'Beachfront Cabanas',      'sub'=>'Wake up to the sound of waves'],
                ['img'=>asset('hero/c.png'), 'label'=>'Island Adventures',       'sub'=>'Set sail from Dasol Bay'],
                ['img'=>asset('hero/d.png'), 'label'=>'Luxury Kubo Suites',      'sub'=>'Intimate comfort, island style'],
                ['img'=>asset('hero/e.png'), 'label'=>'Penthouse Seaview',       'sub'=>'Panoramic ocean horizons'],
            ];
        @endphp
        @foreach($slides as $i => $slide)
            <div class="hero-slide {{ $i===0?'active':'' }}" data-index="{{ $i }}">
                <img src="{{ $slide['img'] }}"
                     onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80'"
                     alt="{{ $slide['label'] }}">
                <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(0,0,0,.75) 0%, rgba(0,0,0,.25) 55%, rgba(0,0,0,.1) 100%);"></div>
            </div>
        @endforeach
    </div>

    {{-- Hero content --}}
    <div class="relative z-10 h-full flex flex-col justify-end pb-20 px-6 max-w-7xl mx-auto w-full">

        {{-- Mode toggle --}}
        <div class="flex items-center gap-0 mb-4 w-fit border-b border-white/20">
            <button id="tab-stay" class="mode-tab active" onclick="setMode('stay')">
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined" style="font-size: 14px;">home</span>
                    Stay
                </span>
            </button>
            <button id="tab-sail" class="mode-tab" onclick="setMode('sail')">
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined" style="font-size: 14px;">sailing</span>
                    Sail
                </span>
            </button>
        </div>

        {{-- Main Content Layout --}}
        <div class="flex flex-col lg:flex-row items-center justify-between gap-12 mb-12">
            
            {{-- Left Side: Dynamic Headline --}}
            <div class="flex-1 min-w-0" data-reveal>
                <p id="hero-sub" class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4 transition-all duration-500">
                    Where the sea meets serenity
                </p>
                <h1 id="hero-label" class="text-5xl md:text-6xl lg:text-7xl text-white font-bold leading-[1.1] tracking-tight transition-all duration-500">
                    Welcome to<br><span style="color:#f5c87a;">Cabanas</span>
                </h1>
            </div>

            {{-- Right Side: Luxurious Lifestyle Box --}}
            <div class="max-w-2xl bg-black/70 text-white p-12 hidden lg:block mr-[5%]" data-reveal>
                <h2 class="text-4xl lg:text-5xl font-bold mb-4 leading-tight">Live a Luxurious Lifestyle</h2>
                <p class="text-white/90 mb-8 leading-relaxed text-lg">
                    Curabitur molestie luctus odio et consectetur. Donec cursus elementum arcu eget blandit.
                </p>
                <a href="{{ url('/home/rooms') }}" class="inline-block px-10 py-3 font-semibold bg-white text-black text-sm uppercase tracking-wide hover:bg-gray-200 transition">
                    View More
                </a>
            </div>
        </div>

        {{-- Search bar --}}
        <div class="search-bar w-full max-w-7xl mb-6">
            {{-- Stay bar --}}
            <form id="stay-bar" class="flex flex-col md:flex-row items-stretch" action="{{ route('search.stay') }}" method="POST">
                @csrf
                <div class="flex-1 flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                    <label class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-1">Check-in</label>
                    <input type="date" class="search-input" id="hero-checkin" name="checkin" min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="flex-1 flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                    <label class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-1">Check-in Time</label>
                    <input type="time" class="search-input" id="hero-checkin-time" name="checkin_time" required>
                </div>
                <div class="flex-1 flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                    <label class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-1">Check-out</label>
                    <input type="date" class="search-input" id="hero-checkout" name="checkout" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                </div>
                <div class="flex-1 flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                    <label class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-1">Check-out Time</label>
                    <input type="time" class="search-input" id="hero-checkout-time" name="checkout_time" required>
                </div>
                <div class="flex-1 flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                    <label class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-1">Guests</label>
                    <select class="search-input" id="hero-guests" name="guests">
                        @for($i=1;$i<=10;$i++)
                            <option value="{{ $i }}">{{ $i }} Guest{{ $i>1?'s':'' }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-center px-4 py-3">
                    <button type="submit"
                       class="btn-primary w-full md:w-auto px-8 py-3.5 text-xs font-bold tracking-widest uppercase flex items-center gap-2 justify-center whitespace-nowrap">
                        <span class="material-symbols-outlined text-base">search</span>
                        Search
                    </button>
                </div>
            </form>

            {{-- Sail bar --}}
            <form id="sail-bar" class="hidden flex-col md:flex-row items-stretch" action="{{ route('search.sail') }}" method="POST">
                @csrf
                <div class="flex-1 flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                    <label class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-1">Departure Date</label>
                    <input type="date" class="search-input" id="hero-boat-date" name="departure_date" min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="flex-1 flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                    <label class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-1">Duration</label>
                    <select class="search-input" id="hero-boat-duration" name="duration" required>
                        <option value="half">Half Day (4h)</option>
                        <option value="full">Full Day (8h)</option>
                        <option value="overnight">Overnight</option>
                    </select>
                </div>
                <div class="flex-1 flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                    <label class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-1">Passengers</label>
                    <select class="search-input" id="hero-boat-passengers" name="passengers" required>
                        @for($i=1;$i<=20;$i++)
                            <option value="{{ $i }}">{{ $i }} Passenger{{ $i>1?'s':'' }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-center px-4 py-3">
                    <button type="submit"
                       class="btn-primary w-full md:w-auto px-8 py-3.5 text-xs font-bold tracking-widest uppercase flex items-center gap-2 justify-center whitespace-nowrap">
                        <span class="material-symbols-outlined text-base">sailing</span>
                        Find Vessel
                    </button>
                </div>
            </form>
        </div>

        {{-- Trust badges + dots row --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            {{-- Trust badges --}}
            <div class="flex items-center gap-5">
                @php $badges = [
                    ['icon'=>'verified','label'=>'Verified Resort'],
                    ['icon'=>'lock','label'=>'Secure Booking'],
                    ['icon'=>'credit_card','label'=>'PayMongo Payments']
                ]; @endphp
                @foreach($badges as $b)
                    <div class="flex items-center gap-1.5" style="color: #ffffff;">
                        <span class="material-symbols-outlined" style="font-size: 16px; color: #ffffff;">{{ $b['icon'] }}</span>
                        <span class="text-xs" style="color: #ffffff;">{{ $b['label'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Slide dots --}}
            <div class="flex items-center gap-2" id="hero-dots">
                @foreach($slides as $i => $slide)
                    <button onclick="goToSlide({{ $i }})"
                            class="slide-dot {{ $i===0?'active':'' }}"
                            aria-label="Slide {{ $i+1 }}"></button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Scroll cue --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-10 scroll-cue">
        <span class="material-symbols-outlined text-2xl text-white/50">expand_more</span>
    </div>

      <div class="w-full bg-[#777777]"> <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
            <div class="bg-black text-white p-12 flex flex-col items-center text-center">
                <div class="mb-6">
                    <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-4">Check-in & Check-out Time</h3>
                <p class="text-gray-400 text-sm leading-relaxed">It is a long established fact that a reader will be distracted</p>
            </div>

            <div class="bg-[#333333] text-white p-12 flex flex-col items-center text-center">
                <div class="mb-6">
                    <svg class="w-14 h-14" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-4">High Speed Internet</h3>
                <p class="text-gray-300 text-sm leading-relaxed">It is a long established fact that a reader will be distracted</p>
            </div>

            <div class="bg-[#555555] text-white p-12 flex flex-col items-center text-center">
                <div class="mb-6">
                    <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-4">Simple Booking</h3>
                <p class="text-gray-200 text-sm leading-relaxed">It is a long established fact that a reader will be distracted</p>
            </div>

            <div class="bg-[#777777] text-white p-12 flex flex-col items-center text-center">
                <div class="mb-6">
                    <svg class="w-14 h-14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-4">Helpful Staff</h3>
                <p class="text-gray-100 text-sm leading-relaxed">It is a long established fact that a reader will be distracted</p>
            </div>
        </div>
    </div>

</section>

<script>
    let currentIdx = 0;
    let heroTimer;
    const heroSlides = document.querySelectorAll('.hero-slide');
    const heroDots   = document.querySelectorAll('.slide-dot');
    const heroLabel  = document.getElementById('hero-label');
    const heroSub    = document.getElementById('hero-sub');

    @php
        $slideData = $slides ?? [
            ['label'=>'Welcome to Cabanas','sub'=>'Where the sea meets serenity'],
            ['label'=>'Beachfront Cabanas','sub'=>'Wake up to the sound of waves'],
            ['label'=>'Island Adventures','sub'=>'Set sail from Dasol Bay'],
            ['label'=>'Luxury Kubo Suites','sub'=>'Intimate comfort, island style'],
            ['label'=>'Penthouse Seaview','sub'=>'Panoramic ocean horizons'],
        ];
    @endphp
    const slideData = @json($slideData);

    function goToSlide(idx) {
        heroSlides[currentIdx].classList.remove('active');
        heroDots[currentIdx].classList.remove('active');
        currentIdx = (idx + heroSlides.length) % heroSlides.length;
        heroSlides[currentIdx].classList.add('active');
        heroDots[currentIdx].classList.add('active');
        const d = slideData[currentIdx];
        if(heroLabel && d){
            heroLabel.style.opacity='0'; heroSub.style.opacity='0';
            setTimeout(()=>{
                heroLabel.innerHTML = d.label.replace(/(\S+)$/, '<span style="color:#f5c87a;">$1</span>');
                heroSub.textContent = d.sub;
                heroLabel.style.opacity='1'; heroSub.style.opacity='1';
            }, 300);
        }
        clearInterval(heroTimer);
        heroTimer = setInterval(()=>goToSlide(currentIdx+1), 5500);
    }

    function setMode(mode){
        document.getElementById('tab-stay').classList.toggle('active', mode==='stay');
        document.getElementById('tab-sail').classList.toggle('active', mode==='sail');
        const stayBar = document.getElementById('stay-bar');
        const sailBar = document.getElementById('sail-bar');
        if(mode==='stay'){ stayBar.classList.remove('hidden'); stayBar.classList.add('flex'); sailBar.classList.add('hidden'); sailBar.classList.remove('flex'); }
        else { sailBar.classList.remove('hidden'); sailBar.classList.add('flex'); stayBar.classList.add('hidden'); stayBar.classList.remove('flex'); }
    }

    document.addEventListener('DOMContentLoaded', ()=>{
        if(heroLabel){ heroLabel.style.transition='opacity .35s ease'; heroSub.style.transition='opacity .35s ease'; }
        heroTimer = setInterval(()=>goToSlide(currentIdx+1), 5500);
        
        // Set minimum dates for date inputs
        const today = new Date().toISOString().split('T')[0];
        const tomorrow = new Date(Date.now() + 86400000).toISOString().split('T')[0];
        
        const checkinInput = document.getElementById('hero-checkin');
        const checkoutInput = document.getElementById('hero-checkout');
        const boatDateInput = document.getElementById('hero-boat-date');
        
        if(checkinInput) checkinInput.min = today;
        if(checkoutInput) checkoutInput.min = tomorrow;
        if(boatDateInput) boatDateInput.min = today;
        
        // Update checkout min date when checkin changes
        if(checkinInput && checkoutInput) {
            checkinInput.addEventListener('change', function() {
                const checkinDate = new Date(this.value);
                checkinDate.setDate(checkinDate.getDate() + 1);
                checkoutInput.min = checkinDate.toISOString().split('T')[0];
                if(checkoutInput.value && checkoutInput.value <= this.value) {
                    checkoutInput.value = '';
                }
            });
        }
    });
</script>
