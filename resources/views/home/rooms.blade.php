@extends('home.layouts.app')
@section('content')

    <div class="relative w-full h-[35vh] md:h-[45vh]">
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
            alt="Luxury Beach Resort" class="absolute inset-0 object-cover w-full h-full">
        <div class="relative z-10 flex items-end justify-center w-full h-full bg-black bg-opacity-50 px-4 pb-12 md:pb-16">
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl text-white text-center font-medium">
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

<div class="bg-white py-8 " x-data="{ roomFilter: 'all', roomLocation: 'all' }" x-cloak>
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
              <button @click.prevent="roomFilter='all'" :class="{ 'bg-[#63360D] text-white': roomFilter === 'all', 'bg-white text-black': roomFilter !== 'all' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">All</button>
              <button @click.prevent="roomFilter='regular'" :class="{ 'bg-[#63360D] text-white': roomFilter === 'regular', 'bg-white text-black': roomFilter !== 'regular' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">Regular</button>
              <button @click.prevent="roomFilter='premium'" :class="{ 'bg-[#63360D] text-white': roomFilter === 'premium', 'bg-white text-black': roomFilter !== 'premium' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">Premium</button>
              <button @click.prevent="roomFilter='deluxe'" :class="{ 'bg-[#63360D] text-white': roomFilter === 'deluxe', 'bg-white text-black': roomFilter !== 'deluxe' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">Deluxe</button>
            </div>
          </div>
        </div>

        <div class="mt-4 md:mt-0 md:ml-6 md:flex-none">
          <div class="text-sm font-medium text-black text-right md:text-right mb-2">Filter by location</div>
          <div class="mt-2 -mx-6 px-6 md:mx-0 md:px-0">
            <div class="flex items-center gap-2 justify-end overflow-x-auto md:overflow-visible py-2">
              <button @click.prevent="roomLocation='all'" :class="{ 'bg-[#63360D] text-white': roomLocation === 'all', 'bg-white text-black': roomLocation !== 'all' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">All locations</button>
              <button @click.prevent="roomLocation='beach'" :class="{ 'bg-[#63360D] text-white': roomLocation === 'beach', 'bg-white text-black': roomLocation !== 'beach' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">Beach front</button>
              <button @click.prevent="roomLocation='nonbeach'" :class="{ 'bg-[#63360D] text-white': roomLocation === 'nonbeach', 'bg-white text-black': roomLocation !== 'nonbeach' }" class="flex-shrink-0 px-3 py-2  border border-gray-200 text-sm">Non beach front</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">

      @forelse ($rooms as $room)
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

            $imageSources = [];

            if (!empty($room->image)) {
                $decodedRoomImages = json_decode($room->image, true);
                $addImage = function ($raw) use (&$imageSources, $placeholderImage) {
                    $raw = trim($raw);
                    if (empty($raw))
                        return;
                    if (preg_match('#^https?://#i', $raw)) {
                        $imageSources[] = $raw;
                        return;
                    }
                    $raw = preg_replace('#^/+#', '', $raw);
                    $raw = preg_replace('#^room/#i', '', $raw);
                    $imageSources[] = asset('room/' . $raw);
                };

                if (is_array($decodedRoomImages)) {
                    foreach ($decodedRoomImages as $ri) {
                        $addImage($ri);
                    }
                } else {
                    if (strpos($room->image, ',') !== false) {
                        $parts = array_map('trim', explode(',', $room->image));
                        foreach ($parts as $p) {
                            $addImage($p);
                        }
                    } else {
                        $addImage($room->image);
                    }
                }
            } else {
                $imageSources[] = $placeholderImage;
            }

            if (method_exists($room, 'images')) {
                foreach ($room->images as $imgModel) {
                    $raw = $imgModel->image ?? null;
                    if (!empty($raw)) {
                        if (preg_match('#^https?://#i', $raw)) {
                            $imageSources[] = $raw;
                        } else {
                            $raw = preg_replace('#^/+#', '', $raw);
                            $raw = preg_replace('#^room/#i', '', $raw);
                            $imageSources[] = asset('room/' . $raw);
                        }
                    }
                }
            }

            $imageSources = array_values(array_unique($imageSources));
        @endphp

        <div x-show="(roomFilter === 'all' || roomFilter === '{{ $type }}') && (roomLocation === 'all' || roomLocation === '{{ $location }}')"
            x-cloak
            data-room-id="{{ $room->id }}"
            class="room-card bg-white border border-gray-100 shadow-lg transition duration-300 hover:shadow-xl overflow-hidden flex flex-col h-full">

            <div x-data="{
                        images: {{ json_encode($imageSources) }},
                        currentIndex: 0,
                        init() {
                            if (this.images.length > 1) {
                                setInterval(() => {
                                    this.currentIndex = (this.currentIndex + 1) % this.images.length;
                                }, 5000);
                            }
                        }
                    }" class="relative room-image-container overflow-hidden h-56 w-full flex-shrink-0">

                @php
                    $isAboveFold = $loop->index < 4;
                @endphp
                <div class="h-full w-full relative">
                    <img draggable="false" 
                        src="{{ $imageSources[0] ?? $placeholderImage }}"
                        x-bind:src="images[currentIndex]"
                        :alt="`{{ addslashes($room->room_name) }} image ${currentIndex + 1}`"
                        style="-webkit-user-drag: none; -webkit-user-select: none; user-select: none;"
                        class="absolute inset-0 w-full h-full object-cover transition duration-500 transform group-hover:scale-105"
                        @if($isAboveFold)
                            fetchpriority="high"
                        @else
                            loading="lazy"
                        @endif
                        decoding="async"
                        x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-500"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" />
                </div>

                <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/70 to-transparent z-5">
                </div>

                @if(count($imageSources) > 1)
                    <div class="absolute top-2 left-1/2 transform -translate-x-1/2 flex space-x-1 z-20">
                        @foreach ($imageSources as $key => $src)
                            <button @click="currentIndex = {{ $key }}"
                                class="w-2 h-2  transition-colors duration-300"
                                :class="{ 'bg-white': currentIndex === {{ $key }}, 'bg-white/40': currentIndex !== {{ $key }} }"
                                aria-label="Go to slide {{ $key + 1 }}"></button>
                        @endforeach
                    </div>
                @endif

                {{-- Availability Badge (Left) --}}
                @php
                    $isFullyBooked = in_array($room->id, $unavailableRoomIds ?? []);
                @endphp
                <div class="absolute top-3 left-3 z-30">
                    <span
                        class="inline-block {{ $isFullyBooked ? 'bg-red-600' : 'bg-green-600' }} text-white text-xs font-medium py-1.5 px-3 shadow-xl">
                        {{ $isFullyBooked ? 'Fully booked' : 'Available' }}
                    </span>
                </div>

                @if ($isActive && $discountValue > 0 && ($isPercentage || $isFixedAmount))
                    @php
                        $badgeText = $isPercentage ? '-' . rtrim(rtrim(number_format($discountValue, 2), '0'), '.') . '%' : 'SALE';
                    @endphp
                    <div class="absolute top-3 right-3 z-30">
                        <span
                            class="inline-block bg-[#63360D] text-white text-xs font-medium py-1.5 px-3 shadow-xl ">
                            {{ $badgeText }} off
                        </span>
                    </div>
                @endif

                <div class="absolute bottom-4 left-4 z-20 text-white">
                    @if($isActive && $discountValue > 0 && ($isPercentage || $isFixedAmount))
                        <span class="text-sm text-white line-through block mb-1">
                            PHP {{ number_format($room->price ?? 0, 2) }}
                        </span>
                        <p class="text-white text-lg font-bold">
                            PHP {{ number_format($discountedPrice, 2) }}
                        </p>
                    @else
                        <p class="text-2xl font-bold">
                            PHP {{ number_format($room->price ?? 0, 2) }}
                        </p>
                    @endif
                    <span class="text-sm text-white block mt-0.5">Per night</span>
                </div>

            </div>

            <div class="p-4 flex flex-col flex-grow w-full">

                <h3 class="text-lg font-bold text-[#63360D] mb-2">
                    {{ $room->room_name }}
                </h3>

                <p class="text-sm font-medium text-black leading-relaxed mb-4 flex-grow">
                    {{ \Illuminate\Support\Str::limit($room->description ?? 'An exquisite room offering premium comfort and luxury for your stay.', 100) }}
                </p>

                @if($isActive && $expiryDate)
                    <div class="py-1 mb-2">
                        <p class="text-sm font-normal text-red-600">
                            Offer Ends: {{ \Carbon\Carbon::parse($expiryDate)->format('M d, Y') }}
                        </p>
                    </div>
                @endif

                <div class="flex flex-wrap gap-x-4 gap-y-3 text-black text-sm mb-4 pt-3 border-t border-gray-200">

                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">group</span>
                        <span class="text-sm text-black font-normal">{{ $room->accommodates }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">bed</span>
                        <span class="text-sm text-black font-normal">{{ $room->beds }}</span>
                    </div>
                </div>

                <div class="mt-auto flex justify-between items-center">
                    <a href="{{ url('room_details', $room->id) }}" class="text-[#63360D] font-semibold text-sm hover:text-black transition-colors">
                        View details
                    </a>
                    <a href="{{ route('booking.dates', ['room_id' => $room->id]) }}"
                        class="bg-[#63360D] px-8 py-3 text-sm font-semibold text-white hover:bg-black transition flex items-center justify-center shadow-lg shadow-orange-900/10">
                        Book now
                    </a>
                </div>

            </div>

        </div>
      @empty
        <div class="w-full text-center p-10 col-span-full">
            <p class="text-lg text-black">No rooms available at this time.</p>
        </div>
      @endforelse

    </div>
  </div>
</div>
@endsection

