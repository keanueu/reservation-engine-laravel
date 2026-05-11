{{--
    UNIVERSAL BOOKING MODAL
    ========================
    Trigger from anywhere:
      openBookingModal()                    — no room pre-selected (user picks from dropdown)
      openBookingModal(roomId, roomName, price, maxGuests)  — room pre-selected

    The modal collects: check-in, check-out, check-in time, check-out time, adults, children
    then submits to the cart via POST /add-to-cart/{room_id}
--}}

<div id="universal-booking-modal"
     x-data="universalBooking()"
     x-show="open"
     x-cloak
     @open-booking-modal.window="handleOpen($event.detail)"
     @keydown.escape.window="close()"
     class="fixed inset-0 z-[99998] flex items-end sm:items-center justify-center"
     role="dialog" aria-modal="true" aria-labelledby="ubm-title">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50" @click="close()"></div>

    {{-- Panel --}}
    <div class="relative w-full sm:max-w-xl bg-white shadow-2xl z-10 flex flex-col max-h-[95vh] overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-8">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 id="ubm-title" class="text-lg font-bold text-gray-900">
                    Book Your Stay
                </h2>
                <p class="text-xs text-gray-500 mt-0.5" x-text="roomName ? 'Selected: ' + roomName : 'Choose a room and your dates'"></p>
            </div>
            <button @click="close()" class="p-2 text-gray-400 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Progress steps --}}
        <div class="flex items-center px-6 py-4 bg-gray-50 border-b border-gray-100 gap-0">
            @php $steps = ['Dates','Guests','Review']; @endphp
            @foreach($steps as $si => $sl)
                <div class="flex items-center" :class="'flex-1'">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 flex items-center justify-center text-xs font-bold transition-all"
                             :style="step >= {{ $si+1 }} ? 'background:var(--brand,#964B00);color:#fff;' : 'background:#e5e7eb;color:#6b7280;'">
                            <span x-show="step > {{ $si+1 }}">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span x-show="step <= {{ $si+1 }}">{{ $si+1 }}</span>
                        </div>
                        <span class="text-xs font-semibold hidden sm:block"
                              :style="step >= {{ $si+1 }} ? 'color:var(--brand,#964B00);' : 'color:#9ca3af;'">{{ $sl }}</span>
                    </div>
                    @if(!$loop->last)
                        <div class="flex-1 h-px mx-3 transition-all"
                             :style="step > {{ $si+1 }} ? 'background:var(--brand,#964B00);' : 'background:#e5e7eb;'"></div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Body --}}
        <div class="px-6 py-6 flex-1">

            {{-- STEP 1: Dates --}}
            <div x-show="step === 1" x-transition>
                {{-- Room selector (shown only when no room pre-selected) --}}
                <div x-show="!roomId" class="mb-5">
                    <label class="block text-xs font-bold  text-gray-500 mb-2">Select Room</label>
                    <select x-model="roomId" @change="updateRoomFromSelect($event)"
                            class="w-full border border-gray-200 px-4 py-3 text-sm text-gray-700 bg-white focus:outline-none focus:border-[#964B00] transition-colors">
                        <option value="">— Choose a room —</option>
                        @if(isset($rooms))
                            @foreach($rooms as $r)
                                <option value="{{ $r->id }}"
                                        data-name="{{ $r->room_name }}"
                                        data-price="{{ $r->price }}"
                                        data-max="{{ $r->accommodates }}">
                                    {{ $r->room_name }} — PHP {{ number_format($r->price, 2) }}/night
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Selected room pill --}}
                <div x-show="roomId" class="mb-5 flex items-center justify-between px-4 py-3 border border-[#964B00] bg-[#964B00]/5">
                    <div>
                        <p class="text-xs font-bold  text-[#964B00]">Selected Room</p>
                        <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="roomName"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Per night</p>
                        <p class="text-sm font-bold text-gray-900">PHP <span x-text="Number(roomPrice).toLocaleString('en-PH',{minimumFractionDigits:2})"></span></p>
                    </div>
                </div>

                {{-- Date grid --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold  text-gray-500 mb-2">Check-in Date</label>
                        <input type="date" x-model="checkin" :min="today"
                               class="w-full border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:border-[#964B00] transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-bold  text-gray-500 mb-2">Check-out Date</label>
                        <input type="date" x-model="checkout" :min="checkin || today"
                               class="w-full border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:border-[#964B00] transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-bold  text-gray-500 mb-2">Check-in Time</label>
                        <input type="time" x-model="checkinTime"
                               class="w-full border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:border-[#964B00] transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-bold  text-gray-500 mb-2">Check-out Time</label>
                        <input type="time" x-model="checkoutTime"
                               class="w-full border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:border-[#964B00] transition-colors">
                    </div>
                </div>

                {{-- Nights summary --}}
                <div x-show="nights > 0" class="flex items-center gap-2 px-4 py-3 bg-gray-50 border border-gray-100 text-sm">
                    <svg class="w-4 h-4 text-[#964B00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="font-semibold text-gray-700" x-text="nights + ' night' + (nights > 1 ? 's' : '')"></span>
                    <span class="text-gray-400">·</span>
                    <span class="text-gray-500" x-text="formatDate(checkin) + ' → ' + formatDate(checkout)"></span>
                </div>

                {{-- Step 1 error --}}
                <p x-show="errors.step1" x-text="errors.step1" class="mt-3 text-xs font-semibold text-red-600"></p>
            </div>

            {{-- STEP 2: Guests --}}
            <div x-show="step === 2" x-transition>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold  text-gray-500 mb-2">Adults</label>
                        <div class="flex items-center border border-gray-200">
                            <button @click="adults = Math.max(1, adults - 1)" type="button"
                                    class="w-12 h-12 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors text-xl font-bold">−</button>
                            <span class="flex-1 text-center text-sm font-bold text-gray-900" x-text="adults"></span>
                            <button @click="adults = Math.min(maxGuests, adults + 1)" type="button"
                                    class="w-12 h-12 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors text-xl font-bold">+</button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Max <span x-text="maxGuests"></span> guests total for this room</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold  text-gray-500 mb-2">Children</label>
                        <div class="flex items-center border border-gray-200">
                            <button @click="children = Math.max(0, children - 1)" type="button"
                                    class="w-12 h-12 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors text-xl font-bold">−</button>
                            <span class="flex-1 text-center text-sm font-bold text-gray-900" x-text="children"></span>
                            <button @click="children = Math.min(maxGuests - adults, children + 1)" type="button"
                                    class="w-12 h-12 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors text-xl font-bold">+</button>
                        </div>
                    </div>
                </div>
                <p x-show="errors.step2" x-text="errors.step2" class="mt-3 text-xs font-semibold text-red-600"></p>
            </div>

            {{-- STEP 3: Review --}}
            <div x-show="step === 3" x-transition>
                <div class="space-y-3">
                    <h3 class="text-sm font-bold text-gray-700 mb-4">Booking Summary</h3>

                    <div class="divide-y divide-gray-100 border border-gray-100">
                        <div class="flex justify-between px-4 py-3 text-sm">
                            <span class="text-gray-500 font-medium">Room</span>
                            <span class="font-semibold text-gray-900" x-text="roomName"></span>
                        </div>
                        <div class="flex justify-between px-4 py-3 text-sm">
                            <span class="text-gray-500 font-medium">Check-in</span>
                            <span class="font-semibold text-gray-900" x-text="formatDate(checkin) + ' at ' + (checkinTime || '—')"></span>
                        </div>
                        <div class="flex justify-between px-4 py-3 text-sm">
                            <span class="text-gray-500 font-medium">Check-out</span>
                            <span class="font-semibold text-gray-900" x-text="formatDate(checkout) + ' at ' + (checkoutTime || '—')"></span>
                        </div>
                        <div class="flex justify-between px-4 py-3 text-sm">
                            <span class="text-gray-500 font-medium">Duration</span>
                            <span class="font-semibold text-gray-900" x-text="nights + ' night' + (nights > 1 ? 's' : '')"></span>
                        </div>
                        <div class="flex justify-between px-4 py-3 text-sm">
                            <span class="text-gray-500 font-medium">Guests</span>
                            <span class="font-semibold text-gray-900" x-text="adults + ' adult' + (adults > 1 ? 's' : '') + (children > 0 ? ', ' + children + ' child' + (children > 1 ? 'ren' : '') : '')"></span>
                        </div>
                        <div class="flex justify-between px-4 py-3 text-sm bg-gray-50">
                            <span class="font-bold text-gray-700">Estimated Total</span>
                            <span class="font-bold text-[#964B00]">PHP <span x-text="(roomPrice * nights).toLocaleString('en-PH',{minimumFractionDigits:2})"></span></span>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 mt-2">* Final price may vary. A deposit will be collected at checkout.</p>
                </div>
            </div>
        </div>

        {{-- Footer actions --}}
        <div class="px-6 py-5 border-t border-gray-100 flex items-center justify-between gap-3 bg-white">
            <button x-show="step > 1" @click="step--" type="button"
                    class="px-5 py-2.5 text-xs font-bold  border border-gray-200 text-gray-600 hover:border-gray-400 transition-colors">
                ← Back
            </button>
            <div x-show="step === 1" class="text-xs text-gray-400">Step 1 of 3</div>

            {{-- Next: step 1 → 2 --}}
            <button x-show="step === 1" @click="nextStep1()" type="button"
                    class="ml-auto btn-primary px-8 py-2.5 text-xs font-bold ">
                Continue →
            </button>

            {{-- Next: step 2 → 3 --}}
            <button x-show="step === 2" @click="nextStep2()" type="button"
                    class="ml-auto btn-primary px-8 py-2.5 text-xs font-bold ">
                Review →
            </button>

            {{-- Submit: step 3 → cart --}}
            <button x-show="step === 3" @click="submitToCart()" type="button"
                    :disabled="loading"
                    class="ml-auto btn-primary px-8 py-2.5 text-xs font-bold  flex items-center gap-2 disabled:opacity-60">
                <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span x-text="loading ? 'Adding...' : 'Add to Cart'"></span>
            </button>
        </div>
    </div>
</div>

{{-- Hidden form for cart submission --}}
<form id="ubm-cart-form" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="startDate"   id="ubm-startDate">
    <input type="hidden" name="endDate"     id="ubm-endDate">
    <input type="hidden" name="start_time"  id="ubm-startTime">
    <input type="hidden" name="end_time"    id="ubm-endTime">
    <input type="hidden" name="adults"      id="ubm-adults">
    <input type="hidden" name="children"    id="ubm-children">
</form>

<script>
function universalBooking() {
    return {
        open:         false,
        step:         1,
        loading:      false,
        roomId:       '',
        roomName:     '',
        roomPrice:    0,
        maxGuests:    13,
        checkin:      '',
        checkout:     '',
        checkinTime:  '13:00',
        checkoutTime: '11:00',
        adults:       1,
        children:     0,
        errors:       { step1: '', step2: '' },
        today:        new Date().toISOString().split('T')[0],

        get nights() {
            if (!this.checkin || !this.checkout) return 0;
            const diff = (new Date(this.checkout) - new Date(this.checkin)) / 86400000;
            return diff > 0 ? diff : 0;
        },

        handleOpen(detail) {
            this.step     = 1;
            this.loading  = false;
            this.errors   = { step1: '', step2: '' };
            this.roomId   = detail?.roomId   || '';
            this.roomName = detail?.roomName || '';
            this.roomPrice= detail?.price    || 0;
            this.maxGuests= detail?.maxGuests|| 13;
            this.children = 0;

            // Pre-fill adults from hero guests selector or passed value
            const heroGuests = parseInt(document.getElementById('hero-guests')?.value) || 1;
            this.adults = detail?.adults || heroGuests;

            // Pre-fill dates from passed values or hero search bar
            const heroIn  = document.getElementById('hero-checkin');
            const heroOut = document.getElementById('hero-checkout');
            this.checkin  = detail?.checkin  || heroIn?.value  || '';
            this.checkout = detail?.checkout || heroOut?.value || '';

            this.open = true;
            document.body.style.overflow = 'hidden';
        },

        close() {
            this.open = false;
            document.body.style.overflow = '';
        },

        updateRoomFromSelect(e) {
            const opt = e.target.selectedOptions[0];
            this.roomName  = opt?.dataset?.name  || '';
            this.roomPrice = parseFloat(opt?.dataset?.price || 0);
            this.maxGuests = parseInt(opt?.dataset?.max   || 13);
        },

        nextStep1() {
            this.errors.step1 = '';
            if (!this.roomId)   { this.errors.step1 = 'Please select a room.'; return; }
            if (!this.checkin)  { this.errors.step1 = 'Please select a check-in date.'; return; }
            if (!this.checkout) { this.errors.step1 = 'Please select a check-out date.'; return; }
            if (this.nights < 1){ this.errors.step1 = 'Check-out must be after check-in.'; return; }
            this.step = 2;
        },

        nextStep2() {
            this.errors.step2 = '';
            if (this.adults + this.children > this.maxGuests) {
                this.errors.step2 = 'Total guests exceed room capacity (' + this.maxGuests + ').';
                return;
            }
            this.step = 3;
        },

        submitToCart() {
            this.loading = true;
            const form = document.getElementById('ubm-cart-form');
            form.action = '/add-to-cart/' + this.roomId;
            document.getElementById('ubm-startDate').value  = this.checkin;
            document.getElementById('ubm-endDate').value    = this.checkout;
            document.getElementById('ubm-startTime').value  = this.checkinTime;
            document.getElementById('ubm-endTime').value    = this.checkoutTime;
            document.getElementById('ubm-adults').value     = this.adults;
            document.getElementById('ubm-children').value   = this.children;
            form.submit();
        },

        formatDate(d) {
            if (!d) return '—';
            const dt = new Date(d + 'T00:00:00');
            return dt.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
        }
    };
}

// Global trigger function — call from anywhere
function openBookingModal(roomId, roomName, price, maxGuests, checkin, checkout) {
    window.dispatchEvent(new CustomEvent('open-booking-modal', {
        detail: { roomId, roomName, price, maxGuests, checkin, checkout }
    }));
}
</script>
