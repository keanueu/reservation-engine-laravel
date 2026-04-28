@php
$cartRooms = $cartRooms ?? collect();
$cartBoats = $cartBoats ?? collect();
$total = 0;
// Combine and sort items if needed, but keeping them separate for distinct styling is simpler
@endphp

<div class="font-[Manrope] text-black w-full lg:max-w-md">
    <h2 class="text-lg font-normal tracking-tight mb-6 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-[#964B00]" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        Your Cart Summary ({{ $cartRooms->count() + $cartBoats->count() }}
        Item{{ ($cartRooms->count() + $cartBoats->count()) > 1 ? 's' : '' }})
    </h2>

    <div class="max-h-[380px] overflow-y-auto space-y-5 pr-2 custom-scrollbar">

        @if($cartRooms->count() || $cartBoats->count())

            @foreach($cartRooms as $room)
                @php
                    $cart = $room->cart_data ?? [];
                    $nights = $cart['nights'] ?? 1;
                    $start = $cart['start_date'] ?? '';
                    $end = $cart['end_date'] ?? '';
                    $adults = $cart['adults'] ?? '';
                    $children = $cart['children'] ?? '';
                    // prefer stored subtotal (line_total) if present, else unit price * nights
                    $originalUnit = $cart['original_unit_price'] ?? $room->price;
                    $unitPrice = $cart['unit_price'] ?? $room->price;
                    $subtotal = $cart['line_total'] ?? ($unitPrice * $nights);
                    $discountApplied = isset($cart['discount']) && $cart['discount'] > 0;
                    $total += $subtotal;
                @endphp
                <div class="p-4 transition duration-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-normal text-black flex items-center">
                                {{ $room->room_name }}
                            </p>
                        </div>
                        <button class="remove-room-btn text-xs text-red-500 hover:text-red-700 transition"
                            data-room-id="{{ $room->id }}" title="Remove Room">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6">
                                </path>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-y-1 text-xs text-gray-500 border-t pt-2 mt-2">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-500 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79z" />
                            </svg>
                            {{ $nights }} Night{{ $nights > 1 ? 's' : '' }}
                        </div>
                        <div class="text-right font-medium">
                            @if($discountApplied && $originalUnit != $unitPrice)
                                <div class="text-xs text-gray-400 line-through">PHP {{ number_format($originalUnit * $nights, 2) }}</div>
                                <div class="font-medium">PHP {{ number_format($subtotal, 2) }}</div>
                            @else
                                PHP {{ number_format($subtotal, 2) }}
                            @endif
                        </div>
                        <div class="col-span-2 text-xs text-gray-500">
                            <span title="Check-in Date">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 16"
                                    class="h-3.5 w-3.5 mr-1 text-gray-500 fill-current inline">
                                    <path fill-rule="evenodd"
                                        d="M13 2h-1v1.5c0 .28-.22.5-.5.5h-2c-.28 0-.5-.22-.5-.5V2H6v1.5c0 .28-.22.5-.5.5h-2c-.28 0-.5-.22-.5-.5V2H2c-.55 0-1 .45-1 1v11c0 .55.45 1 1 1h11c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1zm0 12H2V5h11v9zM5 3H4V1h1v2zm6 0h-1V1h1v2zM6 7H5V6h1v1zm2 0H7V6h1v1zm2 0H9V6h1v1zm2 0h-1V6h1v1zM4 9H3V8h1v1zm2 0H5V8h1v1zm2 0H7V8h1v1zm2 0H9V8h1v1zm2 0h-1V8h1v1zm-8 2H3v-1h1v1zm2 0H5v-1h1v1zm2 0H7v-1h1v1zm2 0H9v-1h1v1zm2 0h-1v-1h1v1zm-8 2H3v-1h1v1zm2 0H5v-1h1v1zm2 0H7v-1h1v1zm2 0H9v-1h1v1z" />
                                </svg>
                                {{ $start }}</span> to <span title="Check-out Date">{{ $end }}</span>
                        </div>
                        <div class="col-span-2 text-xs text-gray-500">
                            <span title="Guests"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 inline mr-1"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg> {{ $adults }}
                                adult{{ $adults > 1 ? 's' : '' }}{{ $children > 0 ? ', ' . $children . ' child' . ($children > 1 ? 'ren' : '') : '' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach

            @foreach($cartBoats as $boat)
                @php
                    $cart = $boat->cart_data ?? [];
                    $booking_date = $cart['booking_date'] ?? '';
                    $start_time = $cart['start_time'] ?? '';
                    $end_time = $cart['end_time'] ?? '';
                    $guests = $cart['guests'] ?? '';
                    $subtotal = $boat->price;
                    $total += $subtotal;
                @endphp
                <div class="p-4 transition duration-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-base font-semibold text-gray-900 flex items-center">
                                {{ $boat->name }}
                            </p>
                        </div>
                        <button class="remove-boat-btn text-xs text-red-500 hover:text-red-700 transition"
                            data-boat-id="{{ $boat->id }}" title="Remove Boat">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-y-1 text-xs text-gray-600 border-t pt-2 mt-2">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Per Trip
                        </div>
                        <div class="text-right">
                            PHP {{ number_format($subtotal, 2) }}
                        </div>
                        <div class="col-span-2 text-xs text-gray-500">
                            <span title="Booking Date"> {{ $booking_date }}</span>
                        </div>
                        <div class="col-span-2 text-xs text-gray-500">
                            <span title="Time Slot"> {{ $start_time }} - {{ $end_time }} ({{ $guests }} guests)</span>
                        </div>
                    </div>
                </div>
            @endforeach

        @else
            <div class="flex items-center justify-center min-h-[120px] bg-gray-50 border border-dashed border-gray-300">
                <p class="text-sm text-gray-500 text-center p-4">Your cart is empty. Add a room or boat to start booking!</p>
            </div>
        @endif

    </div>

    <div class="mt-6 pt-4 border-t border-gray-200">
        <div class="flex justify-between items-center mb-5">
            <p class="text-sm font-medium text-black">Total Due:</p>
            <p class="text-sm font-medium text-black font-[Manrope]">
                PHP {{ number_format($total, 2) }}
            </p>
        </div>

        @if($cartRooms->count() || $cartBoats->count())
            <a href="{{ route('checkout.show', ['room_id' => $cartRooms->first()->id ?? 0]) }}"
                class="w-full text-xs block bg-black hover:bg-[#964B00] px-10 py-3 text-white transition text-center tracking-widest  shadow-lg">
                Proceed to Checkout
            </a>
        @else
            <button class="w-full text-xs bg-[#964B00] text-white mt-5 py-3  cursor-not-allowed uppercase tracking-widest" disabled>
                Checkout
            </button>
        @endif
    </div>
</div>