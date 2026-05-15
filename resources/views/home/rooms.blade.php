@extends('home.layouts.app')
@section('content')

    <div class="relative w-full h-[35vh] md:h-[45vh]">
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
            alt="Luxury Beach Resort" class="absolute inset-0 object-cover w-full h-full">
        <div class="relative z-10 flex items-end justify-center w-full h-full bg-black bg-opacity-50 px-4 pb-12 md:pb-16">
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl text-white text-center font-[Inter] font-medium">
                Our exclusive stays
            </h1>
        </div>
    </div>

@php
  // Existing PHP logic retained to handle data structure
  if (!isset($rooms) && isset($room)) {
    $rooms = $room;
  }
  $rooms = $rooms ?? collect();
  $placeholderImage = "https://placehold.co/400x300/f3f4f6/1f2937?text=Minimal+Suite";
@endphp

<div class="bg-white py-8 font-[Inter]" x-data="{ roomFilter: 'all', roomLocation: 'all' }" x-cloak>
  <div class="text-center mb-12">
      <p class="text-sm font-medium mb-4 section-label">Our accommodation</p>
      <h2 class="text-4xl md:text-5xl font-medium leading-relaxed] text-black">
        Exclusive stays
      </h2>
      <p class="text-base text-black leading-relaxed mt-4 max-w-2xl mx-auto">
       Discover the perfect space for your getaway, designed for comfort, relaxation, and lasting memories.
      </p>
    </div>

  <div class="max-w-7xl mx-auto px-6">

    <!-- Filters -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="md:flex-1">
          <div class="text-sm font-medium text-black mb-2">Filter by category</div>
          <div class="mt-2 -mx-6 px-6 md:mx-0 md:px-0">
            <div class="flex items-center gap-2 overflow-x-auto md:overflow-visible py-2">
              <button @click.prevent="roomFilter='all'" :class="{ 'bg-[#964B00] text-white': roomFilter === 'all', 'bg-white text-black': roomFilter !== 'all' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">All</button>
              <button @click.prevent="roomFilter='regular'" :class="{ 'bg-[#964B00] text-white': roomFilter === 'regular', 'bg-white text-black': roomFilter !== 'regular' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">Regular</button>
              <button @click.prevent="roomFilter='premium'" :class="{ 'bg-[#964B00] text-white': roomFilter === 'premium', 'bg-white text-black': roomFilter !== 'premium' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">Premium</button>
              <button @click.prevent="roomFilter='deluxe'" :class="{ 'bg-[#964B00] text-white': roomFilter === 'deluxe', 'bg-white text-black': roomFilter !== 'deluxe' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">Deluxe</button>
            </div>
          </div>
        </div>

        <div class="mt-4 md:mt-0 md:ml-6 md:flex-none">
          <div class="text-sm font-medium text-black text-right md:text-right mb-2">Filter by location</div>
          <div class="mt-2 -mx-6 px-6 md:mx-0 md:px-0">
            <div class="flex items-center gap-2 justify-end overflow-x-auto md:overflow-visible py-2">
              <button @click.prevent="roomLocation='all'" :class="{ 'bg-indigo-600 text-white': roomLocation === 'all', 'bg-white text-black': roomLocation !== 'all' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">All locations</button>
              <button @click.prevent="roomLocation='beach'" :class="{ 'bg-indigo-600 text-white': roomLocation === 'beach', 'bg-white text-black': roomLocation !== 'beach' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">Beach front</button>
              <button @click.prevent="roomLocation='nonbeach'" :class="{ 'bg-indigo-600 text-white': roomLocation === 'nonbeach', 'bg-white text-black': roomLocation !== 'nonbeach' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">Non beach front</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">

      @foreach ($rooms as $room)
        @php
          $type = strtolower($room->room_type ?? '');
          $beachFrontNames = [
            'Beach Front - Beach Front Kubo - Terrace Cabana',
            'Beach Front Kubo Units - Room B or C',
            'Beach Front Kubo - Twin Cabana',
            'Beach Front Kubo - Family Cabana',
            'Vacation House',
            'Britannia Room w/ Sand Kubo',
            'Beach Front-Penthouse Suite w/ Sand Kubo',
            'Beach Front - VIP STUDIO w/ Open Cabana',
          ];
          $nonBeachFrontNames = [
            'Luxury Unit - King Suite w/ Sand Kubo',
            'Luxury Unit - Queen Suite w/ Sand Kubo',
            'Luxury Unit - Mini Queen w/ Sand Kubo',
            'Lovers Room w/ Sand Kubo',
            'Sunset Rooftop w/ Sand Kubo',
          ];
          $location = in_array($room->room_name, $beachFrontNames) ? 'beach' : (in_array($room->room_name, $nonBeachFrontNames) ? 'nonbeach' : 'other');

          // Discount Logic
          $discount = $room->discounts->first();
          $discountValue = optional($discount)->amount;
          $isPercentage = optional($discount)->amount_type === 'percent' || optional($discount)->amount_type === 'percentage';
          $isFixedAmount = optional($discount)->amount_type === 'fixed';
          $isActive = optional($discount)->active;
          $expiryDate = optional($discount)->end_date;

          $discountedPrice = $room->price;
          if ($isActive && $discountValue > 0) {
              if ($isPercentage) {
                  $discountedPrice = $room->price * (1 - ($discountValue / 100));
              } elseif ($isFixedAmount) {
                  $discountedPrice = max(0, $room->price - $discountValue);
              }
          }

          $imageUrls = [];
          if(isset($room->images) && $room->images->isNotEmpty()){
              foreach($room->images as $img){
                  $imageUrls[] = asset('room/' . ($img->image ?? 'placeholder.jpg'));
              }
          } else {
              $imageUrls[] = asset('room/' . ($room->image ?? 'placeholder.jpg'));
          }

          $badgeImages = [];
          // No discount images in slider
          $imageUrls = array_values(array_unique($imageUrls));
        @endphp

        <div x-show="(roomFilter === 'all' || roomFilter === '{{ $type }}') && (roomLocation === 'all' || roomLocation === '{{ $location }}')" x-cloak
          class="group relative block bg-white border border-gray-100 shadow-lg transition duration-300 hover:shadow-xl overflow-hidden flex flex-col h-full">

          <div class="relative overflow-hidden h-56">
            <div x-data="{ idx: 0, imagesCount: {{ count($imageUrls) }}, timer: null, next(){ this.idx = (this.idx + 1) % this.imagesCount }, go(i){ this.idx = i }, pause(){ if(this.timer){ clearInterval(this.timer); this.timer = null } }, play(){ if(!this.timer){ this.timer = setInterval(()=> this.next(), 4000) } } }" x-init="play()" @mouseenter="pause()" @mouseleave="play()" class="h-full w-full relative">
              @foreach($imageUrls as $i => $url)
                <img x-show="idx === {{ $i }}" src="{{ $url }}" alt="{{ $room->room_name }} - image {{ $i + 1 }}" onerror="this.onerror=null;this.src='{{ $placeholderImage }}'" class="absolute inset-0 w-full h-full object-cover transition duration-500" />
              @endforeach

              <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/70 to-transparent"></div>

              {{-- Percentage Badge (Right) --}}
              @if ($isActive && $discountValue > 0 && ($isPercentage || $isFixedAmount))
                  @php
                      $badgeText = $isPercentage ? '-' . rtrim(rtrim(number_format($discountValue, 2), '0'), '.') . '%' : 'SALE';
                  @endphp
                  <div class="absolute top-3 right-3 z-30">
                      <span
                          class="inline-block bg-[#964B00] text-white text-[10px] font-medium py-1.5 px-3 shadow-xl ">
                          {{ $badgeText }} OFF
                      </span>
                  </div>
              @endif

              <div class="absolute bottom-4 left-4 z-10">
                @if($isActive && $discountValue > 0 && ($isPercentage || $isFixedAmount))
                    <span class="text-sm text-white line-through block mb-1">
                        PHP {{ number_format($room->price ?? 0, 2) }}
                    </span>
                    <p class="text-white text-lg font-medium">
                        PHP {{ number_format($discountedPrice, 2) }}
                    </p>
                @else
                    <p class="text-lg font-medium text-white">
                        PHP <span class="text-white">{{ number_format($room->price ?? 0, 2) }}</span>
                    </p>
                @endif
                <span class="text-sm text-white">Per night</span>
              </div>

              <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 z-20 flex items-center gap-2">
                @for($i = 0; $i < count($imageUrls); $i++)
                  <button @click.prevent="go({{ $i }})" :class="{ 'bg-white': idx === {{ $i }}, 'bg-white/40': idx !== {{ $i }} }" class="w-2 h-2  transition"></button>
                @endfor
              </div>
            </div>
          </div>

          <div class="p-5 flex flex-col flex-grow">

            <h3 class="text-lg font-medium text-black mb-2">
              {{ $room->room_name }}
            </h3>

            <p class="text-sm text-black leading-relaxed mb-4">
              A brief, enticing description of the room can go here.
            </p>

            <div class="flex items-center space-x-4 text-sm text-black mb-5 pt-4 border-t border-gray-100">

              <div class="flex items-center" title="Accommodates">
                <span class="material-symbols-outlined text-base mr-1.5 text-black">group</span>
                <span class="font-medium text-sm text-black">{{ $room->accommodates }}</span>
              </div>

              <div class="flex items-center" title="Airconditioned">
                <span class="material-symbols-outlined text-base mr-1.5 text-black">ac_unit</span>
                <span class="font-medium text-sm text-black">Aircon</span>
              </div>

              <div class="flex items-center" title="Seaview">
                <span class="material-symbols-outlined text-base mr-1.5 text-black">apartment</span>
                <span class="font-medium text-sm text-black">Seaview</span>
              </div>
            </div>

            <div class="mt-auto">
              <button type="button"
                onclick="openBookingModal('{{ $room->id }}', '{{ addslashes($room->room_name) }}', {{ $room->price }}, {{ (int)$room->accommodates }})"
                class="flex justify-center items-center w-full bg-[#964B00] p-3 text-sm font-medium text-white border border-[#964B00] transition duration-300 hover:bg-black hover:border-black">
                Book now
              </button>
            </div>

          </div>
        </div>
      @endforeach

    </div>
  </div>
</div>
@endsection
