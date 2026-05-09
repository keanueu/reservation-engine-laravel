@extends('home.layouts.app')
@section('content')

@php
    $isBoat  = $step1['type'] === 'boat';
    $name    = $isBoat ? $model->name : $model->room_name;
    $price   = $model->price;
@endphp

<div class="relative w-full h-[30vh] md:h-[38vh]">
    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
         alt="Guests" class="absolute inset-0 object-cover w-full h-full">
    <div class="relative z-10 flex items-end justify-center w-full h-full bg-black/50 px-4 pb-10 md:pb-14">
        <h1 class="text-4xl sm:text-5xl md:text-6xl text-white text-center font-[Inter]">Who's Coming?</h1>
    </div>
</div>

<div class="max-w-2xl mx-auto px-6 py-10" x-data="guestStep({{ $prefill['adults'] ?? 1 }}, {{ $prefill['children'] ?? 0 }}, {{ $maxGuests }})">

    {{-- Progress bar --}}
    @include('home.booking._progress', ['current' => 2])

    {{-- Selection summary pill --}}
    <div class="flex items-center justify-between px-4 py-3 border border-[#964B00] bg-[#964B00]/5 mb-8">
        <div>
            <p class="text-xs font-bold tracking-widest uppercase text-[#964B00]">
                {{ $isBoat ? 'Boat Trip' : 'Room Stay' }}
            </p>
            <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $name }}</p>
        </div>
        <div class="text-right text-sm">
            @if($isBoat)
                <p class="text-xs text-gray-500">{{ $step1['booking_date'] }}</p>
                <p class="text-xs text-gray-500">{{ $step1['start_time'] }} – {{ $step1['end_time'] }}</p>
            @else
                <p class="text-xs text-gray-500">{{ $step1['checkin'] }} → {{ $step1['checkout'] }}</p>
                <p class="text-xs font-bold text-gray-700">{{ $step1['nights'] }} night{{ $step1['nights'] > 1 ? 's' : '' }}</p>
            @endif
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('booking.guests.post') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Adults --}}
        <div>
            <label class="block text-xs font-bold tracking-widest uppercase text-gray-500 mb-2">Adults</label>
            <div class="flex items-center border border-gray-200">
                <button type="button" @click="adults = Math.max(1, adults - 1)"
                        class="w-12 h-12 flex items-center justify-center text-gray-500 hover:bg-gray-50 text-xl font-bold transition-colors">−</button>
                <span class="flex-1 text-center text-sm font-bold text-gray-900" x-text="adults"></span>
                <button type="button" @click="adults = Math.min(maxGuests - children, adults + 1)"
                        class="w-12 h-12 flex items-center justify-center text-gray-500 hover:bg-gray-50 text-xl font-bold transition-colors">+</button>
            </div>
            <input type="hidden" name="adults" :value="adults">
        </div>

        {{-- Children --}}
        <div>
            <label class="block text-xs font-bold tracking-widest uppercase text-gray-500 mb-2">Children</label>
            <div class="flex items-center border border-gray-200">
                <button type="button" @click="children = Math.max(0, children - 1)"
                        class="w-12 h-12 flex items-center justify-center text-gray-500 hover:bg-gray-50 text-xl font-bold transition-colors">−</button>
                <span class="flex-1 text-center text-sm font-bold text-gray-900" x-text="children"></span>
                <button type="button" @click="children = Math.min(maxGuests - adults, children + 1)"
                        class="w-12 h-12 flex items-center justify-center text-gray-500 hover:bg-gray-50 text-xl font-bold transition-colors">+</button>
            </div>
            <input type="hidden" name="children" :value="children">
            <p class="text-xs text-gray-400 mt-1">Max <span x-text="maxGuests"></span> guests total</p>
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('booking.dates') }}"
               class="flex-1 py-3 text-xs font-bold tracking-widest uppercase border border-gray-200 text-gray-600 hover:border-gray-400 text-center transition-colors">
                ← Back
            </a>
            <button type="submit" class="flex-1 btn-primary py-3 text-xs font-bold tracking-widest uppercase">
                Review Booking →
            </button>
        </div>
    </form>
</div>

<script>
function guestStep(adults, children, maxGuests) {
    return { adults, children, maxGuests };
}
</script>
@endsection
