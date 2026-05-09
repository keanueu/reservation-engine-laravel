@php
    $cartRooms = $cartRooms ?? collect();
    $cartBoats = $cartBoats ?? collect();
    $total = 0;
    $room_color = '';
    $boat_color = ''; 
@endphp

<div id="cart-summary"
    class="border border-gray-300 p-6 sticky top-24 bg-white shadow-lg h-fit font-[Inter] text-gray-800"
    style="min-height: 320px; display: flex; flex-direction: column;">

    <h2 class="text-lg font-normal text-black tracking-tight mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
            </path>
        </svg>
        Your Selection
    </h2>
    <div class="max-h-[300px] overflow-y-auto space-y-4 flex-grow pr-2 custom-scrollbar">

        @if($cartRooms->count() || $cartBoats->count())

            @foreach($cartRooms as $room)
                @php
                    $cart = $room->cart_data ?? [];
                    $nights = $cart['nights'] ?? 1;
                    $start = $cart['start_date'] ?? '';
                    $end = $cart['end_date'] ?? '';
                    $adults = $cart['adults'] ?? '';
                    $children = $cart['children'] ?? '';
                    // Prefer stored subtotal if present (line_total), else use unit price * nights
                    $originalUnit = $cart['original_unit_price'] ?? $room->price;
                    $unitPrice = $cart['unit_price'] ?? $room->price;
                    $subtotal = $cart['line_total'] ?? ($unitPrice * $nights);
                    $discountApplied = isset($cart['discount']) && $cart['discount'] > 0;
                    $total += $subtotal;
                @endphp
                <div class="bg-gray-50 p-4 shadow-sm transition duration-200 hover:shadow-md">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-normal text-black flex items-center">
                                {{ $room->room_name }}
                            </p>
                        </div>
                        <button class="remove-room-btn text-xs text-red-500 hover:text-red-700 transition"
                            data-room-id="{{ $room->id }}" title="Remove Room">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6">
                                </path>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-y-1 text-xs text-gray-600 border-t pt-2 mt-2">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-500 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79z" />
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
                                {{ $start }}</span> &mdash; <span title="Check-out Date">{{ $end }}</span>
                        </div>
                        <div class="col-span-2 text-xs text-gray-500">
                            <span title="Guests"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 inline mr-1"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round">
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
                    $subtotal = $boat->cart_data['price'] ?? $boat->price ?? 0;
                    $total += $subtotal;
                @endphp
                <div class="bg-gray-50 p-4 shadow-sm transition duration-200 hover:shadow-md">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-normal text-black flex items-center">
                                {{ $boat->name }}
                            </p>
                        </div>
                        <button class="remove-boat-btn text-xs text-red-500 hover:text-red-700 transition"
                            data-boat-id="{{ $boat->id }}" title="Remove Boat">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6">
                                </path>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-y-1 text-xs text-gray-600 border-t pt-2 mt-2">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 432 480"
                                class="h-4 w-4 mr-1 text-gray-500 fill-current inline">
                                <path
                                    d="M384 432h43v43h-43q-44 0-85-21q-41 20-86 20t-85-20q-42 21-85 21H0v-43h43q45 0 85-28q39 27 85.5 27t85.5-27q40 28 85 28zM42 389L1 247q-3-8 1-17q4-8 13-10l28-9v-99q0-18 12.5-30.5T85 69h64V5h128v64h64q18 0 30.5 12.5T384 112v99l27 9q9 2 13 10t1 17l-40 142h-1q-48 0-85-42q-38 42-86 42t-85-42q-37 42-85 42h-1zm43-277v85l128-42l128 42v-85H85z" />
                            </svg>
                            Per Trip
                        </div>
                        <div class="text-right font-medium">
                            PHP {{ number_format($subtotal, 2) }}
                        </div>
                        <div class="col-span-2 text-xs text-gray-500">
                            <span title="Booking Date">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 16"
                                    class="h-3.5 w-3.5 mr-1 text-gray-500 fill-current inline">
                                    <path fill-rule="evenodd"
                                        d="M13 2h-1v1.5c0 .28-.22.5-.5.5h-2c-.28 0-.5-.22-.5-.5V2H6v1.5c0 .28-.22.5-.5.5h-2c-.28 0-.5-.22-.5-.5V2H2c-.55 0-1 .45-1 1v11c0 .55.45 1 1 1h11c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1zm0 12H2V5h11v9zM5 3H4V1h1v2zm6 0h-1V1h1v2zM6 7H5V6h1v1zm2 0H7V6h1v1zm2 0H9V6h1v1zm2 0h-1V6h1v1zM4 9H3V8h1v1zm2 0H5V8h1v1zm2 0H7V8h1v1zm2 0H9V8h1v1zm2 0h-1V8h1v1zm-8 2H3v-1h1v1zm2 0H5v-1h1v1zm2 0H7v-1h1v1zm2 0H9v-1h1v1zm2 0h-1v-1h1v1zm-8 2H3v-1h1v1zm2 0H5v-1h1v1zm2 0H7v-1h1v1zm2 0H9v-1h1v1z" />
                                </svg> {{ $booking_date }}</span>
                        </div>
                        <div class="col-span-2 text-xs text-gray-500">
                            <span title="Time Slot">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 432 432"
                                    class="h-4 w-4 mr-1 text-gray-500 fill-current inline">
                                    <path
                                        d="M213.5 3q88.5 0 151 62.5T427 216t-62.5 150.5t-151 62.5t-151-62.5T0 216T62.5 65.5T213.5 3zm0 384q70.5 0 120.5-50t50-121t-50-121t-120.5-50T93 95T43 216t50 121t120.5 50zM224 109v112l96 57l-16 27l-112-68V109h32z" />
                                </svg>
                                {{ $start_time }} &mdash; {{ $end_time }} ({{ $guests }} guests)</span>
                        </div>
                    </div>
                </div>
            @endforeach
            <a href="/home/roomcart"
                class="text-xs  text-[#964B00] hover:text-[#7a3c00] transition duration-150 flex items-center justify-center mt-3 pt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add More Items
            </a>
        @else
            <div class="flex items-center justify-center min-h-[120px] bg-gray-50 border border-dashed border-gray-300">
                <p class="text-sm text-gray-500 text-center w-full p-4">Your cart is empty. Start planning your trip!</p>
            </div>
        @endif

    </div>
    <div class="mt-6 pt-4 border-t border-gray-200">
        <div class="flex justify-between items-center mb-2">
            <p class="text-sm text-gray-600">Deposit ({{ \App\Models\Setting::get('deposit_percentage', config('booking.deposit_percentage', 50)) }}%):</p>
            <p class="text-sm font-medium text-black">
                PHP {{ number_format($deposit ?? ($total * (\App\Models\Setting::get('deposit_percentage', config('booking.deposit_percentage', 50))/100)), 2) }}
            </p>
        </div>
        <div class="flex justify-between items-center mb-5">
            <p class="text-sm font-medium text-black">Total Due:</p>
            <p class="text-sm font-medium text-black">
                PHP {{ number_format($total, 2) }}
            </p>
        </div>

        @if($cartRooms->count() || $cartBoats->count())
            <a href="{{ route('checkout.show', ['room_id' => $cartRooms->first()->id ?? 0]) }}"
                class="w-full text-xs block bg-black hover:bg-[#964B00] px-10 py-3 text-white transition text-center tracking-widest  shadow-lg">
                Proceed to Checkout
            </a>
        @else
            <button
                class="w-full bg-gray-300 text-gray-600 mt-5 py-3 font-medium cursor-not-allowed uppercase tracking-widest"
                disabled>
                Checkout
            </button>
        @endif
    </div>
</div>
