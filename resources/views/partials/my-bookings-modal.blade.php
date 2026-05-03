<div id="my-bookings-modal"
     class="fixed inset-0 hidden items-center justify-end z-[9999] font-[Manrope]"
     style="background:rgba(0,0,0,0.55);">

    {{-- Slide-over panel --}}
    <div id="my-bookings-panel"
         class="relative h-full w-full max-w-lg bg-white shadow-2xl flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 flex-shrink-0">
            <div>
                <h2 class="text-xl font-bold text-gray-900">My Bookings</h2>
                <p class="text-xs text-gray-400 mt-0.5 font-medium">Your reservations at Cabanas</p>
            </div>
            <button data-close-mybookings
                    class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Filter tabs --}}
        <div class="flex border-b border-gray-100 flex-shrink-0 px-6">
            <button id="tab-all"    onclick="filterBookings('all')"
                    class="booking-tab active-tab px-4 py-3 text-xs font-bold tracking-widest uppercase border-b-2 transition-colors mr-4">All</button>
            <button id="tab-upcoming" onclick="filterBookings('upcoming')"
                    class="booking-tab px-4 py-3 text-xs font-bold tracking-widest uppercase border-b-2 border-transparent text-gray-400 hover:text-gray-700 transition-colors mr-4">Upcoming</button>
            <button id="tab-past"   onclick="filterBookings('past')"
                    class="booking-tab px-4 py-3 text-xs font-bold tracking-widest uppercase border-b-2 border-transparent text-gray-400 hover:text-gray-700 transition-colors">Past</button>
        </div>

        {{-- Content --}}
        <div id="my-bookings-content" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
            {{-- Populated by JS --}}
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <svg class="animate-spin w-8 h-8 mb-4" style="color:#964B00;" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>
                <p class="text-sm font-semibold text-gray-500">Loading your bookings…</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
            <p class="text-xs text-gray-400">Need help? <a href="{{ url('/home/contact') }}" class="font-semibold underline" style="color:#964B00;">Contact us</a></p>
            <button data-close-mybookings
                    class="px-5 py-2.5 text-xs font-bold tracking-widest uppercase text-white transition-colors"
                    style="background:#964B00;"
                    onmouseover="this.style.background='#6b3500'" onmouseout="this.style.background='#964B00'">
                Close
            </button>
        </div>
    </div>
</div>

<style>
    .booking-tab.active-tab { color:#964B00; border-bottom-color:#964B00; }
    .booking-tab:not(.active-tab) { border-bottom-color:transparent; color:#9ca3af; }

    /* Booking card */
    .booking-card { background:#fff; border:1px solid #e5e7eb; transition:box-shadow .2s; }
    .booking-card:hover { box-shadow:0 4px 20px rgba(0,0,0,.08); }

    /* Status badges */
    .badge { display:inline-flex; align-items:center; gap:4px; padding:2px 10px; font-size:.7rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; }
    .badge-green  { background:#dcfce7; color:#15803d; }
    .badge-yellow { background:#fef9c3; color:#a16207; }
    .badge-red    { background:#fee2e2; color:#b91c1c; }
    .badge-blue   { background:#dbeafe; color:#1d4ed8; }
    .badge-gray   { background:#f3f4f6; color:#4b5563; }

    /* Accordion */
    .accordion-body { max-height:0; overflow:hidden; transition:max-height .3s ease; }
    .accordion-body.open { max-height:600px; }
    .accordion-chevron { transition:transform .25s ease; }
    .accordion-chevron.open { transform:rotate(180deg); }
</style>
