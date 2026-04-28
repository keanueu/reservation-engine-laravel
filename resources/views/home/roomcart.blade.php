@extends('home.layouts.app')
@section('content')

  @php
    use App\Models\Room;
    $placeholderImage = "https://placehold.co/400x300/f3f4f6/1f2937?text=Image";
  @endphp

  <div class="relative w-full h-[35vh] md:h-[45vh]">
    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
      alt="Luxury Beach Resort" class="absolute inset-0 object-cover w-full h-full">
    <div class="relative z-10 flex items-end justify-center w-full h-full bg-black bg-opacity-50 px-4 pb-12 md:pb-16">
      <h1
        class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl  text-white text-center font-[Manrope] tracking-wide">
        Explore Our Offerings
      </h1>
    </div>
  </div>

  <div class="max-w-6xl mx-auto px-6 py-10">

    <div class="lg:hidden space-y-2 mb-8">
      <a href="{{ url('/') }}"
        class="flex items-center text-sm text-black hover:text-yellow-700 transition font-[Manrope]">
        <svg class="h-4 w-4 mr-1 transition" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Back to Home
      </a>

      <h1 class="text-2xl sm:text-3xl font-normal tracking-wide text-black font-[Manrope]">
        Review and Select
      </h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

      <div class="lg:col-span-1 lg:order-2">
        <div id="cart-summary" class="border border-gray-200 p-6 sticky top-28 bg-white shadow-sm ">

          @php
            $cart = session('cart', []);
            $roomIds = collect($cart)->pluck('room_id')->all();
            $cartRooms = App\Models\Room::whereIn('id', $roomIds)->get();
            // Attach cart data to each room for display
            foreach ($cartRooms as $room) {
              $room->cart_data = collect($cart)->firstWhere('room_id', $room->id);
            }
          @endphp
          @include('home.partials.cart-summary', ['cartRooms' => $cartRooms])
        </div>
      </div>

        <div class="lg:col-span-2 lg:order-1 space-y-8" x-data="{ 
        tab: '{{ $searchParams['type'] === 'boat' ? 'boats' : 'rooms' }}', 
        roomFilter: 'all', 
        roomLocation: 'all', 
        roomPromo: 'all', 
        room_startDate: '{{ $searchParams['checkin'] ?? '' }}', 
        room_endDate: '{{ $searchParams['checkout'] ?? '' }}', 
        room_checkin_time: '{{ $searchParams['checkin_time'] ?? '' }}',
        room_checkout_time: '{{ $searchParams['checkout_time'] ?? '' }}',
        room_adults: {{ $searchParams['guests'] ?? 1 }}, 
        room_children: 0,
        // Boat booking defaults
        boat_booking_date: '{{ $searchParams['departure_date'] ?? '' }}',
        boat_guests: {{ $searchParams['passengers'] ?? 1 }},
        boat_start_time: '',
        boat_end_time: '',
        boat_duration: '{{ $searchParams['duration'] ?? '' }}',
        filtersOpen: false 
      }">


        <div class="hidden lg:block space-y-2">
          <a href="{{ url('/') }}"
            class="flex items-center text-sm font-[Manrope] text-black hover:text-yellow-700 transition">
            <svg class="h-4 w-4 mr-1 transition" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Home
          </a>
          <h1 class="text-2xl sm:text-3xl font-normal tracking-wide text-black font-[Manrope]">
            Review & Select
          </h1>
        </div>

        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button @click="tab = 'rooms'"
              :class="{ 'border-[#964B00] text-[#964B00]': tab === 'rooms', 'border-transparent text-black hover:text-black  hover:border-gray-300': tab !== 'rooms' }"
              class="whitespace-nowrap py-4 px-1 border-b-2 font-normal font-[Manrope] text-md transition-colors duration-200">
              Book Your Stay
            </button>
            <button @click="tab = 'boats'"
              :class="{ 'border-[#964B00] text-[#964B00]': tab === 'boats', 'border-transparent text-black hover:text-black  hover:border-gray-300': tab !== 'boats' }"
              class="whitespace-nowrap py-4 px-1 border-b-2 font-normal font-[Manrope] text-md transition-colors duration-200">
              Book an Adventure
            </button>
          </nav>
        </div>

        @include('home.cart.room-tab')

        @include('home.cart.boat-tab')

        @include('home.partials.promo-v2')

      </div>
    </div>
  </div>

@endsection
