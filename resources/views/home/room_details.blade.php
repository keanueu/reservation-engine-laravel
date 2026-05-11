@extends('home.layouts.app')
@section('content')

    <div class="relative w-full h-[35vh] md:h-[45vh]">
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
            alt="Luxury Beach Resort" class="absolute inset-0 object-cover w-full h-full">
        <div class="relative z-10 flex items-end justify-center w-full h-full bg-black bg-opacity-50 px-4 pb-12 md:pb-16">
            <h1
                class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl  text-white text-center font-[Inter]">
                Our Exclusive Stays
            </h1>
        </div>
    </div>


    <section class="pt-12 pb-12">
        <div class="max-w-7xl mx-auto px-4" id="booking-section">

            <div class="pb-3 font-[Inter]">
                <a href="{{ url('/home/rooms') }}"
                    class="flex items-center text-sm text-gray-600 hover:text-gray-900 transition mb-1">
                    <span class="material-symbols-outlined mr-1" style="font-size: 16px;">arrow_back</span>
                    Back to Rooms
                </a>
                <h1 class="text-2xl md:text-3xl  text-gray-900">
                    {{ $room->room_name ?? 'Room Details' }}
                </h1>
            </div>

            {{-- We keep the max-w-7xl alignment from the parent div. --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                <div class="lg:col-span-2 flex flex-col space-y-8">

                    @php
                        $placeholderImage = "https://placehold.co/800x600/f3f4f6/1f2937?text=Room+Image";
                        $imageUrls = [];
                        if (isset($room->images) && $room->images->isNotEmpty()) {
                            foreach ($room->images as $img) {
                                $imageUrls[] = asset('room/' . ($img->image ?? 'placeholder.jpg'));
                            }
                        } else {
                            $imageUrls[] = asset('room/' . ($room->image ?? 'placeholder.jpg'));
                        }
                    @endphp

                    {{-- Increased height to h-[400px] --}}
                    <div class="h-[400px] overflow-hidden relative shadow-xl"
                        x-data="{ idx: 0, imagesCount: {{ count($imageUrls) }}, timer: null, next(){ this.idx = (this.idx + 1) % this.imagesCount }, prev(){ this.idx = (this.idx - 1 + this.imagesCount) % this.imagesCount }, go(i){ this.idx = i }, pause(){ if(this.timer){ clearInterval(this.timer); this.timer = null } }, play(){ if(!this.timer){ this.timer = setInterval(()=> this.next(), 4000) } } }"
                        x-init="play()" @mouseenter="pause()" @mouseleave="play()">
                        @foreach($imageUrls as $i => $url)
                            {{-- Use object-cover and w-full h-full to eliminate internal whitespace --}}
                            <img x-show="idx === {{ $i }}" src="{{ $url }}" alt="{{ $room->room_name }} - image {{ $i + 1 }}"
                                onerror="this.onerror=null;this.src='{{ $placeholderImage }}'"
                                class="absolute inset-0 w-full h-full object-cover transition duration-500" />
                        @endforeach

                        {{-- Next/Prev Arrows Added Here --}}

                        {{-- Previous Button --}}
                        <button @click.prevent="prev()"
                            class="absolute left-4 top-1/2 transform -translate-y-1/2 z-30 p-2 bg-black/30 hover:bg-black/60 text-white  transition duration-300">
                            <span class="material-symbols-outlined" style="font-size: 24px;">chevron_left</span>
                        </button>

                        {{-- Next Button --}}
                        <button @click.prevent="next()"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 z-30 p-2 bg-black/30 hover:bg-black/60 text-white  transition duration-300">
                            <span class="material-symbols-outlined" style="font-size: 24px;">chevron_right</span>
                        </button>

                        {{-- End of Arrows --}}

                        <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/70 to-transparent">
                        </div>

                        {{-- Dots (No Changes) --}}
                        <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 z-20 flex items-center gap-2">
                            @for($i = 0; $i < count($imageUrls); $i++)
                                <button @click.prevent="go({{ $i }})"
                                    :class="{ 'bg-white': idx === {{ $i }}, 'bg-white/40': idx !== {{ $i }} }"
                                    class="w-2 h-2  transition"></button>
                            @endfor
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl  font-[Inter] text-gray-900 pb-2 flex items-center gap-2">
                            Description
                        </h2>
                        <p class="mt-2 text-gray-700 font-[Inter] text-sm  leading-relaxed">
                            {{ $room->description }}
                        </p>
                    </div>

                </div>

                <div class="lg:col-span-1 flex flex-col space-y-8">

                    {{-- Sticky Booking Widget --}}
                    <div class="sticky top-24 p-6 bg-white shadow-lg border border-gray-200"
                         x-data="roomBookingWidget({{ $room->id }}, {{ (float) $room->price }})">

                        {{-- Price header --}}
                        <div class="flex items-end justify-between mb-4 pb-4 border-b border-gray-100">
                            <div>
                                <p class="text-xs font-bold text-gray-400">Price</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">
                                    PHP {{ number_format($room->price, 2) }}
                                    <span class="text-sm font-normal text-gray-400">/ night</span>
                                </p>
                            </div>
                            {{-- Availability badge --}}
                            <div>
                                <span x-show="status === 'idle'" class="flex items-center gap-1 text-xs font-semibold text-gray-400">
                                    <span class="material-symbols-outlined" style="font-size:16px;">calendar_month</span>
                                    Select dates
                                </span>
                                <span x-show="status === 'checking'" class="flex items-center gap-1 text-xs font-semibold text-amber-500">
                                    <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                    Checking...
                                </span>
                                <span x-show="status === 'available'" class="flex items-center gap-1 text-xs font-semibold text-green-600">
                                    <span class="material-symbols-outlined" style="font-size:16px;">check_circle</span>
                                    Available
                                </span>
                                <span x-show="status === 'unavailable'" class="flex items-center gap-1 text-xs font-semibold text-red-600">
                                    <span class="material-symbols-outlined" style="font-size:16px;">cancel</span>
                                    Not available
                                </span>
                                <span x-show="status === 'error'" class="flex items-center gap-1 text-xs font-semibold text-gray-400">
                                    <span class="material-symbols-outlined" style="font-size:16px;">warning</span>
                                    Check failed
                                </span>
                            </div>
                        </div>

                        {{-- Date inputs --}}
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="border border-gray-200 px-3 py-2.5">
                                <p class="text-[10px] font-bold text-gray-400 mb-1">Check-in</p>
                                <input type="date" x-model="checkin"
                                       :min="today"
                                       @change="onCheckinChange"
                                       class="w-full text-xs text-gray-700 bg-transparent outline-none cursor-pointer">
                            </div>
                            <div class="border border-gray-200 px-3 py-2.5">
                                <p class="text-[10px] font-bold text-gray-400 mb-1">Check-out</p>
                                <input type="date" x-model="checkout"
                                       :min="minCheckout"
                                       @change="runChecks"
                                       class="w-full text-xs text-gray-700 bg-transparent outline-none cursor-pointer">
                            </div>
                        </div>

                        {{-- Time inputs --}}
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="border border-gray-200 px-3 py-2.5">
                                <p class="text-[10px] font-bold text-gray-400 mb-1">Check-in time</p>
                                <input type="time" x-model="checkinTime"
                                       class="w-full text-xs text-gray-700 bg-transparent outline-none cursor-pointer">
                            </div>
                            <div class="border border-gray-200 px-3 py-2.5">
                                <p class="text-[10px] font-bold text-gray-400 mb-1">Check-out time</p>
                                <input type="time" x-model="checkoutTime"
                                       class="w-full text-xs text-gray-700 bg-transparent outline-none cursor-pointer">
                            </div>
                        </div>

                        {{-- Price breakdown --}}
                        <div x-show="nights > 0" class="mb-4 p-3 bg-gray-50 border border-gray-100 text-xs space-y-1.5">

                            {{-- Loading state --}}
                            <div x-show="priceStatus === 'loading'" class="flex items-center gap-2 text-amber-500 py-1">
                                <svg class="animate-spin w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                </svg>
                                <span>Calculating price...</span>
                            </div>

                            {{-- Dynamic pricing badge --}}
                            <div x-show="priceStatus === 'done' && hasDynamicPricing"
                                 class="flex items-center gap-1 text-[#964B00] mb-2">
                                <span class="material-symbols-outlined" style="font-size:14px;">local_offer</span>
                                <span class="font-bold">Special rate applied</span>
                            </div>

                            {{-- Applied rules summary --}}
                            <template x-if="priceStatus === 'done' && appliedRules.length > 0">
                                <div class="space-y-1 mb-2">
                                    <template x-for="rule in appliedRules" :key="rule.label">
                                        <div class="flex justify-between text-[#964B00]">
                                            <span x-text="rule.label"></span>
                                            <span x-text="formatCurrency(rule.price) + '/night'"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Base rate line --}}
                            <div x-show="priceStatus === 'done'" class="flex justify-between text-gray-600">
                                <span x-text="'Base rate'"></span>
                                <span x-text="formatCurrency(pricePerNight) + '/night'"></span>
                            </div>

                            {{-- Nights × rate --}}
                            <div x-show="priceStatus === 'done'" class="flex justify-between text-gray-600">
                                <span x-text="nights + ' night' + (nights > 1 ? 's' : '')"></span>
                                <span x-text="formatCurrency(subtotal)"></span>
                            </div>

                            {{-- Total --}}
                            <div x-show="priceStatus === 'done'"
                                 class="flex justify-between font-bold text-gray-900 pt-1.5 border-t border-gray-200">
                                <span>Total</span>
                                <span x-text="formatCurrency(subtotal)"></span>
                            </div>

                            <p class="text-gray-400 text-[10px]">Deposit (50%) charged at checkout</p>
                        </div>

                        {{-- Unavailability panel with blocked dates --}}
                        <div x-show="availStatus === 'unavailable'" class="mb-3 text-xs">

                            {{-- Header --}}
                            <div class="flex items-center gap-2 p-3 bg-red-50 border border-red-200 text-red-700 mb-2">
                                <span class="material-symbols-outlined flex-shrink-0" style="font-size:16px;">event_busy</span>
                                <span x-text="availMessage"></span>
                            </div>

                            {{-- Blocked ranges list --}}
                            <template x-if="blockedRanges.length > 0">
                                <div class="space-y-1 mb-2">
                                    <p class="text-gray-500 mb-1">Conflicting reservations:</p>
                                    <template x-for="(range, i) in blockedRanges" :key="i">
                                        <div class="flex items-center gap-2 px-3 py-2 bg-red-50 border border-red-100 text-red-700">
                                            <span class="material-symbols-outlined flex-shrink-0" style="font-size:14px;">block</span>
                                            <span x-text="range.label"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Blocked dates chips --}}
                            <template x-if="blockedDates.length > 0">
                                <div>
                                    <p class="text-gray-500 mb-1.5">Blocked dates in your range:</p>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="date in blockedDates" :key="date">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-semibold">
                                                <span class="material-symbols-outlined" style="font-size:11px;">close</span>
                                                <span x-text="new Date(date + 'T00:00:00').toLocaleDateString('en-PH', { month: 'short', day: 'numeric' })"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <p class="text-gray-500 mt-2">Please select different dates to continue.</p>
                        </div>

                        {{-- CTA Button --}}
                        <a :href="bookingUrl"
                           :class="{
                               'opacity-50 pointer-events-none cursor-not-allowed': availStatus === 'unavailable' || availStatus === 'checking',
                               'btn-primary': availStatus === 'available' || availStatus === 'idle'
                           }"
                           class="w-full btn-primary py-3.5 text-sm font-bold flex items-center justify-center gap-2">
                            <span x-show="availStatus === 'checking' || priceStatus === 'loading'">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                </svg>
                            </span>
                            <span class="material-symbols-outlined"
                                  x-show="availStatus !== 'checking' && priceStatus !== 'loading'"
                                  style="font-size:16px;">calendar_month</span>
                            <span x-text="(availStatus === 'checking' || priceStatus === 'loading') ? 'Checking...' : 'Book This Room'"></span>
                        </a>

                        <p class="text-center text-xs text-gray-400 mt-3">No charge until checkout</p>

                        {{-- Trust signals --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span class="material-symbols-outlined text-[#964B00] flex-shrink-0" style="font-size:16px;">verified</span>
                                Verified & Secure Booking
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span class="material-symbols-outlined text-[#964B00] flex-shrink-0" style="font-size:16px;">lock</span>
                                Free cancellation policy
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span class="material-symbols-outlined text-[#964B00] flex-shrink-0" style="font-size:16px;">credit_card</span>
                                PayMongo secure payment
                            </div>
                        </div>
                    </div>
                    <div class="p-5 bg-white shadow-sm border border-gray-300 font-[Inter]">
                        <h3 class="text-xl  text-gray-900 mb-4 flex items-center gap-2">
                            Properties
                        </h3>

                        <ul class="grid grid-cols-1 gap-y-3 text-xs text-gray-900">
                            <li class="flex items-center justify-between sm:justify-start sm:space-x-2">
                                <span class="material-symbols-outlined text-black mr-1.5" style="font-size: 20px;">group</span>
                                <span class=" text-gray-900 flex-1">Accommodates:</span>
                                <span class="">{{ $room->accommodates }} Guests</span>
                            </li>
                            <li class="flex items-center justify-between sm:justify-start sm:space-x-2">
                                <span class="material-symbols-outlined text-black mr-1.5" style="font-size: 20px;">bed</span>
                                <span class=" text-gray-900 flex-1">Beds:</span>
                                <span class="">{{ $room->beds }}</span>
                            </li>
                            <li class="flex items-center justify-between sm:justify-start sm:space-x-2">
                                <span class="material-symbols-outlined text-black mr-1.5" style="font-size: 20px;">login</span>
                                <span class=" text-gray-900 flex-1">Check-in:</span>
                                <span
                                    class="text-gray-900 ">{{ \Carbon\Carbon::parse($room->check_in)->format('h:i A') }}</span>
                            </li>
                            <li class="flex items-center justify-between sm:justify-start sm:space-x-2">
                                <span class="material-symbols-outlined text-black mr-1.5" style="font-size: 20px;">logout</span>
                                <span class=" text-gray-900 flex-1">Check-out:</span>
                                <span
                                    class="text-gray-900 ">{{ \Carbon\Carbon::parse($room->check_out)->format('h:i A') }}</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Amenities Section --}}
                    <div>
                        <h3 class="text-xl  text-gray-900 pb-2 flex items-center gap-2 font-[Inter]">
                            Amenities
                        </h3>

                        @if($room->amenities)
                            @php
                                $icons = [
                                    'Airconditioned' => '<span class="material-symbols-outlined text-black mr-1.5 inline-block align-middle" style="font-size: 20px;">ac_unit</span>',
                                    'Minibar' => '<span class="material-symbols-outlined text-black mr-1.5 inline-block align-middle" style="font-size: 20px;">local_bar</span>',
                                    'Shower' => '<span class="material-symbols-outlined text-black mr-1.5 inline-block align-middle" style="font-size: 20px;">shower</span>',
                                    'Bath' => '<span class="material-symbols-outlined text-black mr-1.5 inline-block align-middle" style="font-size: 20px;">bathtub</span>',
                                    'Kitchen' => '<span class="material-symbols-outlined text-black mr-1.5 inline-block align-middle" style="font-size: 20px;">kitchen</span>',
                                    'Balcony with sea view' => '<span class="material-symbols-outlined text-black mr-1.5 inline-block align-middle" style="font-size: 20px;">balcony</span>',
                                    'Work Space' => '<span class="material-symbols-outlined text-black mr-1.5 inline-block align-middle" style="font-size: 20px;">desk</span>',
                                    'Hot & Cold Shower' => '<span class="material-symbols-outlined text-black mr-1.5 inline-block align-middle" style="font-size: 20px;">shower</span>',
                                    'Kitchen with stove for free use' => '<span class="material-symbols-outlined text-black mr-1.5 inline-block align-middle" style="font-size: 20px;">stove</span>',
                                    'Refrigerator' => '<span class="material-symbols-outlined text-black mr-1.5 inline-block align-middle" style="font-size: 20px;">kitchen</span>',
                                ];
                            @endphp

                            <div class="grid grid-cols-2 md:grid-cols-1 xl:grid-cols-2 gap-2 mt-2 font-[Inter]">
                                @foreach(explode(',', $room->amenities) as $amenity)
                                    @php $name = trim($amenity); @endphp
                                    <span class="flex items-center gap-1 bg-white text-black text-xs px-3 py-1.5 border border-gray-200 shadow-sm hover:bg-gray-100">
                                        {!! $icons[$name] ?? '<span class="material-symbols-outlined text-black mr-1.5 inline-block align-middle" style="font-size: 20px;">check_circle</span>' !!}
                                        {{ $name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

<script>
function roomBookingWidget(roomId, pricePerNight) {
    return {
        roomId:        roomId,
        pricePerNight: pricePerNight,

        checkin:       '',
        checkout:      '',
        checkinTime:   '13:00',
        checkoutTime:  '11:00',

        nights:             0,
        subtotal:           0,
        hasDynamicPricing:  false,
        appliedRules:       [],
        breakdown:          [],

        // 'idle' | 'checking' | 'available' | 'unavailable' | 'error'
        availStatus:   'idle',
        // 'idle' | 'loading' | 'done' | 'error'
        priceStatus:   'idle',

        blockedDates:  [],   // ['Y-m-d', ...]
        blockedRanges: [],   // [{ start, end, label }, ...]
        availMessage:  '',

        debounceAvail: null,
        debouncePrice: null,

        get today() {
            return new Date().toISOString().split('T')[0];
        },

        get minCheckout() {
            if (!this.checkin) return this.today;
            const d = new Date(this.checkin);
            d.setDate(d.getDate() + 1);
            return d.toISOString().split('T')[0];
        },

        get status() { return this.availStatus; }, // alias for badge

        get bookingUrl() {
            const base = '{{ route("booking.dates") }}';
            if (!this.checkin || !this.checkout) return base;
            const p = new URLSearchParams({
                type:          'room',
                room_id:       this.roomId,
                checkin:       this.checkin,
                checkout:      this.checkout,
                checkin_time:  this.checkinTime,
                checkout_time: this.checkoutTime,
            });
            return base + '?' + p.toString();
        },

        onCheckinChange() {
            if (this.checkout && this.checkout <= this.checkin) {
                this.checkout = '';
                this.reset();
            }
            if (this.checkout) this.runChecks();
        },

        reset() {
            this.nights            = 0;
            this.subtotal          = 0;
            this.hasDynamicPricing = false;
            this.appliedRules      = [];
            this.breakdown         = [];
            this.availStatus       = 'idle';
            this.priceStatus       = 'idle';
            this.blockedDates      = [];
            this.blockedRanges     = [];
            this.availMessage      = '';
        },

        runChecks() {
            if (!this.checkin || !this.checkout) { this.reset(); return; }
            this.checkAvailability();
            this.fetchPricing();
        },

        // -------------------------------------------------------
        // Availability check (debounced 400ms)
        // -------------------------------------------------------
        checkAvailability() {
            clearTimeout(this.debounceAvail);
            this.availStatus = 'checking';

            this.debounceAvail = setTimeout(async () => {
                try {
                    const params = new URLSearchParams({
                        room_id:    this.roomId,
                        start_date: this.checkin,
                        end_date:   this.checkout,
                        start_time: this.checkinTime,
                        end_time:   this.checkoutTime,
                    });
                    const res  = await fetch('/check-room-availability?' + params, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    });
                    const data = await res.json();

                    if (data.available) {
                        this.availStatus   = 'available';
                        this.blockedDates  = [];
                        this.blockedRanges = [];
                        this.availMessage  = '';
                    } else {
                        this.availStatus   = 'unavailable';
                        this.blockedDates  = data.blocked_dates  || [];
                        this.blockedRanges = data.blocked_ranges || [];
                        this.availMessage  = data.message        || 'Room is not available for the selected dates.';
                    }
                } catch (e) {
                    this.availStatus = 'error';
                }
            }, 400);
        },

        // -------------------------------------------------------
        // Dynamic pricing fetch (debounced 500ms)
        // -------------------------------------------------------
        async fetchPricing() {
            clearTimeout(this.debouncePrice);
            this.priceStatus = 'loading';

            this.debouncePrice = setTimeout(async () => {
                try {
                    const params = new URLSearchParams({
                        room_id:  this.roomId,
                        checkin:  this.checkin,
                        checkout: this.checkout,
                    });
                    const res  = await fetch('/room-pricing?' + params, {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                    const data = await res.json();

                    if (data.error) throw new Error(data.error);

                    this.nights            = data.nights;
                    this.subtotal          = data.total;
                    this.hasDynamicPricing = data.has_dynamic_pricing;
                    this.appliedRules      = data.applied_rules  || [];
                    this.breakdown         = data.breakdown      || [];
                    this.priceStatus       = 'done';
                } catch (e) {
                    // Fallback to client-side calculation
                    const diff = (new Date(this.checkout) - new Date(this.checkin)) / 86400000;
                    this.nights   = diff > 0 ? Math.round(diff) : 0;
                    this.subtotal = this.nights * this.pricePerNight;
                    this.hasDynamicPricing = false;
                    this.appliedRules      = [];
                    this.breakdown         = [];
                    this.priceStatus       = 'done';
                }
            }, 500);
        },

        formatCurrency(val) {
            return 'PHP ' + parseFloat(val).toLocaleString('en-PH', { minimumFractionDigits: 2 });
        },
    };
}
</script>
@endsection
