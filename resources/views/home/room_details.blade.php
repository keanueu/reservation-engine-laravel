@extends('home.layouts.app')
@section('content')

    <div class="relative w-full h-[35vh] md:h-[45vh]">
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
            alt="Luxury Beach Resort" class="absolute inset-0 object-cover w-full h-full">
        <div class="relative z-10 flex items-end justify-center w-full h-full bg-black bg-opacity-50 px-4 pb-12 md:pb-16">
            <h1
                class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl  text-white text-center font-[Manrope] tracking-wide">
                Our Exclusive Stays
            </h1>
        </div>
    </div>


    <section class="pt-12 pb-12">
        <div class="max-w-6xl mx-auto px-4" id="booking-section">

            <div class="pb-3 font-[Manrope]">
                <a href="{{ url('/home/rooms') }}"
                    class="flex items-center text-sm text-gray-600 hover:text-gray-900 transition mb-1">
                    <span class="material-symbols-outlined mr-1" style="font-size: 16px;">arrow_back</span>
                    Back to Rooms
                </a>
                <h1 class="text-2xl md:text-3xl  text-gray-900 uppercase tracking-wide">
                    {{ $room->room_name ?? 'Room Details' }}
                </h1>
            </div>

            {{-- We keep the max-w-6xl alignment from the parent div. --}}
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
                        <h2 class="text-xl  font-[Manrope] text-gray-900 pb-2 flex items-center gap-2">
                            Description
                        </h2>
                        <p class="mt-2 text-gray-700 font-[Manrope] text-sm  leading-relaxed">
                            {{ $room->description }}
                        </p>
                    </div>

                </div>

                <div class="lg:col-span-1 flex flex-col space-y-8">

                    {{-- Sticky Booking Widget --}}
                    <div class="sticky top-24 p-6 bg-white shadow-lg border border-gray-200">
                        <div class="flex items-end justify-between mb-4 pb-4 border-b border-gray-100">
                            <div>
                                <p class="text-xs font-bold tracking-widest uppercase text-gray-400">Price</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">
                                    PHP {{ number_format($room->price, 2) }}
                                    <span class="text-sm font-normal text-gray-400">/ night</span>
                                </p>
                            </div>
                            <div class="flex items-center gap-1 text-xs font-semibold text-[#964B00]">
                                <span class="material-symbols-outlined" style="font-size: 16px;">check_circle</span>
                                Available
                            </div>
                        </div>

                        {{-- Quick date preview --}}
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="border border-gray-200 px-3 py-2.5">
                                <p class="text-[10px] font-bold tracking-widest uppercase text-gray-400">Check-in</p>
                                <p class="text-xs font-semibold text-gray-700 mt-1">Select date</p>
                            </div>
                            <div class="border border-gray-200 px-3 py-2.5">
                                <p class="text-[10px] font-bold tracking-widest uppercase text-gray-400">Check-out</p>
                                <p class="text-xs font-semibold text-gray-700 mt-1">Select date</p>
                            </div>
                        </div>

                        <a href="{{ route('booking.dates') }}"
                            class="w-full btn-primary py-3.5 text-sm font-bold tracking-widest uppercase flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined" style="font-size: 16px;">calendar_month</span>
                            Book This Room
                        </a>

                        <p class="text-center text-xs text-gray-400 mt-3">No charge until checkout</p>

                        {{-- Trust signals --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span class="material-symbols-outlined text-[#964B00] flex-shrink-0" style="font-size: 16px;">verified</span>
                                Verified & Secure Booking
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span class="material-symbols-outlined text-[#964B00] flex-shrink-0" style="font-size: 16px;">lock</span>
                                Free cancellation policy
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span class="material-symbols-outlined text-[#964B00] flex-shrink-0" style="font-size: 16px;">credit_card</span>
                                PayMongo secure payment
                            </div>
                        </div>
                    </div>
                    <div class="p-5 bg-white shadow-sm border border-gray-300 font-[Manrope]">
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
                        <h3 class="text-xl  text-gray-900 pb-2 flex items-center gap-2 font-[Manrope]">
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

                            <div class="grid grid-cols-2 md:grid-cols-1 xl:grid-cols-2 gap-2 mt-2 font-[Manrope]">
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
@endsection