        <div class="bg-white p-8 border border-gray-300 shadow-sm space-y-8">
                <h2 class="text-xl font-medium flex items-center text-black border-b border-gray-300 pb-3">
                    Guest Contact Details
                </h2>

                {{-- Display validation errors and flash messages so we can surface why the form reloads --}}
                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-sm text-red-800">
                        <strong>There were some problems with your input:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-sm text-red-800">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-sm text-green-800">{{ session('success') }}</div>
                @endif

                <form action="{{ route('bookings.store') }}" method="POST">
                    @csrf
                    @foreach($cartRooms as $room)
                        @php $cart = $room->cart_data ?? []; @endphp
                        <input type="hidden" name="room_ids[]" value="{{ $room->id }}">
                        <input type="hidden" name="start_dates[]" value="{{ $cart['start_date'] ?? '' }}">
                        <input type="hidden" name="end_dates[]" value="{{ $cart['end_date'] ?? '' }}">
                        <input type="hidden" name="adults[]" value="{{ $cart['adults'] ?? '' }}">
                        <input type="hidden" name="children[]" value="{{ $cart['children'] ?? '' }}">
                        <input type="hidden" name="nights[]" value="{{ $cart['nights'] ?? '' }}">
                    @endforeach
                    @if(!empty($cartBoats) && count($cartBoats) > 0)
                        @foreach($cartBoats as $boat)
                            @php $b = $boat->cart_data ?? []; @endphp
                            <input type="hidden" name="boat_ids[]" value="{{ $boat->id }}">
                            <input type="hidden" name="booking_dates[]" value="{{ $b['booking_date'] ?? '' }}">
                            <input type="hidden" name="start_times[]" value="{{ $b['start_time'] ?? '' }}">
                            <input type="hidden" name="end_times[]" value="{{ $b['end_time'] ?? '' }}">
                            <input type="hidden" name="boat_guests[]" value="{{ $b['guests'] ?? 1 }}">
                            <input type="hidden" name="boat_prices[]" value="{{ $b['price'] ?? $boat->price ?? 0 }}">
                        @endforeach
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm  mb-1 text-black">Full Name
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name"
                                class="w-full border border-gray-300 text-sm p-3 focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition"
                                value="{{ Auth::id() ? Auth::user()->name : '' }}" required />
                        </div>
                        <div>
                            <label class="block text-sm  mb-1 text-black">Email
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email"
                                class="w-full border border-gray-300 text-sm p-3 focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition"
                                value="{{ Auth::id() ? Auth::user()->email : '' }}" required />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm  mb-1 text-black">Phone
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="phone"
                                class="w-full border border-gray-300 text-sm p-3 focus:ring-2 focus:ring-brand-gold focus:border-brand-gold transition"
                                value="{{ Auth::id() ? Auth::user()->phone : '' }}" required />
                        </div>
                    </div>
                    <div class="pt-10">
                        <h3 class="text-base font-medium flex items-center text-black">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Payment Method
                        </h3>

                        <div class="space-y-2">
                            <div class="flex items-start space-x-3 p-4 bg-blue-50 border border-blue-300">
                                <input type="radio" id="pay_at_hotel" name="payment_method" value="pay_at_hotel" checked
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                <label for="pay_at_hotel" class="text-sm font-medium text-blue-800">
                                    Pay at Hotel / Resort
                                    <p class="text-sm  text-blue-700">
                                        Your reservation is secured now. The full amount (PHP{{ number_format($total, 2) }}) will
                                        be settled upon check-in.
                                    </p>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="text-center py-4">
                        <p class="text-sm font-medium text-black">Amount Due Now ({{ \App\Models\Setting::get('deposit_percentage', config('booking.deposit_percentage', 50)) }}% deposit)</p>
                        <p class="text-lg font-medium text-brand-gold">PHP {{ number_format($deposit ?? ($total * (\App\Models\Setting::get('deposit_percentage', config('booking.deposit_percentage', 50))/100)), 2) }}</p>
                        <p class="text-sm text-black mt-2">Total Payable at Check-in: <strong>PHP {{ number_format($total, 2) }}</strong></p>
                    </div>
                    <button type="submit"
                        class="w-full bg-[#964B00] hover:bg-black text-white text-sm font-medium py-3 shadow-md transition duration-300">
                        Confirm & Book Now
                    </button>

                </form>
            </div>

