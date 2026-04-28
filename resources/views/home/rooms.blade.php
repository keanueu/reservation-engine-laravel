@extends('home.layouts.app')
@section('content')

    <div class="relative w-full h-[35vh] md:h-[45vh]">
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
            alt="Luxury Beach Resort" class="absolute inset-0 object-cover w-full h-full">
        <div class="relative z-10 flex items-end justify-center w-full h-full bg-black bg-opacity-50 px-4 pb-12 md:pb-16">
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl  text-white text-center font-[Manrope]">
                Our Exclusive Stays
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

<div class="bg-white py-8 font-[Manrope]" x-data="{ roomFilter: 'all', roomLocation: 'all' }" x-cloak>
  <div class="text-center mb-12">
      <p class="text-[oklch(66.6%_0.179_58.318)] text-sm sm:  mb-2">Our Accommodation</p>
      <h1 class="text-3xl sm:text-4xl md:text-5xl ">
        Exclusive Stays
      </h1>
      <p class="text-black text-sm sm: mt-3  max-w-2xl mx-auto">
       Discover the perfect space for your getaway, designed for comfort, relaxation, and lasting memories.
      </p>
    </div>

  <div class="max-w-6xl mx-auto px-6">

    <!-- Filters -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="md:flex-1">
          <div class="text-xs text-gray-500">Filter by category</div>
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
          <div class="text-xs text-gray-500 text-right md:text-right">Filter by location</div>
          <div class="mt-2 -mx-6 px-6 md:mx-0 md:px-0">
            <div class="flex items-center gap-2 justify-end overflow-x-auto md:overflow-visible py-2">
              <button @click.prevent="roomLocation='all'" :class="{ 'bg-indigo-600 text-white': roomLocation === 'all', 'bg-white text-black': roomLocation !== 'all' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">All Locations</button>
              <button @click.prevent="roomLocation='beach'" :class="{ 'bg-indigo-600 text-white': roomLocation === 'beach', 'bg-white text-black': roomLocation !== 'beach' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">Beach Front</button>
              <button @click.prevent="roomLocation='nonbeach'" :class="{ 'bg-indigo-600 text-white': roomLocation === 'nonbeach', 'bg-white text-black': roomLocation !== 'nonbeach' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">Non Beach Front</button>
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
        @endphp

        <div x-show="(roomFilter === 'all' || roomFilter === '{{ $type }}') && (roomLocation === 'all' || roomLocation === '{{ $location }}')" x-cloak
          class="group relative block bg-white border border-gray-100 shadow-lg transition duration-300 hover:shadow-xl overflow-hidden flex flex-col h-full">

          <div class="relative overflow-hidden h-56">
            @php
              $imageUrls = [];
              if(isset($room->images) && $room->images->isNotEmpty()){
                  foreach($room->images as $img){
                      $imageUrls[] = asset('room/' . ($img->image ?? 'placeholder.jpg'));
                  }
              } else {
                  $imageUrls[] = asset('room/' . ($room->image ?? 'placeholder.jpg'));
              }
            @endphp

            <div x-data="{ idx: 0, imagesCount: {{ count($imageUrls) }}, timer: null, next(){ this.idx = (this.idx + 1) % this.imagesCount }, go(i){ this.idx = i }, pause(){ if(this.timer){ clearInterval(this.timer); this.timer = null } }, play(){ if(!this.timer){ this.timer = setInterval(()=> this.next(), 4000) } } }" x-init="play()" @mouseenter="pause()" @mouseleave="play()" class="h-full w-full relative">
              @foreach($imageUrls as $i => $url)
                <img x-show="idx === {{ $i }}" src="{{ $url }}" alt="{{ $room->room_name }} - image {{ $i + 1 }}" onerror="this.onerror=null;this.src='{{ $placeholderImage }}'" class="absolute inset-0 w-full h-full object-cover transition duration-500" />
              @endforeach

              <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/70 to-transparent"></div>

              <div class="absolute bottom-4 left-4 z-10">
                <p class="text-lg font-bold text-white">
                  PHP <span class="text-white">{{ number_format($room->price ?? 0, 2) }}</span>
                </p>
                <span class="text-xs  text-gray-200 tracking-wide">PER NIGHT</span>
              </div>

              <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 z-20 flex items-center gap-2">
                @for($i = 0; $i < count($imageUrls); $i++)
                  <button @click.prevent="go({{ $i }})" :class="{ 'bg-white': idx === {{ $i }}, 'bg-white/40': idx !== {{ $i }} }" class="w-2 h-2  transition"></button>
                @endfor
              </div>
            </div>
          </div>

          <div class="p-5 flex flex-col flex-grow">

            <h3 class="text-lg  text-black tracking-tight mb-2">
              {{ $room->room_name }}
            </h3>

            <p class="text-sm text-black  mb-4">
              A brief, enticing description of the room can go here.
            </p>

            <div class="flex items-center space-x-4 text-sm text-gray-700 mb-5 pt-4 border-t border-gray-100">

              <div class="flex items-center" title="Accommodates">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-5 mr-1.5 text-gray-500" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2">
                  </path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span class="font-medium text-xs text-gray-600">{{ $room->accommodates }}</span>
              </div>

              <div class="flex items-center" title="Airconditioned">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500 mr-1.5" viewBox="0 0 24 24"
                  fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                  <g>
                    <path
                      d="M8 16a3 3 0 0 1-3 3m11-3a3 3 0 0 0 3 3m-7-3v4M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <path d="M7 13v-3a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v3" />
                  </g>
                </svg>
                <span class="font-medium text-xs text-gray-600">Aircon</span>
              </div>

              <div class="flex items-center" title="Seaview">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-4 mr-1.5 text-gray-500" viewBox="0 0 24 24"
                  fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path
                    d="M4 13v8m4-8v8m8-8v8m-4-8v8m8-8v8M2 21h20M2 13h20m-4-3V3.6a.6.6 0 0 0-.6-.6H6.6a.6.6 0 0 0-.6.6V10" />
                </svg>
                <span class="font-medium text-xs text-gray-600">Seaview</span>
              </div>
            </div>

            <div class="mt-auto">
              <button type="button"
                onclick="openBookingModal('{{ $room->id }}', '{{ addslashes($room->room_name) }}', {{ $room->price }}, {{ (int)$room->accommodates }})"
                class="flex justify-center items-center w-full bg-[#964B00] p-3 text-xs font-semibold text-white tracking-widest border border-[#964B00] transition duration-300 hover:bg-black hover:border-black">
                Book Now
              </button>
            </div>

          </div>
        </div>
      @endforeach

    </div>
  </div>
</div>
@endsection