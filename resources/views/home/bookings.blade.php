@extends('home.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-12">
    <h1 class="text-2xl font-bold mb-6">My Bookings</h1>

    @if(count($bookings) === 0)
        <div class="p-6 bg-white rounded shadow">You have no bookings yet.</div>
    @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
                <div class="p-4 bg-white rounded shadow flex justify-between items-start">
                    <div>
                        <div class="text-sm text-gray-500">Booking #{{ $booking->id }} · {{ $booking->status ?? 'N/A' }}</div>
                        <div class="font-medium text-lg">{{ optional($booking->room)->room_name ?? 'Room' }}</div>
                        <div class="text-sm text-gray-600">{{ $booking->start_date }} → {{ $booking->end_date }} ({{ $booking->nights }} nights)</div>

                        <div class="text-sm text-gray-500 mt-1">
                            <span class="text-xs text-gray-500 uppercase">Scheduled</span>
                            <div class="mt-1">
                                <strong class="text-gray-700">{{ $booking->scheduled_checkin_at ? $booking->scheduled_checkin_at->format('M d, Y, h:i A') : ($booking->checkin_time ?? '—') }}</strong>
                                <span class="mx-2 text-gray-400">→</span>
                                <strong class="text-gray-700">{{ $booking->scheduled_checkout_at ? $booking->scheduled_checkout_at->format('M d, Y, h:i A') : ($booking->checkout_time ?? '—') }}</strong>
                            </div>
                        </div>

                        @if($booking->actual_checkin_at || $booking->actual_checkout_at)
                            <div class="text-sm text-gray-500 mt-1">
                                <span class="text-xs text-gray-500 uppercase">Actual</span>
                                <div class="mt-1">
                                    <strong class="text-gray-700">{{ $booking->actual_checkin_at ? $booking->actual_checkin_at->format('M d, Y, h:i A') : '—' }}</strong>
                                    <span class="mx-2 text-gray-400">→</span>
                                    <strong class="text-gray-700">{{ $booking->actual_checkout_at ? $booking->actual_checkout_at->format('M d, Y, h:i A') : '—' }}</strong>
                                </div>
                            </div>
                        @endif
                        <div class="text-sm text-gray-600">Deposit paid: <span class="font-semibold text-gray-800">₱{{ number_format($booking->deposit_amount ?? ($booking->total_amount * ((float) \App\Models\Setting::get('deposit_percentage', config('booking.deposit_percentage',50))/100)), 2) }}</span></div>
                        <div class="text-sm text-gray-600 mt-1">Total: ₱{{ number_format($booking->total_amount, 2) }}</div>

                        @if($booking->extensions && $booking->extensions->count())
                            <div class="mt-2 text-sm">
                                <strong>Extensions</strong>
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($booking->extensions as $ext)
                                        <li>#{{ $ext->id }} — {{ $ext->hours }}h — {{ $ext->status }} @if($ext->price) · ₱{{ number_format($ext->price,2) }}@endif</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col items-end space-y-2">
                        <button data-open-extension="{{ $booking->id }}" class="px-4 py-2 bg-[#964B00] text-white rounded">Request Extension</button>
                        @if($booking->payment_status !== 'paid')
                            <a href="{{ route('bookings.pay', ['booking' => $booking->id]) }}" class="text-sm text-blue-600">Pay deposit</a>
                        @endif

                        <!-- refund ui -->
                        @if(($booking->payment_status ?? '') === 'paid')
                            @if(empty($booking->refund_status))
                                <button data-open-refund="{{ $booking->id }}" class="mt-2 px-3 py-1 bg-red-500 text-white text-sm rounded">Request Refund</button>
                            @else
                                <div class="mt-2 text-sm">
                                    <strong>Refund:</strong>
                                    <span class="ml-2">{{ $booking->refund_status }}@if($booking->refund_amount) · ₱{{ number_format($booking->refund_amount,2) }}@endif</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- partials -->
                @include('partials.booking-extension-modal', ['booking' => $booking])
                @include('partials.booking-refund-modal', ['booking' => $booking])
            @endforeach
        </div>
    @endif
</div>
@endsection
