@extends('home.layouts.app')
@section('content')

<div class="relative w-full h-[30vh] md:h-[38vh]">
    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
         alt="Your Cart" class="absolute inset-0 object-cover w-full h-full">
    <div class="relative z-10 flex items-end justify-center w-full h-full bg-black/50 px-4 pb-10 md:pb-14">
        <h1 class="text-4xl sm:text-5xl md:text-6xl text-white text-center ">Your Cart</h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 py-10">

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-sm text-red-700">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    @if($cartRooms->isEmpty() && $cartBoats->isEmpty())
        <div class="text-center py-20">
            <span class="material-symbols-outlined text-white mb-6" style="font-size: 96px;">shopping_cart</span>
            <h2 class="text-2xl font-medium text-black mb-3 ">Your cart is empty</h2>
            <p class="text-black mb-8">Start adding rooms or boats to your cart to begin your booking.</p>
            <a href="javascript:void(0)" onclick="openBookingModal()" class="inline-block btn-primary px-8 py-3 text-sm font-medium">
                Browse Rooms & Boats
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- Cart Items --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-medium text-black ">
                        Cart Items ({{ $cartRooms->count() + $cartBoats->count() }})
                    </h2>
                    <a href="javascript:void(0)" onclick="openBookingModal()" class="text-sm text-[#964B00] hover:text-black font-medium">
                        + Add More
                    </a>
                </div>

                {{-- Room Items --}}
                @foreach($cartRooms as $room)
                    @php
                        $cart = $room->cart_data ?? [];
                        $nights = $cart['nights'] ?? 1;
                        $start = $cart['start_date'] ?? '';
                        $end = $cart['end_date'] ?? '';
                        $adults = $cart['adults'] ?? 1;
                        $children = $cart['children'] ?? 0;
                        $originalUnit = $cart['original_unit_price'] ?? $room->price;
                        $unitPrice = $cart['unit_price'] ?? $room->price;
                        $subtotal = $cart['line_total'] ?? ($unitPrice * $nights);
                        $discountApplied = isset($cart['discount']) && $cart['discount'] > 0;
                        $imageSrc = $room->images->first() ? asset('room/' . $room->images->first()->image) : asset('room/' . $room->image);
                    @endphp

                    <div class="bg-white border border-gray-200 shadow-sm overflow-hidden">
                        <div class="flex flex-col sm:flex-row">
                            {{-- Image --}}
                            <div class="w-full sm:w-48 h-48 sm:h-auto flex-shrink-0">
                                <img src="{{ $imageSrc }}" alt="{{ $room->room_name }}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.src='https://placehold.co/400x300/f3f4f6/1f2937?text=Room'">
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 p-6">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="text-lg font-medium text-black mb-1">{{ $room->room_name }}</h3>
                                        <p class="text-sm text-black">Room Stay</p>
                                    </div>
                                    <button class="remove-room-btn text-white hover:text-red-600 transition-colors p-2"
                                            data-room-id="{{ $room->id }}" title="Remove from cart">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">close</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-sm text-black mb-4">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-white" style="font-size: 16px;">calendar_month</span>
                                        <span>{{ $start }} → {{ $end }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-white" style="font-size: 16px;">bedtime</span>
                                        <span>{{ $nights }} night{{ $nights > 1 ? 's' : '' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 col-span-2">
                                        <span class="material-symbols-outlined text-white" style="font-size: 16px;">group</span>
                                        <span>{{ $adults }} adult{{ $adults > 1 ? 's' : '' }}{{ $children > 0 ? ', ' . $children . ' child' . ($children > 1 ? 'ren' : '') : '' }}</span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-end pt-3 border-t border-gray-100">
                                    <div>
                                        @if($discountApplied && $originalUnit != $unitPrice)
                                            <p class="text-sm text-white line-through">PHP {{ number_format($originalUnit * $nights, 2) }}</p>
                                            <p class="text-sm font-medium text-black">PHP {{ number_format($subtotal, 2) }}</p>
                                        @else
                                            <p class="text-sm font-medium text-black">PHP {{ number_format($subtotal, 2) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Boat Items --}}
                @foreach($cartBoats as $boat)
                    @php
                        $cart = $boat->cart_data ?? [];
                        $booking_date = $cart['booking_date'] ?? '';
                        $start_time = $cart['start_time'] ?? '';
                        $end_time = $cart['end_time'] ?? '';
                        $guests = $cart['guests'] ?? 1;
                        $subtotal = $boat->price;
                        $imageSrc = $boat->image ? asset('boats/' . $boat->image) : 'https://placehold.co/400x300/f3f4f6/1f2937?text=Boat';
                    @endphp

                    <div class="bg-white border border-gray-200 shadow-sm overflow-hidden">
                        <div class="flex flex-col sm:flex-row">
                            {{-- Image --}}
                            <div class="w-full sm:w-48 h-48 sm:h-auto flex-shrink-0">
                                <img src="{{ $imageSrc }}" alt="{{ $boat->name }}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.src='https://placehold.co/400x300/f3f4f6/1f2937?text=Boat'">
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 p-6">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="text-lg font-medium text-black mb-1">{{ $boat->name }}</h3>
                                        <p class="text-sm text-black">Boat Adventure</p>
                                    </div>
                                    <button class="remove-boat-btn text-white hover:text-red-600 transition-colors p-2"
                                            data-boat-id="{{ $boat->id }}" title="Remove from cart">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">close</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-sm text-black mb-4">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-white" style="font-size: 16px;">calendar_month</span>
                                        <span>{{ $booking_date }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-white" style="font-size: 16px;">schedule</span>
                                        <span>{{ $start_time }} – {{ $end_time }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 col-span-2">
                                        <span class="material-symbols-outlined text-white" style="font-size: 16px;">group</span>
                                        <span>{{ $guests }} guest{{ $guests > 1 ? 's' : '' }}</span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-end pt-3 border-t border-gray-100">
                                    <p class="text-sm font-medium text-black">PHP {{ number_format($subtotal, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-200 shadow-sm p-6 sticky top-28">
                    <h3 class="text-lg font-medium text-black mb-6 ">Order Summary</h3>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-black">Subtotal</span>
                            <span class="font-medium text-black">PHP {{ number_format($total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm pt-3 border-t border-gray-100">
                            <span class="text-black">Deposit Due ({{ $depositPercent }}%)</span>
                            <span class="font-medium text-[#964B00]">PHP {{ number_format($deposit, 2) }}</span>
                        </div>
                    </div>

                    <p class="text-sm text-white mb-6">* Remaining balance due at check-in</p>

                    <a href="{{ route('checkout.show', ['room_id' => $cartRooms->first()->id ?? 0]) }}"
                       class="block w-full btn-primary py-3 text-sm font-medium text-center">
                        Proceed to Checkout
                    </a>

                    <a href="javascript:void(0)" onclick="openBookingModal()"
                       class="block w-full mt-3 py-3 text-sm font-medium text-center border border-gray-200 text-black hover:border-gray-400 transition-colors">
                        Continue Booking
                    </a>
                </div>
            </div>

        </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove room from cart
    document.querySelectorAll('.remove-room-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const roomId = this.dataset.roomId;
            if (confirm('Remove this room from your cart?')) {
                fetch(`/remove-from-cart/${roomId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-From-Cart': 'true'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.reload();
                    }
                })
                .catch(err => console.error('Error removing room:', err));
            }
        });
    });

    // Remove boat from cart
    document.querySelectorAll('.remove-boat-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const boatId = this.dataset.boatId;
            if (confirm('Remove this boat from your cart?')) {
                fetch(`/remove-boat-from-cart/${boatId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-From-Cart': 'true'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.reload();
                    }
                })
                .catch(err => console.error('Error removing boat:', err));
            }
        });
    });
});
</script>

@endsection

