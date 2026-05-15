<section class="relative font-[Inter] pb-20">
    <style>
        @keyframes ribbonSlideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .ribbon-animate {
            animation: ribbonSlideUp 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        /* ── Flatpickr Theme Overrides ── */
        .flatpickr-calendar { background: #fff; border-radius: 0; border: none; shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange { background: #A15D1A !important; border-color: #A15D1A !important; }
        .flatpickr-day.inRange { background: rgba(161, 93, 26, 0.1) !important; box-shadow: -5px 0 0 rgba(161, 93, 26, 0.1), 5px 0 0 rgba(161, 93, 26, 0.1) !important; }
        .flatpickr-day:hover { background: #63360D !important; color: #fff !important; border-color: #63360D !important; }
        .flatpickr-months .flatpickr-month { background: #63360D; color: #fff; }
        .flatpickr-current-month .flatpickr-monthDropdown-months { font-weight: 700; }
        .flatpickr-weekday { color: rgba(0,0,0,0.5); font-weight: 700; font-size: 10px; }

        /* ── Hide Default Browser Icons ── */
        input::-webkit-calendar-picker-indicator { display: none !important; opacity: 0; -webkit-appearance: none; }
        input[type="date"], input[type="time"] { -webkit-appearance: none; -moz-appearance: none; appearance: none; }

        /* ── Minimal Selects ── */
        .minimal-select { 
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23A15D1A'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right center;
            background-size: 12px;
            padding-right: 20px;
        }

        /* ── Input Focus Reset ── */
        .search-input { border: none !important; background: transparent !important; }
        .search-input:focus { outline: none !important; box-shadow: none !important; ring: none !important; }
    </style>

    {{-- 4 Feature Cards Ribbon --}}
    <div class="max-w-7xl mx-auto -mt-64 relative z-30 mb-12 ribbon-animate">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
            {{-- Card 1 --}}
            <div class="group bg-[#261405] text-white p-12 flex flex-col items-center text-center transition-all duration-500 hover:-translate-y-4 hover:shadow-2xl">
                <div class="mb-6 transform transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                    <span class="material-symbols-outlined text-6xl text-white/90">schedule</span>
                </div>
                <h3 class="text-2xl font-medium mb-4 text-white">Check-in & Check-out</h3>
                <p class="text-white/70 text-md leading-relaxed max-w-xs transition-colors group-hover:text-white">Experience seamless transitions with our flexible scheduling.</p>
            </div>

            {{-- Card 2 --}}
            <div class="group bg-[#63360D] text-white p-12 flex flex-col items-center text-center transition-all duration-500 hover:-translate-y-4 hover:shadow-2xl">
                <div class="mb-6 transform transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-6">
                    <span class="material-symbols-outlined text-6xl text-white/90">wifi</span>
                </div>
                <h3 class="text-2xl font-medium mb-4 text-white">High Speed Internet</h3>
                <p class="text-white/70 text-md leading-relaxed max-w-xs transition-colors group-hover:text-white">Stay connected in paradise with our dedicated fiber network.</p>
            </div>

            {{-- Card 3 --}}
            <div class="group bg-[#A15D1A] text-white p-12 flex flex-col items-center text-center transition-all duration-500 hover:-translate-y-4 hover:shadow-2xl">
                <div class="mb-6 transform transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                    <span class="material-symbols-outlined text-6xl text-white/90">calendar_month</span>
                </div>
                <h3 class="text-2xl font-medium mb-4 text-white">Simple Booking</h3>
                <p class="text-white/70 text-md leading-relaxed max-w-xs transition-colors group-hover:text-white">Book your dream vacation in just a few simple clicks.</p>
            </div>

            {{-- Card 4 --}}
            <div class="group bg-[#B87431] text-white p-12 flex flex-col items-center text-center transition-all duration-500 hover:-translate-y-4 hover:shadow-2xl">
                <div class="mb-6 transform transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-6">
                    <span class="material-symbols-outlined text-6xl text-white/90">support_agent</span>
                </div>
                <h3 class="text-2xl font-medium mb-4 text-white">Helpful Staff</h3>
                <p class="text-white/70 text-md leading-relaxed max-w-xs transition-colors group-hover:text-white">Our professional team is here to assist you 24/7.</p>
            </div>
        </div>
    </div>

    {{-- Rest of Content in White Background --}}
    <div class="bg-white py-32">
        {{-- Search Form Section --}}
        <div class="max-w-7xl mx-auto px-6 -mt-16 relative z-40 mb-20">
            {{-- Search bar container --}}
            <div class="w-full bg-white shadow-2xl overflow-hidden mb-6 border border-gray-100">
                {{-- Tabs --}}
                <div class="flex border-b border-gray-100">
                    <style>
                        .booking-tab.active { border-bottom-color: #A15D1A !important; color: #A15D1A !important; background: #fdfaf7; }
                    </style>
                    <button id="tab-stay" class="booking-tab flex-1 py-6 text-sm font-medium flex items-center justify-center gap-3 border-b-2 border-transparent transition-all active hover:bg-gray-50" onclick="setMode('stay')">
                        <span class="material-symbols-outlined text-lg">home</span> Stay
                    </button>
                    <button id="tab-sail" class="booking-tab flex-1 py-6 text-sm font-medium flex items-center justify-center gap-3 border-b-2 border-transparent transition-all hover:bg-gray-50" onclick="setMode('sail')">
                        <span class="material-symbols-outlined text-lg">sailing</span> Sail
                    </button>
                </div>

                {{-- Stay bar --}}
                <form id="stay-bar" class="flex flex-col md:flex-row items-stretch" action="{{ route('search.stay') }}" method="POST">
                    @csrf
                    <div class="flex-[2] flex flex-col px-6 py-5 border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
                        <label class="text-[10px] font-medium text-white mb-1">Stay Duration</label>
                        <input type="text" id="stay-range" name="date_range" class="search-input cursor-pointer font-medium text-black" placeholder="Select Dates..." readonly>
                    </div>
                    <div class="flex-1 flex flex-col px-6 py-5 border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
                        <label class="text-[10px] font-medium text-white mb-1">Check-in</label>
                        <select name="checkin_time" class="search-input minimal-select bg-transparent cursor-pointer font-medium text-black">
                            @for($h=8;$h<=20;$h++)
                                <option value="{{ sprintf('%02d:00',$h) }}">{{ date('h:i A', strtotime("$h:00")) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex-1 flex flex-col px-6 py-5 border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
                        <label class="text-[10px] font-medium text-white mb-1">Check-out</label>
                        <select name="checkout_time" class="search-input minimal-select bg-transparent cursor-pointer font-medium text-black">
                            @for($h=8;$h<=20;$h++)
                                <option value="{{ sprintf('%02d:00',$h) }}">{{ date('h:i A', strtotime("$h:00")) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex-1 flex flex-col px-6 py-5 border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
                        <label class="text-[10px] font-medium text-white mb-1">Guests</label>
                        <select class="search-input font-medium text-black" id="hero-guests" name="guests">
                            @for($i=1;$i<=10;$i++)
                                <option value="{{ $i }}">{{ $i }} Guest{{ $i>1?'s':'' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-center px-6 py-4">
                        <button type="submit" class="bg-[#63360D] text-white px-10 py-4 text-[11px] font-medium ] flex items-center gap-2 justify-center whitespace-nowrap hover:bg-[#4D290A] transition shadow-xl shadow-orange-900/10">
                            <span class="material-symbols-outlined text-base">search</span> Search
                        </button>
                    </div>
                </form>

                {{-- Sail bar --}}
                <form id="sail-bar" class="hidden flex-col md:flex-row items-stretch" action="{{ route('search.sail') }}" method="POST">
                    @csrf
                    <div class="flex-[2] flex flex-col px-6 py-5 border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
                        <label class="text-[10px] font-medium text-white mb-1">Departure Date</label>
                        <input type="text" id="sail-date" name="departure_date" class="search-input cursor-pointer font-medium text-black" placeholder="Select Date..." readonly>
                    </div>
                    <div class="flex-1 flex flex-col px-6 py-5 border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
                        <label class="text-[10px] font-medium text-white mb-1">Duration</label>
                        <select class="search-input font-medium text-black" id="hero-boat-duration" name="duration" required>
                            <option value="half">Half Day (4h)</option>
                            <option value="full">Full Day (8h)</option>
                            <option value="overnight">Overnight</option>
                        </select>
                    </div>
                    <div class="flex-1 flex flex-col px-6 py-5 border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
                        <label class="text-[10px] font-medium text-white mb-1">Passengers</label>
                        <select class="search-input font-medium text-black" id="hero-boat-passengers" name="passengers" required>
                            @for($i=1;$i<=20;$i++)
                                <option value="{{ $i }}">{{ $i }} Passenger{{ $i>1?'s':'' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-center px-6 py-4">
                        <button type="submit" class="bg-[#63360D] text-white px-10 py-4 text-[11px] font-medium ] flex items-center gap-2 justify-center whitespace-nowrap hover:bg-[#4D290A] transition shadow-xl shadow-orange-900/10">
                            <span class="material-symbols-outlined text-base">sailing</span> Find Vessel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <section class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 relative">
            <!-- Decorative background text -->
            <div class="absolute -top-10 -right-20 text-[180px] font-medium text-white select-none pointer-events-none ">Services</div>
            
            <div class="relative z-10">
                <div class="flex flex-col items-center mb-20" data-reveal>
                    <p class="text-sm font-medium text-[#A15D1A] ] mb-4">What we offer</p>
                    <h2 class="text-5xl font-medium text-black text-center">World-Class Services</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-0 border border-gray-100">
                    @php
                        $services = [
                            ['title' => 'Restaurant', 'icon' => 'restaurant', 'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=800'],
                            ['title' => 'Luxurious Rooms', 'icon' => 'king_bed', 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=800'],
                            ['title' => 'Inside Pool', 'icon' => 'pool', 'image' => 'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?q=80&w=800'],
                            ['title' => '24h Service', 'icon' => 'concierge', 'image' => 'https://images.unsplash.com/photo-1563330232-57114bb0823c?q=80&w=800'],
                        ];
                    @endphp

                    @foreach($services as $index => $service)
                        <div class="group relative aspect-[3/5] overflow-hidden cursor-pointer" data-reveal data-reveal-delay="{{ $index + 1 }}">
                            <img src="{{ $service['image'] }}" alt="{{ $service['title'] }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent transition-opacity duration-500 group-hover:opacity-80"></div>
                            
                            <div class="absolute inset-x-0 bottom-0 p-8 transform transition-transform duration-500 group-hover:-translate-y-4">
                                <span class="material-symbols-outlined text-4xl text-white/50 mb-6 block group-hover:text-[#B87431] transition-colors">{{ $service['icon'] }}</span>
                                <h3 class="text-2xl font-medium text-white mb-4 ">{{ $service['title'] }}</h3>
                                <div class="h-0.5 w-12 bg-[#B87431] mb-6 transform scale-x-0 transition-transform origin-left duration-500 group-hover:scale-x-100"></div>
                                <p class="text-white/60 text-sm leading-relaxed opacity-0 group-hover:opacity-100 transition-opacity duration-500">Experience the peak of luxury and comfort with our premium {{ strtolower($service['title']) }}.</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none">
        <img src="{{ asset('LOGO-FINAL.png') }}"
             class="w-[900px] h-[900px] object-contain opacity-[0.15]"
             aria-hidden="true" />
    </div>

    {{-- Content --}}
    <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">

            {{-- Left: Text --}}
            <div data-reveal>
                <div class="inline-block bg-[#63360D]/5 px-4 py-2 mb-6">
                    <p class="text-sm font-medium ] text-[#63360D]">Our Heritage</p>
                </div>

                <h2 class="text-5xl md:text-6xl font-medium leading-relaxed] mb-8 text-black">
                    Welcome to the<br>
                    <span class="text-[#63360D]">Cabanas Family</span>
                </h2>

                <p class="text-lg text-black leading-relaxed mb-10 max-w-md">
                    The Cabanas Family Resort is a family-owned getaway built on legacy land. Nestled in the heart of Tambobong, Dasol, we invite you to share in our hidden paradise.
                </p>

                {{-- Stats row --}}
                <div class="grid grid-cols-3 gap-6 mb-12">
                    @php $stats = [['15','Room Types','+'],['5','Guest Rating','★'],['10','Years Open','+']]; @endphp
                    @foreach($stats as [$val,$lbl, $suffix])
                        <div class="border border-gray-100 p-8 text-center bg-white shadow-xl shadow-gray-200/40 hover:border-[#63360D] transition-colors group">
                            <p class="text-4xl font-medium mb-2 text-black group-hover:text-[#63360D] transition-colors">
                                <span class="stat-value" data-value="{{ $val }}" data-suffix="{{ $suffix }}">0</span>
                            </p>
                            <p class="text-[10px] font-medium text-white">{{ $lbl }}</p>
                        </div>
                    @endforeach
                </div>

                <a href="{{ url('/home/amenities') }}"
                   class="inline-flex items-center gap-4 text-sm font-medium ] text-black border-b-2 pb-2 transition-all hover:text-[#63360D] hover:border-[#63360D] group"
                   style="border-color:#63360D;">
                    Discover our amenities
                    <span class="material-symbols-outlined text-lg transform transition-transform group-hover:translate-x-2">east</span>
                </a>
            </div>

            {{-- Right: Images --}}
            <div class="grid grid-cols-2 gap-4" data-reveal data-reveal-delay="2">
                {{-- Tall left image --}}
                <div class="col-span-2 h-56 overflow-hidden shadow-md">
                    <img src="{{ asset('images/1758952332.jpg') }}"
                         class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                         alt="Cabanas Resort" />
                </div>
                {{-- Two smaller images --}}
                <div class="h-44 overflow-hidden shadow-md">
                    <img src="{{ asset('images/1758952350.jpg') }}"
                         class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                         alt="Cabanas Beach" />
                </div>
                <div class="h-44 overflow-hidden shadow-md relative">
                    <img src="{{ asset('images/1758952017.jpg') }}"
                         class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                         alt="Cabanas Sign" />
                    {{-- Location badge --}}
                    <div class="absolute bottom-0 left-0 right-0 px-3 py-2" style="background:rgba(99,54,13,0.85);">
                        <p class="text-white text-sm font-medium">Tambobong, Dasol</p>
                        <p class="text-white/70 text-[11px]">Pangasinan, Philippines</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
function setMode(mode) {
    const stayTab = document.getElementById('tab-stay');
    const sailTab = document.getElementById('tab-sail');
    const stayBar = document.getElementById('stay-bar');
    const sailBar = document.getElementById('sail-bar');

    if (mode === 'stay') {
        stayTab.classList.add('active');
        sailTab.classList.remove('active');
        stayBar.classList.remove('hidden');
        stayBar.classList.add('flex');
        sailBar.classList.add('hidden');
        sailBar.classList.remove('flex');
    } else {
        sailTab.classList.add('active');
        stayTab.classList.remove('active');
        sailBar.classList.remove('hidden');
        sailBar.classList.add('flex');
        stayBar.classList.add('hidden');
        stayBar.classList.remove('flex');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Flatpickr for Stay Range
    flatpickr("#stay-range", {
        mode: "range",
        minDate: "today",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "M j, Y",
        showMonths: 2
    });

    // Initialize Flatpickr for Sail Date
    flatpickr("#sail-date", {
        minDate: "today",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "M j, Y"
    });
});
</script>
