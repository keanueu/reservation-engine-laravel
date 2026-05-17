<section class="relative font-['Raleway'] pb-20">
    <style>
        @keyframes ribbonSlideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .ribbon-animate {
            animation: ribbonSlideUp 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }


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
                <h3 class="text-2xl font-semibold mb-4 text-white">Check-in & Check-out</h3>
                <p class="text-white text-md leading-relaxed max-w-xs transition-colors group-hover:text-white">Experience seamless transitions with our flexible scheduling.</p>
            </div>

            {{-- Card 2 --}}
            <div class="group bg-[#63360D] text-white p-12 flex flex-col items-center text-center transition-all duration-500 hover:-translate-y-4 hover:shadow-2xl">
                <div class="mb-6 transform transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-6">
                    <span class="material-symbols-outlined text-6xl text-white/90">wifi</span>
                </div>
                <h3 class="text-2xl font-semibold mb-4 text-white">High Speed Internet</h3>
                <p class="text-white text-md leading-relaxed max-w-xs transition-colors group-hover:text-white">Stay connected in paradise with our dedicated fiber network.</p>
            </div>

            {{-- Card 3 --}}
            <div class="group bg-[#A15D1A] text-white p-12 flex flex-col items-center text-center transition-all duration-500 hover:-translate-y-4 hover:shadow-2xl">
                <div class="mb-6 transform transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6">
                    <span class="material-symbols-outlined text-6xl text-white/90">calendar_month</span>
                </div>
                <h3 class="text-2xl font-semibold mb-4 text-white">Simple Booking</h3>
                <p class="text-white text-md leading-relaxed max-w-xs transition-colors group-hover:text-white">Book your dream vacation in just a few simple clicks.</p>
            </div>

            {{-- Card 4 --}}
            <div class="group bg-[#B87431] text-white p-12 flex flex-col items-center text-center transition-all duration-500 hover:-translate-y-4 hover:shadow-2xl">
                <div class="mb-6 transform transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-6">
                    <span class="material-symbols-outlined text-6xl text-white/90">support_agent</span>
                </div>
                <h3 class="text-2xl font-semibold mb-4 text-white">Helpful Staff</h3>
                <p class="text-white text-md leading-relaxed max-w-xs transition-colors group-hover:text-white">Our professional team is here to assist you 24/7.</p>
            </div>
        </div>
    </div>

    {{-- Rest of Content in White Background --}}
    <div class="bg-white py-32 relative overflow-hidden">
        {{-- Logo Watermark --}}
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none z-0">
            <img src="{{ asset('LOGO-FINAL.png') }}"
                 class="w-[850px] h-[850px] object-contain opacity-[0.15]"
                 aria-hidden="true" />
        </div>

        {{-- Search Form Section --}}
        <div class="max-w-7xl mx-auto px-6 relative z-40 mb-6">
             <div class="flex flex-col items-center mb-12" data-reveal>
                    <p class="text-md font-semibold text-[#A15D1A] mb-2">Dream Vacation</p>
                    <h2 class="text-6xl font-extrabold text-[#63360D] text-center">Book Your Staycation</h2>
                </div>
            {{-- Search bar container --}}
            <div class="w-full bg-white shadow-md overflow-hidden mb-6 border border-gray-100">
                {{-- Tabs --}}
                <div class="flex border-b border-gray-100">
                    <style>
                        .booking-tab.active { border-bottom-color: #A15D1A !important; color: #A15D1A !important; background: #fdfaf7; }
                    </style>
                    <button id="tab-stay" class="booking-tab flex-1 py-6 text-md font-semibold flex items-center justify-center gap-1 border-b-2 border-transparent transition-all active hover:bg-gray-50" onclick="setMode('stay')">
                        <span class="material-symbols-outlined text-xl">home</span> Stay
                    </button>
                    <button id="tab-sail" class="booking-tab flex-1 py-6 text-md font-semibold flex items-center justify-center gap-1 border-b-2 border-transparent transition-all hover:bg-gray-50" onclick="setMode('sail')">
                        <span class="material-symbols-outlined text-2xl">sailing</span> Sail
                    </button>
                </div>

                {{-- Stay bar --}}
                <form id="stay-bar" class="flex flex-col md:flex-row items-stretch" action="{{ route('search.stay') }}" method="POST">
                    @csrf
                    <div class="flex-[2] flex flex-col px-6 py-5 border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
                        <label class="text-lg font-semibold text-[#63360D] mb-1">Stay Duration</label>
                        <input type="text" id="stay-range" name="date_range" class="search-input cursor-pointer font-medium text-black" placeholder="Select Dates..." readonly>
                    </div>
                    <div class="flex-1 flex flex-col px-6 py-5 border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
                        <label class="text-lg font-semibold text-[#63360D] mb-1">Check-in</label>
                        <select name="checkin_time" class="search-input minimal-select bg-transparent cursor-pointer font-medium text-black">
                            @for($h=8;$h<=20;$h++)
                                <option value="{{ sprintf('%02d:00',$h) }}">{{ date('h:i A', strtotime("$h:00")) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex-1 flex flex-col px-6 py-5 border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
                        <label class="text-lg font-semibold text-[#63360D] mb-1">Check-out</label>
                        <select name="checkout_time" class="search-input minimal-select bg-transparent cursor-pointer font-medium text-black">
                            @for($h=8;$h<=20;$h++)
                                <option value="{{ sprintf('%02d:00',$h) }}">{{ date('h:i A', strtotime("$h:00")) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex-1 flex flex-col px-6 py-5 border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
                        <label class="text-lg font-semibold text-[#63360D] mb-1">Guests</label>
                        <select class="search-input font-semibold text-black" id="hero-guests" name="guests">
                            @for($i=1;$i<=10;$i++)
                                <option value="{{ $i }}">{{ $i }} Guest{{ $i>1?'s':'' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-center px-6 py-4">
                        <button type="submit" class="bg-[#63360D] text-white px-8 py-3 text-md font-semibold flex items-center gap-1 justify-center whitespace-nowrap hover:bg-[#4D290A] transition shadow-xl shadow-orange-900/10">
                            <span class="material-symbols-outlined text-xl">search</span> Find Rooms
                        </button>
                    </div>
                </form>

                {{-- Sail bar --}}
                <form id="sail-bar" class="hidden flex-col md:flex-row items-stretch" action="{{ route('search.sail') }}" method="POST">
                    @csrf
                    <div class="flex-[2] flex flex-col px-6 py-5 border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
                        <label class="text-lg font-semibold text-[#63360D] mb-1">Departure Date</label>
                        <input type="text" id="sail-date" name="departure_date" class="search-input cursor-pointer font-medium text-black" placeholder="Select Date..." readonly>
                    </div>
                    <div class="flex-1 flex flex-col px-6 py-5 border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
                        <label class="text-lg font-semibold text-[#63360D] mb-1">Duration</label>
                        <select class="search-input font-medium text-black" id="hero-boat-duration" name="duration" required>
                            <option value="half">Half Day (4h)</option>
                            <option value="full">Full Day (8h)</option>
                            <option value="overnight">Overnight</option>
                        </select>
                    </div>
                    <div class="flex-1 flex flex-col px-6 py-5 border-b md:border-b-0 md:border-r border-gray-100 hover:bg-gray-50 transition-colors">
                        <label class="text-lg font-semibold text-[#63360D] mb-1">Passengers</label>
                        <select class="search-input font-medium text-black" id="hero-boat-passengers" name="passengers" required>
                            @for($i=1;$i<=20;$i++)
                                <option value="{{ $i }}">{{ $i }} Passenger{{ $i>1?'s':'' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-center px-6 py-4">
                        <button type="submit" class="bg-[#63360D] text-white px-8 py-3 text-md font-semibold flex items-center gap-1 justify-center whitespace-nowrap hover:bg-[#4D290A] transition shadow-xl shadow-orange-900/10">
                            <span class="material-symbols-outlined text-md">sailing</span> Find Vessel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <section class="py-16 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 relative">
            <div class="relative z-10">
                <div class="flex flex-col items-center mb-16" data-reveal>
                    <p class="text-md font-semibold text-[#A15D1A] mb-2">What we offer</p>
                    <h2 class="text-6xl font-extrabold text-[#63360D] text-center">World-class services</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-0 border border-gray-100 mb-10">
                    @php
                        $services = [
                            ['title' => 'Restaurant', 'icon' => 'restaurant', 'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=800'],
                            ['title' => 'Luxurious rooms', 'icon' => 'king_bed', 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=800'],
                            ['title' => 'Inside pool', 'icon' => 'pool', 'image' => 'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?q=80&w=800'],
                            ['title' => '24h service', 'icon' => 'concierge', 'image' => 'https://images.unsplash.com/photo-1563330232-57114bb0823c?q=80&w=800'],
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

   
    {{-- Content --}}
    <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-0 items-center">
            
            {{-- Left: Image with Overlapping Badge --}}
            <div class="lg:col-span-5 relative" data-reveal>
                <div class="relative h-[600px] overflow-hidden shadow-2xl transform-gpu">
                    <img src="{{ asset('images/1758952332.jpg') }}"
                         class="w-full h-full object-cover transition-transform duration-700 hover:scale-105 transform-gpu"
                         loading="lazy" decoding="async"
                         alt="Cabanas Resort Heritage" />
                </div>
                {{-- Overlapping Badge --}}
                <div class="absolute top-1/2 -right-4 lg:-right-20 -translate-y-1/2 bg-white px-8 lg:px-14 py-4 lg:py-6 shadow-xl z-20 border border-gray-50 hidden md:block">
                    <p class="text-xl lg:text-3xl font-bold text-black tracking-tight">About Us</p>
                </div>
            </div>

            {{-- Right: Content Area --}}
            <div class="lg:col-span-7 lg:pl-32 py-12" data-reveal data-reveal-delay="2">
                <h2 class="text-4xl lg:text-6xl font-extrabold text-black mb-8 leading-[1.2]">
                    We Invite Guests To<br>
                    <span class="text-[#63360D]">Celebrate Life</span>
                </h2>

                <p class="text-black font-medium text-lg leading-relaxed mb-10">
                    The Cabanas Family Resort is a family-owned getaway built on legacy land. Nestled in the heart of Tambobong, Dasol, we invite you to share in our hidden paradise where every moment becomes a cherished memory.
                </p>

                {{-- Feature Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 mb-12">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-black text-xl">star</span>
                        <span class="text-lg font-semibold text-black tracking-wider">15 Premium Room types</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-black text-xl">star</span>
                        <span class="text-lg font-semibold text-black tracking-wider">5-Star Guest rating</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-black text-xl">star</span>
                        <span class="text-lg font-semibold text-black tracking-wider">Well Garden Area</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-black text-xl">star</span>
                        <span class="text-lg font-semibold text-black tracking-wider">Hidden Beach Paradise</span>
                    </div>
                </div>

                <a href="{{ url('/home/amenities') }}"
                   class="inline-block bg-[#63360D] text-white px-10 py-3 text-lg font-semibold transition-all hover:bg-[#261405] hover:-translate-y-1">
                    Read More
                </a>
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
