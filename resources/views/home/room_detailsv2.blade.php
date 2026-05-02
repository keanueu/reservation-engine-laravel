@extends('home.layouts.app')
@section('content')

<div class="relative w-full h-[35vh] md:h-[45vh]">
    <img src="{{ $room->image_url ?? 'https://placehold.co/1920x800' }}"
         class="absolute inset-0 w-full h-full object-cover" />

    <div class="absolute inset-0 bg-black/50 flex items-end justify-center pb-12">
        <h1 class="text-5xl  text-white font-[Manrope] tracking-wide">
            Promo Room Details
        </h1>
    </div>
</div>

<section class="pt-10 pb-16">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Back Button --}}
        <a href="{{ url('/home/promos') }}" class="flex items-center text-sm text-gray-600 mb-4">
            <span class="material-symbols-outlined mr-1" style="font-size: 16px;">arrow_back</span>
            Back to Promos
        </a>

        <h2 class="text-3xl  text-gray-900 uppercase tracking-wide">
            {{ $room->room_name }}
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mt-8">

            {{-- IMAGES --}}
            <div class="lg:col-span-2 space-y-6">

                @php
                    $placeholder = "https://placehold.co/800x600";
                    $images = $room->images->count()
                        ? $room->images->map(fn($i) => asset('room/'.$i->image))->toArray()
                        : [asset('room/'.$room->image)];
                @endphp

                <div x-data="{i:0}" class="relative h-[380px] overflow-hidden shadow-md">
                    @foreach ($images as $index => $src)
                        <img x-show="i === {{ $index }}" src="{{ $src }}"
                             class="absolute inset-0 w-full h-full object-cover transition" />
                    @endforeach
                </div>

                {{-- DESCRIPTION --}}
                <div>
                    <h3 class="text-xl  text-gray-900 mb-2">Description</h3>
                    <p class="text-sm text-gray-700 leading-relaxed ">
                        {{ $room->description }}
                    </p>
                </div>
            </div>

            {{-- RIGHT SIDE: PROMO + DETAILS --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- PROMO CARD --}}
                <div class="p-5 bg-white border border-red-400 shadow-sm ">
                    <h3 class="text-xl text-red-600  mb-2">🔥 Promo Price</h3>

                    <div class="text-gray-800">
                        <p class="text-sm line-through">Regular Price: ₱{{ number_format($room->price, 2) }}</p>
                        <p class="text-3xl font-semibold text-red-600 mt-1">
                            ₱{{ number_format($room->promo_price, 2) }}
                        </p>
                    </div>

                    <a href="{{ route('booking.dates') }}"
                       class="block text-center mt-4 bg-red-600 text-white py-2  text-sm">
                        Book Now
                    </a>
                </div>

                {{-- PROPERTIES --}}
                <div class="p-5 bg-white border shadow-sm">
                    <h3 class="text-xl  text-gray-900 mb-4">Room Details</h3>

                    <ul class="text-sm space-y-2">
                        <li>Accommodates: {{ $room->accommodates }} guests</li>
                        <li>Beds: {{ $room->beds }}</li>
                        <li>Check-in: {{ \Carbon\Carbon::parse($room->check_in)->format('h:i A') }}</li>
                        <li>Check-out: {{ \Carbon\Carbon::parse($room->check_out)->format('h:i A') }}</li>
                    </ul>
                </div>

                {{-- Amenities --}}
                <div>
                    <h3 class="text-xl  text-gray-900">Amenities</h3>

                    <ul class="text-sm text-gray-700 mt-3 space-y-1">
                        @foreach(explode(',', $room->amenities) as $amen)
                            <li>• {{ trim($amen) }}</li>
                        @endforeach
                    </ul>
                </div>

            </div>

        </div>
    </div>
</section>

@endsection
