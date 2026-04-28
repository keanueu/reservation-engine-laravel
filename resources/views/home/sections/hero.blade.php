<style>
    #hero-section { height: 92vh; min-height: 600px; position: relative; overflow: hidden; }

    .hero-slide { position:absolute; inset:0; opacity:0; transition:opacity 1.8s cubic-bezier(.4,0,.2,1), transform 2.2s cubic-bezier(.4,0,.2,1); transform:scale(1.05); will-change:opacity,transform; }
    .hero-slide.active { opacity:1; transform:scale(1); z-index:2; }
    .hero-slide img { width:100%; height:100%; object-fit:cover; }

    /* Search bar */
    .search-bar { background:rgba(255,255,255,0.97); box-shadow:0 8px 40px rgba(0,0,0,0.18); }
    .search-input { border:none; outline:none; background:transparent; font-family:'Manrope',sans-serif; font-size:.875rem; color:#111827; width:100%; }
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
    <div class="relative z-10 h-full flex flex-col justify-end pb-20 px-6 max-w-6xl mx-auto w-full">

        {{-- Mode toggle --}}
        <div class="flex items-center gap-0 mb-4 w-fit border-b border-white/20">
            <button id="tab-stay" class="mode-tab active" onclick="setMode('stay')">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Stay
                </span>
            </button>
            <button id="tab-sail" class="mode-tab" onclick="setMode('sail')">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-4 6m4-6l4 6M3 19h18"/></svg>
                    Sail
                </span>
            </button>
        </div>

        {{-- Headline --}}
        <div class="mb-6">
            <p id="hero-sub" class="text-xs font-semibold tracking-[.3em] uppercase text-white/70 mb-2 transition-all duration-500">Where the sea meets serenity</p>
            <h1 id="hero-label" class="font-playfair text-5xl md:text-6xl lg:text-7xl text-white font-semibold leading-tight transition-all duration-500">
                Welcome to<br><span style="color:#f5c87a;">Cabanas</span>
            </h1>
        </div>

        {{-- Search bar --}}
        <div class="search-bar w-full max-w-5xl mb-6">
            {{-- Stay bar --}}
            <form id="stay-bar" class="flex flex-col md:flex-row items-stretch" action="{{ url('/home/roomcart') }}" method="GET">
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
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Search
                    </button>
                </div>
            </form>

            {{-- Sail bar --}}
            <form id="sail-bar" class="hidden flex-col md:flex-row items-stretch" action="{{ url('/home/roomcart') }}" method="GET">
                <input type="hidden" name="type" value="boat">
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
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-4 6m4-6l4 6M3 19h18"/></svg>
                        Find Vessel
                    </button>
                </div>
            </form>
        </div>

        {{-- Trust badges + dots row --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            {{-- Trust badges --}}
            <div class="flex items-center gap-5">
                @php $badges = [['icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','label'=>'Verified Resort'],['icon'=>'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z','label'=>'Secure Booking'],['icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z','label'=>'PayMongo Payments']]; @endphp
                @foreach($badges as $b)
                    <div class="flex items-center gap-1.5 text-white/75">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $b['icon'] }}"/></svg>
                        <span class="text-xs font-semibold">{{ $b['label'] }}</span>
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
        <svg class="w-5 h-5 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </div>
</section>

{{-- Animated SVG wave divider --}}
<div class="relative overflow-hidden bg-white" style="height:64px; margin-top:-2px;">
    <svg class="wave-scroll absolute bottom-0" style="width:200%; height:64px;" viewBox="0 0 1440 64" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,32 C180,64 360,0 540,32 C720,64 900,0 1080,32 C1260,64 1440,0 1440,32 L1440,64 L0,64 Z" fill="#faf9f7"/>
        <path d="M1440,32 C1620,64 1800,0 1980,32 C2160,64 2340,0 2520,32 C2700,64 2880,0 2880,32 L2880,64 L1440,64 Z" fill="#faf9f7"/>
    </svg>
</div>

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
