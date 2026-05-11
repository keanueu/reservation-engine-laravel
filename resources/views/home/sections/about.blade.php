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
            <div class="bg-[#261405] text-white p-12 flex flex-col items-center text-center">
                <div class="mb-4">
                    <span class="material-symbols-outlined text-6xl">schedule</span>
                </div>
                <h3 class="text-2xl font-medium mb-4 text-white">Check-in & Check-out Time</h3>
                <p class="text-white text-md leading-relaxed max-w-xs">It is a long established fact that a reader will be distracted</p>
            </div>

            {{-- Card 2 --}}
            <div class="bg-[#63360D] text-white p-12 flex flex-col items-center text-center">
                <div class="mb-4">
                    <span class="material-symbols-outlined text-6xl">wifi</span>
                </div>
                <h3 class="text-2xl font-medium mb-4 text-white">High Speed Internet</h3>
                <p class="text-white text-md leading-relaxed max-w-xs">It is a long established fact that a reader will be distracted</p>
            </div>

            {{-- Card 3 --}}
            <div class="bg-[#A15D1A] text-white p-12 flex flex-col items-center text-center">
                <div class="mb-4">
                    <span class="material-symbols-outlined text-6xl">calendar_month</span>
                </div>
                <h3 class="text-2xl font-medium mb-4 text-white">Simple Booking</h3>
                <p class="text-white text-md leading-relaxed max-w-xs">It is a long established fact that a reader will be distracted</p>
            </div>

            {{-- Card 4 --}}
            <div class="bg-[#B87431] text-white p-12 flex flex-col items-center text-center">
                <div class="mb-4">
                    <span class="material-symbols-outlined text-6xl">support_agent</span>
                </div>
                <h3 class="text-2xl font-medium mb-4 text-white">Helpful Staff</h3>
                <p class="text-white text-md leading-relaxed max-w-xs">It is a long established fact that a reader will be distracted</p>
            </div>
        </div>
    </div>

    {{-- Rest of Content in White Background --}}
    <div class="bg-white py-32">
        {{-- Search Form Section --}}
        <div class="max-w-7xl mx-auto px-6 -mt-16 relative z-40 mb-20">
            {{-- Search bar container --}}
            <div class="w-full bg-white shadow-2xl overflow-hidden mb-6">
                {{-- Tabs --}}
                <div class="flex border-b border-gray-100">
                    <style>
                        .booking-tab.active { border-bottom-color: #A15D1A !important; color: #A15D1A !important; background: #fdfaf7; }
                    </style>
                    <button id="tab-stay" class="booking-tab flex-1 py-5 text-xs font-bold flex items-center justify-center gap-2 border-b-2 border-transparent transition-all active" onclick="setMode('stay')">
                        <span class="material-symbols-outlined text-base">home</span> Stay
                    </button>
                    <button id="tab-sail" class="booking-tab flex-1 py-5 text-xs font-bold flex items-center justify-center gap-2 border-b-2 border-transparent transition-all" onclick="setMode('sail')">
                        <span class="material-symbols-outlined text-base">sailing</span> Sail
                    </button>
                </div>

                {{-- Stay bar --}}
                <form id="stay-bar" class="flex flex-col md:flex-row items-stretch" action="{{ route('search.stay') }}" method="POST">
                    @csrf
                    <div class="flex-[2] flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                        <label class="text-[10px] font-bold text-gray-400 mb-1">Stay Duration</label>
                        <input type="text" id="stay-range" name="date_range" class="search-input cursor-pointer" placeholder="Select Dates..." readonly>
                    </div>
                    <div class="flex-1 flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                        <label class="text-[10px] font-bold text-gray-400 mb-1">Check-in Time</label>
                        <select name="checkin_time" class="search-input minimal-select bg-transparent cursor-pointer">
                            @for($h=8;$h<=20;$h++)
                                <option value="{{ sprintf('%02d:00',$h) }}">{{ date('h:i A', strtotime("$h:00")) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex-1 flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                        <label class="text-[10px] font-bold text-gray-400 mb-1">Check-out Time</label>
                        <select name="checkout_time" class="search-input minimal-select bg-transparent cursor-pointer">
                            @for($h=8;$h<=20;$h++)
                                <option value="{{ sprintf('%02d:00',$h) }}">{{ date('h:i A', strtotime("$h:00")) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex-1 flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                        <label class="text-[10px] font-bold text-gray-400 mb-1">Guests</label>
                        <select class="search-input" id="hero-guests" name="guests">
                            @for($i=1;$i<=10;$i++)
                                <option value="{{ $i }}">{{ $i }} Guest{{ $i>1?'s':'' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <button type="submit" class="bg-[#63360D] text-white px-8 py-3.5 text-xs font-bold flex items-center gap-2 justify-center whitespace-nowrap hover:bg-[#4D290A] transition">
                            <span class="material-symbols-outlined text-base">search</span> Search
                        </button>
                    </div>
                </form>

                {{-- Sail bar --}}
                <form id="sail-bar" class="hidden flex-col md:flex-row items-stretch" action="{{ route('search.sail') }}" method="POST">
                    @csrf
                    <div class="flex-[2] flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                        <label class="text-[10px] font-bold text-gray-400 mb-1">Departure Date</label>
                        <input type="text" id="sail-date" name="departure_date" class="search-input cursor-pointer" placeholder="Select Date..." readonly>
                    </div>
                    <div class="flex-1 flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                        <label class="text-[10px] font-bold text-gray-400 mb-1">Duration</label>
                        <select class="search-input" id="hero-boat-duration" name="duration" required>
                            <option value="half">Half Day (4h)</option>
                            <option value="full">Full Day (8h)</option>
                            <option value="overnight">Overnight</option>
                        </select>
                    </div>
                    <div class="flex-1 flex flex-col px-5 py-4 border-b md:border-b-0 md:border-r border-gray-100">
                        <label class="text-[10px] font-bold text-gray-400 mb-1">Passengers</label>
                        <select class="search-input" id="hero-boat-passengers" name="passengers" required>
                            @for($i=1;$i<=20;$i++)
                                <option value="{{ $i }}">{{ $i }} Passenger{{ $i>1?'s':'' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-center px-4 py-3">
                        <button type="submit" class="bg-[#63360D] text-white px-8 py-3.5 text-xs font-bold flex items-center gap-2 justify-center whitespace-nowrap hover:bg-[#4D290A] transition">
                            <span class="material-symbols-outlined text-base">sailing</span> Find Vessel
                        </button>
                    </div>
                </form>

                {{-- Trust Badges Bar --}}
                <div class="bg-gray-50 px-6 py-2 flex items-center gap-6 border-t border-gray-100">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-green-600" style="font-size: 14px;">verified</span>
                        <span class="text-[10px] font-bold text-gray-500">Verified Resort</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-gray-400" style="font-size: 14px;">lock</span>
                        <span class="text-[10px] font-bold text-gray-500">Secure Booking</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-gray-400" style="font-size: 14px;">credit_card</span>
                        <span class="text-[10px] font-bold text-gray-500">PayMongo Payments</span>
                    </div>
                </div>

                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="bg-red-50 px-6 py-3 border-t border-red-100">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li class="text-[10px] font-bold text-red-600">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none">
        <img src="{{ asset('LOGO-FINAL.png') }}"
             class="w-[900px] h-[900px] object-contain opacity-[0.15]"
             aria-hidden="true" />
    </div>

    {{-- Content --}}
    <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- Left: Text --}}
            <div data-reveal>
                <p class="text-sm font-semibold mb-4 section-label">About us</p>

                <h2 class="text-4xl md:text-5xl font-bold leading-[1.15] tracking-tight mb-6 text-gray-900">
                    Welcome to Cabanas<br>
                    Beach Resort &amp; Hotel
                </h2>

                <p class="text-base text-gray-600 leading-relaxed mb-8 max-w-md">
                    The Cabanas Family Resort is a family-owned getaway built on land that has
                    been passed down through generations. Nestled in Tambobong, Dasol,
                    Pangasinan, we take pride in sharing our hidden gem with you.
                </p>

                {{-- Stats row --}}
                <div class="grid grid-cols-3 gap-4 mb-8">
                    @php $stats = [['15+','Room Types'],['5★','Guest Rating'],['10+','Years Open']]; @endphp
                    @foreach($stats as [$val,$lbl])
                        <div class="border border-gray-200 px-4 py-4 text-center shadow-sm bg-white">
                            <p class="text-2xl font-bold mb-1" style="color:#63360D;">{{ $val }}</p>
                            <p class="text-xs font-semibold text-gray-500">{{ $lbl }}</p>
                        </div>
                    @endforeach
                </div>

                <a href="{{ url('/home/amenities') }}"
                   class="inline-flex items-center gap-2 text-sm font-bold text-gray-900 border-b-2 pb-1 transition-all hover:text-[#63360D] hover:border-[#63360D] hover:gap-3"
                   style="border-color:#63360D;">
                    Learn More
                    <span class="material-symbols-outlined text-base">arrow_forward</span>
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
                        <p class="text-white text-xs font-semibold">Tambobong, Dasol</p>
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
