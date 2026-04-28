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
                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
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
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                        </button>

                        {{-- Next Button --}}
                        <button @click.prevent="next()"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 z-30 p-2 bg-black/30 hover:bg-black/60 text-white  transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
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
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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

                        <button type="button"
                            onclick="openBookingModal('{{ $room->id }}', '{{ addslashes($room->room_name) }}', {{ $room->price }}, {{ (int)$room->accommodates }})"
                            class="w-full btn-primary py-3.5 text-sm font-bold tracking-widest uppercase flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Book This Room
                        </button>

                        <p class="text-center text-xs text-gray-400 mt-3">No charge until checkout</p>

                        {{-- Trust signals --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                            @php $signals = [['M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','Verified & Secure Booking'],['M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z','Free cancellation policy'],['M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z','PayMongo secure payment']]; @endphp
                            @foreach($signals as [$icon,$label])
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <svg class="w-4 h-4 text-[#964B00] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                                    {{ $label }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="p-5 bg-white shadow-sm border border-gray-300 font-[Manrope]">
                        <h3 class="text-xl  text-gray-900 mb-4 flex items-center gap-2">
                            Properties
                        </h3>

                        <ul class="grid grid-cols-1 gap-y-3 text-xs text-gray-900">
                            <li class="flex items-center justify-between sm:justify-start sm:space-x-2">
                                {{-- Accommodates Icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-[18px] mr-1.5 font-semibold text-black"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                                <span class=" text-gray-900 flex-1">Accommodates:</span>
                                <span class="">{{ $room->accommodates }} Guests</span>
                            </li>
                            <li class="flex items-center justify-between sm:justify-start sm:space-x-2">
                                {{-- Beds Icon (adjusted viewBox for better sizing) --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-4 mr-1.5  text-black"
                                    viewBox="0 0 24 24">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="1.7"
                                        d="M5 9a2 2 0 1 0 4 0a2 2 0 1 0-4 0m17 8v-3H2m0-6v9m10-3h10v-2a3 3 0 0 0-3-3h-7z" />
                                </svg>
                                <span class=" text-gray-900 flex-1">Beds:</span>
                                <span class="">{{ $room->beds }}</span>
                            </li>
                            <li class="flex items-center justify-between sm:justify-start sm:space-x-2">
                                {{-- Check-in Icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1.5 text-black"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11.795 21H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4" />
                                    <path d="M14 18a4 4 0 1 0 8 0a4 4 0 1 0-8 0m1-15v4M7 3v4m-4 4h16" />
                                    <path d="M18 16.496V18l1 1" />
                                </svg>
                                <span class=" text-gray-900 flex-1">Check-in:</span>
                                <span
                                    class="text-gray-900 ">{{ \Carbon\Carbon::parse($room->check_in)->format('h:i A') }}</span>
                            </li>
                            <li class="flex items-center justify-between sm:justify-start sm:space-x-2">
                                {{-- Check-out Icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1.5 text-black"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11.795 21H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4" />
                                    <path d="M14 18a4 4 0 1 0 8 0a4 4 0 1 0-8 0m1-15v4M7 3v4m-4 4h16" />
                                    <path d="M18 16.496V18l1 1" />
                                </svg>
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
                                    'Airconditioned' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                                                class="w-5 h-[18px] mr-1.5 inline-block align-middle text-black"
                                                                                                fill="none"
                                                                                                viewBox="0 0 24 24"
                                                                                                stroke="currentColor"
                                                                                                stroke-width="2.2">
                                                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                                                    d="M8 16a3 3 0 0 1-3 3m11-3a3 3 0 0 0 3 3m-7-3v4M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                                                    d="M7 13v-3a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v3" />
                                                                                            </svg>',

                                    'Minibar' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                                                class="w-5 h-5 mr-1.5 inline-block align-middle text-black"
                                                                                                fill="none"
                                                                                                viewBox="0 0 32 32"
                                                                                                stroke="currentColor"
                                                                                                stroke-width="2.2"
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round">
                                                                                                <path d="M25 11H15a1 1 0 0 0-1 1v4a6.005 6.005 0 0 0 5 5.91V28h-3v2h8v-2h-3v-6.09A6.005 6.005 0 0 0 26 16v-4a1 1 0 0 0-1-1Zm-1 5a4 4 0 0 1-8 0v-3h8Z"/>
                                                                                                <path d="M15 1h-5a1 1 0 0 0-1 1v7.37A6.09 6.09 0 0 0 6 15v14a1 1 0 0 0 1 1h5v-2H8V15c0-3.188 2.231-4.02 2.316-4.051L11 10.72V3h3v5h2V2a1 1 0 0 0-1-1Z"/>
                                                                                            </svg>',

                                    'Shower' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                                                class="w-5 h-5 mr-1.5 inline-block align-middle text-black"
                                                                                                fill="none"
                                                                                                viewBox="0 0 24 24"
                                                                                                stroke="currentColor"
                                                                                                stroke-width="2.2"
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round">
                                                                                                <path d="M5 14v-2q0-2.65 1.7-4.6T11 5.1V3h2v2.1q2.6.35 4.3 2.3T19 12v2z"/>
                                                                                                <circle cx="8" cy="17" r="1"/><circle cx="12" cy="17" r="1"/><circle cx="16" cy="17" r="1"/>
                                                                                                <circle cx="8" cy="20" r="1"/><circle cx="12" cy="20" r="1"/><circle cx="16" cy="20" r="1"/>
                                                                                            </svg>',

                                    'Bath' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                                                class="w-5 h-5 mr-1.5 inline-block align-middle text-black"
                                                                                                fill="none"
                                                                                                viewBox="0 0 24 24"
                                                                                                stroke="currentColor"
                                                                                                stroke-width="2.2"
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round">
                                                                                                <path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1m2 0V5a2 2 0 0 1 2-2h3v2.25M4 21l1-1.5M20 21l-1-1.5"/>
                                                                                            </svg>',

                                    'Kitchen' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                                                class="w-5 h-5 mr-1.5 inline-block align-middle text-black"
                                                                                                fill="none"
                                                                                                viewBox="0 0 24 24"
                                                                                                stroke="currentColor"
                                                                                                stroke-width="2.2"
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round">
                                                                                                <path d="M19 3v12h-5c-.023-3.681.184-7.406 5-12m0 12v6h-1v-3M8 4v17M5 4v3a3 3 0 1 0 6 0V4"/>
                                                                                            </svg>',

                                    'Balcony with sea view' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                                                class="w-5 h-5 mr-1.5 inline-block align-middle text-black"
                                                                                                fill="none"
                                                                                                viewBox="0 0 24 24"
                                                                                                stroke="currentColor"
                                                                                                stroke-width="2.2"
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round">
                                                                                                <path d="M4 13v8m4-8v8m8-8v8m-4-8v8m8-8v8M2 21h20M2 13h20m-4-3V3.6a.6.6 0 0 0-.6-.6H6.6a.6.6 0 0 0-.6.6V10" />
                                                                                            </svg>',

                                    'Work Space' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                                                class="w-5 h-5 mr-1.5 inline-block align-middle text-black"
                                                                                                fill="none"
                                                                                                viewBox="0 0 14 14"
                                                                                                stroke="currentColor"
                                                                                                stroke-width="2.2"
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round">
                                                                                                <path d="M7 6.052v1.883M7.002.678c-.756 0-1.541 0-2.24.088a1.39 1.39 0 0 0-1.216 1.18c-.052.377-.052.768-.052 1.42c0 .65 0 1.041.052 1.418a1.39 1.39 0 0 0 1.216 1.18c.699.088 1.484.088 2.24.088s1.54 0 2.24-.088a1.39 1.39 0 0 0 1.216-1.18c.052-.377.052-.768.052-1.419s0-1.042-.052-1.42A1.39 1.39 0 0 0 9.242.767c-.7-.088-1.484-.088-2.24-.088M1.856 13.322c-.058-.818-.166-1.685-.166-2.583c0-.314.014-.624.033-.93A1.87 1.87 0 0 1 3.51 8.057c1.344-.063 3.048-.122 3.49-.122s2.146.06 3.49.122a1.867 1.867 0 0 1 1.787 1.752q.032.459.033.93c0 .898-.108 1.765-.165 2.583"/>
                                                                                                <path d="M1.694 10.628c1.065.066 4.136.198 5.306.198s4.24-.132 5.306-.198"/>
                                                                                            </svg>',

                                    'Hot & Cold Shower' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                                                class="w-5 h-5 mr-1.5 inline-block align-middle text-black"
                                                                                                fill="none"
                                                                                                viewBox="0 0 24 24"
                                                                                                stroke="currentColor"
                                                                                                stroke-width="2.2"
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round">
                                                                                                <path d="M5 14v-2q0-2.65 1.7-4.6T11 5.1V3h2v2.1q2.6.35 4.3 2.3T19 12v2z"/>
                                                                                                <circle cx="8" cy="17" r="1"/><circle cx="12" cy="17" r="1"/><circle cx="16" cy="17" r="1"/>
                                                                                                <circle cx="8" cy="20" r="1"/><circle cx="12" cy="20" r="1"/><circle cx="16" cy="20" r="1"/>
                                                                                            </svg>',

                                    'Kitchen with stove for free use' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                                                class="w-5 h-5 mr-1.5 inline-block align-middle text-black"
                                                                                                fill="none"
                                                                                                viewBox="0 0 17 16"
                                                                                                stroke="currentColor"
                                                                                                stroke-width="2.2"
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round">
                                                                                                <path d="M1 0v3h14.958V0H1zm2 2H2V1h1v1zm2 0H4V1h1v1zM1 16h14.958V4.042H1V16zM5 6h7v1H5V6zM4 7.958h9v6H4v-6z"/>
                                                                                            </svg>',

                                    'Refrigerator' => '<svg xmlns="http://www.w3.org/2000/svg"
                                                                                                class="w-5 h-5 mr-1.5 inline-block align-middle text-black"
                                                                                                fill="none"
                                                                                                viewBox="0 0 24 24"
                                                                                                stroke="currentColor"
                                                                                                stroke-width="2.2"
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round">
                                                                                                <path d="M4 10v3c0 3.771 0 5.657 1.172 6.828C6.343 21 8.229 21 12 21c3.771 0 5.657 0 6.828-1.172C20 18.657 20 16.771 20 13v-3c0-3.771 0-5.657-1.172-6.828C17.657 2 15.771 2 12 2C8.229 2 6.343 2 5.172 3.172C4.518 3.825 4.229 4.7 4.102 6"/>
                                                                                                <path d="M20 11.5h-5m-11 0h7M17 7v2m0 5v2"/>
                                                                                            </svg>',
                                ];
                            @endphp

                            {{-- Apply consistent style to all SVGs and use a responsive grid for wrapping --}}
                            <div
                                class="grid grid-cols-2 md:grid-cols-1 xl:grid-cols-2 gap-2 mt-2 font-[Manrope] [&_svg]:w-5 [&_svg]:h-5 [&_svg]:text-black [&_svg]:mr-1.5 [&_svg]:inline-block [&_svg]:align-middle [&_svg]:stroke-[2.2]">
                                @foreach(explode(',', $room->amenities) as $amenity)
                                    @php $name = trim($amenity); @endphp
                                    <span
                                        class="flex items-center gap-1 bg-white text-black text-xs  px-3 py-1.5  border border-gray-200 shadow-sm hover:bg-gray-100">
                                        {!! $icons[$name] ?? '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-black mr-1.5 inline-block align-middle" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="9" /></svg>' !!}
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