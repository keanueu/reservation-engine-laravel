@extends('home.layouts.app')
@section('content')

@php
    $selectedRoom = $prefill['room_id'] ? $rooms->firstWhere('id', $prefill['room_id']) : null;
    $selectedBoat = $prefill['boat_id'] ? $boats->firstWhere('id', $prefill['boat_id']) : null;
    $today = now()->toDateString();
@endphp

<div class="relative w-full h-[30vh] md:h-[38vh]">
    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
         alt="Book Your Stay" class="absolute inset-0 object-cover w-full h-full">
    <div class="relative z-10 flex items-end justify-center w-full h-full bg-black/50 px-4 pb-10 md:pb-14">
        <h1 class="text-4xl sm:text-5xl md:text-6xl text-white text-center font-[Inter]">Book Your Experience</h1>
    </div>
</div>

<div class="max-w-2xl mx-auto px-6 py-10" x-data="bookingStep1('{{ $prefill['type'] ?? 'room' }}')">

    {{-- Progress bar --}}
    @include('home.booking._progress', ['current' => 1])

    {{-- Type toggle --}}
    <div class="flex border border-gray-200 mb-8">
        <button type="button" @click="type = 'room'"
                :class="type === 'room' ? 'bg-[#964B00] text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                class="flex-1 py-3 text-xs font-bold tracking-widest uppercase transition-colors">
            Room Stay
        </button>
        <button type="button" @click="type = 'boat'"
                :class="type === 'boat' ? 'bg-[#964B00] text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                class="flex-1 py-3 text-xs font-bold tracking-widest uppercase transition-colors">
            Boat Adventure
        </button>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-sm text-red-700">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- ROOM FORM --}}
    <form x-show="type === 'room'" action="{{ route('booking.dates.post') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="type" value="room">

        <div>
            <label class="block text-xs font-bold tracking-widest uppercase text-gray-500 mb-2">Select Room</label>
            <select name="room_id" required
                    class="w-full border border-gray-200 px-4 py-3 text-sm text-gray-700 bg-white focus:outline-none focus:border-[#964B00] transition-colors">
                <option value="">— Choose a room —</option>
                @foreach($rooms as $r)
                    <option value="{{ $r->id }}" {{ ($prefill['room_id'] ?? '') == $r->id ? 'selected' : '' }}>
                        {{ $r->room_name }} — PHP {{ number_format($r->price, 2) }}/night (up to {{ $r->accommodates }} guests)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold tracking-widest uppercase text-gray-500 mb-2">Check-in Date</label>
                <input type="date" name="checkin" required min="{{ $today }}"
                       value="{{ $prefill['checkin'] ?? '' }}"
                       class="w-full border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#964B00] transition-colors">
            </div>
            <div>
                <label class="block text-xs font-bold tracking-widest uppercase text-gray-500 mb-2">Check-out Date</label>
                <input type="date" name="checkout" required min="{{ $today }}"
                       value="{{ $prefill['checkout'] ?? '' }}"
                       class="w-full border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#964B00] transition-colors">
            </div>
            <div>
                <label class="block text-xs font-bold tracking-widest uppercase text-gray-500 mb-2">Check-in Time</label>
                <input type="time" name="checkin_time" value="{{ $prefill['checkin_time'] ?? '13:00' }}"
                       class="w-full border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#964B00] transition-colors">
            </div>
            <div>
                <label class="block text-xs font-bold tracking-widest uppercase text-gray-500 mb-2">Check-out Time</label>
                <input type="time" name="checkout_time" value="{{ $prefill['checkout_time'] ?? '11:00' }}"
                       class="w-full border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#964B00] transition-colors">
            </div>
        </div>

        <button type="submit" class="w-full btn-primary py-3 text-xs font-bold tracking-widest uppercase">
            Continue to Guests →
        </button>
    </form>

    {{-- BOAT FORM --}}
    <form x-show="type === 'boat'" action="{{ route('booking.dates.post') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="type" value="boat">

        <div>
            <label class="block text-xs font-bold tracking-widest uppercase text-gray-500 mb-2">Select Boat</label>
            <select name="boat_id" required
                    class="w-full border border-gray-200 px-4 py-3 text-sm text-gray-700 bg-white focus:outline-none focus:border-[#964B00] transition-colors">
                <option value="">— Choose a boat —</option>
                @foreach($boats as $b)
                    <option value="{{ $b->id }}" {{ ($prefill['boat_id'] ?? '') == $b->id ? 'selected' : '' }}>
                        {{ $b->name }} — PHP {{ number_format($b->price, 2) }}/trip (up to {{ $b->capacity }} guests)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold tracking-widest uppercase text-gray-500 mb-2">Departure Date</label>
                <input type="date" name="booking_date" required min="{{ $today }}"
                       value="{{ $prefill['booking_date'] ?? '' }}"
                       class="w-full border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#964B00] transition-colors">
            </div>
            <div>
                <label class="block text-xs font-bold tracking-widest uppercase text-gray-500 mb-2">Start Time</label>
                <input type="time" name="start_time" value="{{ $prefill['start_time'] ?? '' }}"
                       class="w-full border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#964B00] transition-colors">
            </div>
            <div>
                <label class="block text-xs font-bold tracking-widest uppercase text-gray-500 mb-2">End Time</label>
                <input type="time" name="end_time" value="{{ $prefill['end_time'] ?? '' }}"
                       class="w-full border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:border-[#964B00] transition-colors">
            </div>
        </div>

        <button type="submit" class="w-full btn-primary py-3 text-xs font-bold tracking-widest uppercase">
            Continue to Guests →
        </button>
    </form>

</div>

<script>
function bookingStep1(initialType) {
    return { type: initialType || 'room' };
}
</script>
@endsection
