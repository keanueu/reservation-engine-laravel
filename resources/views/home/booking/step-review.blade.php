@extends('home.layouts.app')
@section('content')

@php
    $isBoat   = $step1['type'] === 'boat';
    $name     = $isBoat ? $model->name : $model->room_name;
    $guestStr = $step2['adults'] . ' adult' . ($step2['adults'] > 1 ? 's' : '')
              . ($step2['children'] > 0 ? ', ' . $step2['children'] . ' child' . ($step2['children'] > 1 ? 'ren' : '') : '');
@endphp

<div class="relative w-full h-[30vh] md:h-[38vh]">
    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
         alt="Review" class="absolute inset-0 object-cover w-full h-full">
    <div class="relative z-10 flex items-end justify-center w-full h-full bg-black/50 px-4 pb-10 md:pb-14">
        <h1 class="text-4xl sm:text-5xl md:text-6xl text-white text-center font-[Inter]">Review Your Booking</h1>
    </div>
</div>

<div class="max-w-2xl mx-auto px-6 py-10">

    {{-- Progress bar --}}
    @include('home.booking._progress', ['current' => 3])

    {{-- Summary card --}}
    <div class="border border-gray-200 bg-white mb-8">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-700">Booking Summary</h2>
        </div>

        <div class="divide-y divide-gray-100">
            <div class="flex justify-between px-6 py-3 text-sm">
                <span class="text-gray-500">Type</span>
                <span class="font-semibold text-gray-900">{{ $isBoat ? 'Boat Trip' : 'Room Stay' }}</span>
            </div>
            <div class="flex justify-between px-6 py-3 text-sm">
                <span class="text-gray-500">{{ $isBoat ? 'Boat' : 'Room' }}</span>
                <span class="font-semibold text-gray-900">{{ $name }}</span>
            </div>

            @if($isBoat)
                <div class="flex justify-between px-6 py-3 text-sm">
                    <span class="text-gray-500">Date</span>
                    <span class="font-semibold text-gray-900">{{ $step1['booking_date'] }}</span>
                </div>
                <div class="flex justify-between px-6 py-3 text-sm">
                    <span class="text-gray-500">Time Slot</span>
                    <span class="font-semibold text-gray-900">{{ $step1['start_time'] }} – {{ $step1['end_time'] }}</span>
                </div>
            @else
                <div class="flex justify-between px-6 py-3 text-sm">
                    <span class="text-gray-500">Check-in</span>
                    <span class="font-semibold text-gray-900">{{ $step1['checkin'] }} at {{ $step1['checkin_time'] }}</span>
                </div>
                <div class="flex justify-between px-6 py-3 text-sm">
                    <span class="text-gray-500">Check-out</span>
                    <span class="font-semibold text-gray-900">{{ $step1['checkout'] }} at {{ $step1['checkout_time'] }}</span>
                </div>
                <div class="flex justify-between px-6 py-3 text-sm">
                    <span class="text-gray-500">Duration</span>
                    <span class="font-semibold text-gray-900">{{ $nights }} night{{ $nights > 1 ? 's' : '' }}</span>
                </div>
            @endif

            <div class="flex justify-between px-6 py-3 text-sm">
                <span class="text-gray-500">Guests</span>
                <span class="font-semibold text-gray-900">{{ $guestStr }}</span>
            </div>

            @if(!$isBoat && $unitPrice < $model->price)
                <div class="flex justify-between px-6 py-3 text-sm">
                    <span class="text-gray-500">Original Price</span>
                    <span class="text-gray-400 line-through">PHP {{ number_format($model->price * $nights, 2) }}</span>
                </div>
            @endif

            <div class="flex justify-between px-6 py-3 text-sm bg-gray-50">
                <span class="font-bold text-gray-700">Estimated Total</span>
                <span class="font-bold text-[#964B00]">PHP {{ number_format($total, 2) }}</span>
            </div>

            <div class="flex justify-between px-6 py-3 text-sm bg-[#964B00]/5">
                <span class="text-gray-600">Deposit Due Now ({{ $depositPercent }}%)</span>
                <span class="font-bold text-[#964B00]">PHP {{ number_format($deposit, 2) }}</span>
            </div>
        </div>
    </div>

    <p class="text-xs text-gray-400 mb-6">* Final price may vary. A deposit will be collected at checkout.</p>

    <form action="{{ route('booking.review.post') }}" method="POST">
        @csrf
        <div class="flex gap-3">
            <a href="{{ route('booking.guests') }}"
               class="flex-1 py-3 text-xs font-bold tracking-widest uppercase border border-gray-200 text-gray-600 hover:border-gray-400 text-center transition-colors">
                ← Back
            </a>
            <button type="submit" class="flex-1 btn-primary py-3 text-xs font-bold tracking-widest uppercase">
                Add to Cart
            </button>
        </div>
    </form>
</div>

@endsection
