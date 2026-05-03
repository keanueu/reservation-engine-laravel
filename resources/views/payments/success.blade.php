@extends('home.layouts.app')

@section('content')
<div class="min-h-screen bg-white flex flex-col items-center justify-center py-16 px-4">
    <div class="max-w-2xl w-full mx-auto" data-reveal>

        {{-- Success icon --}}
        <div class="flex justify-center mb-8">
            <div class="w-20 h-20 bg-green-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600" style="font-size: 48px;">check_circle</span>
            </div>
        </div>

        {{-- Heading --}}
        <div class="text-center mb-8">
            <h1 class="text-4xl text-gray-900 tracking-tight mb-3">Thank you for your payment!</h1>
            <p class="text-base text-gray-600 leading-relaxed">
                Your booking is being confirmed. You will receive a confirmation email shortly.
            </p>
        </div>

        {{-- Booking summary card --}}
        @if($bookings->isNotEmpty())
        <div class="bg-white border border-gray-200 shadow-sm mb-8" data-reveal data-reveal-delay="1">
            <div class="px-6 py-5 border-b border-gray-200">
                <h2 class="text-lg text-gray-900">Booking summary</h2>
                <p class="text-sm text-gray-500 mt-1">Group reference: <span class="font-mono text-xs text-gray-700">{{ $groupId }}</span></p>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($bookings as $booking)
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#964B00] text-white flex items-center justify-center flex-shrink-0">
                                @if(isset($booking->room_id))
                                    <span class="material-symbols-outlined" style="font-size:18px;">bed</span>
                                @else
                                    <span class="material-symbols-outlined" style="font-size:18px;">sailing</span>
                                @endif
                            </div>
                            <div>
                                @if(isset($booking->room_id))
                                    <p class="text-sm text-gray-900">{{ optional($booking->room)->room_name ?? 'Room Booking' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ \Carbon\Carbon::parse($booking->start_date)->format('M j') }}
                                        –
                                        {{ \Carbon\Carbon::parse($booking->end_date)->format('M j, Y') }}
                                        · {{ $booking->nights }} night{{ $booking->nights != 1 ? 's' : '' }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-900">{{ optional($booking->boat)->name ?? 'Boat Booking' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('M j, Y') }}
                                        · {{ $booking->start_time }} – {{ $booking->end_time }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-4">
                            <p class="text-sm text-gray-900">₱{{ number_format($booking->total_amount, 2) }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold tracking-wide uppercase
                                {{ $booking->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $booking->payment_status === 'paid' ? 'Paid' : 'Processing' }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Deposit paid row --}}
            @if($depositPaid > 0)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-700">Deposit paid</p>
                    <p class="text-xs text-gray-500 mt-0.5">Remaining balance due at check-in</p>
                </div>
                <p class="text-lg text-gray-900">₱{{ number_format($depositPaid, 2) }}</p>
            </div>
            @endif
        </div>
        @endif

        {{-- What happens next --}}
        <div class="bg-white border border-gray-200 shadow-sm mb-8 p-6" data-reveal data-reveal-delay="2">
            <h3 class="text-base text-gray-900 mb-4">What happens next</h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#964B00] flex-shrink-0 mt-0.5" style="font-size:18px;">mail</span>
                    <p class="text-sm text-gray-600">A confirmation email will be sent to your inbox once payment is verified.</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#964B00] flex-shrink-0 mt-0.5" style="font-size:18px;">login</span>
                    <p class="text-sm text-gray-600">Present your booking reference at check-in. Remaining balance is due on arrival.</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[#964B00] flex-shrink-0 mt-0.5" style="font-size:18px;">support_agent</span>
                    <p class="text-sm text-gray-600">Questions? Contact our front desk — we're happy to help.</p>
                </div>
            </div>
        </div>

        {{-- Action buttons --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center" data-reveal data-reveal-delay="3">
            <a href="{{ url('/') }}" class="btn-primary px-8 py-3.5 text-sm font-bold tracking-widest uppercase flex items-center justify-center gap-2">
                <span class="material-symbols-outlined" style="font-size:16px;">home</span>
                Back to home
            </a>
            <a href="{{ route('my.bookings') }}" class="btn-outline px-8 py-3.5 text-sm font-bold tracking-widest uppercase flex items-center justify-center gap-2">
                <span class="material-symbols-outlined" style="font-size:16px;">calendar_month</span>
                View my bookings
            </a>
        </div>

    </div>
</div>
@endsection
