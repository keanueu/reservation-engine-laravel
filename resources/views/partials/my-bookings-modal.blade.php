<div id="my-bookings-modal"
     class="fixed inset-0 hidden items-center justify-end z-[9999] font-['Raleway']"
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
                    class="booking-tab active-tab px-4 py-3 text-xs font-bold border-b-2 transition-colors mr-4">All</button>
            <button id="tab-upcoming" onclick="filterBookings('upcoming')"
                    class="booking-tab px-4 py-3 text-xs font-bold border-b-2 border-transparent text-gray-400 hover:text-gray-700 transition-colors mr-4">Upcoming</button>
            <button id="tab-past"   onclick="filterBookings('past')"
                    class="booking-tab px-4 py-3 text-xs font-bold border-b-2 border-transparent text-gray-400 hover:text-gray-700 transition-colors">Past</button>
        </div>

        {{-- Content --}}
        <div id="my-bookings-content" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
            {{-- Populated by JS --}}
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <svg class="animate-spin w-8 h-8 mb-4 text-[#63360D]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>
                <p class="text-sm font-semibold text-gray-500">Loading your bookings…</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
            <p class="text-xs text-gray-400">Need help? <a href="{{ url('/home/contact') }}" class="font-bold underline text-[#63360D]">Contact us</a></p>
            <button data-close-mybookings
                    class="px-6 py-2.5 text-[10px] font-bold text-white transition-all rounded-lg uppercase tracking-widest bg-[#63360D] hover:bg-[#261405] shadow-lg">
                Close
            </button>
        </div>
    </div>
</div>

<style>
    .booking-tab.active-tab { color:#63360D; border-bottom-color:#63360D; }
    .booking-tab:not(.active-tab) { border-bottom-color:transparent; color:#9ca3af; }

    /* Booking card */
    .booking-card { background:#fff; border:1px solid #e5e7eb; transition:box-shadow .2s; }
    .booking-card:hover { box-shadow:0 4px 20px rgba(0,0,0,.08); }

    /* Status badges - Modern Soft Style */
    .badge { display:inline-flex; align-items:center; gap:6px; padding:4px 12px; font-size:.65rem; font-weight:800; letter-spacing:.05em; text-transform:uppercase; border-radius:100px; }
    .badge-green  { background:#ecfdf5; color:#059669; border: 1px solid #d1fae5; }
    .badge-yellow { background:#fffbeb; color:#d97706; border: 1px solid #fef3c7; }
    .badge-red    { background:#fef2f2; color:#dc2626; border: 1px solid #fee2e2; }
    .badge-blue   { background:#eff6ff; color:#2563eb; border: 1px solid #dbeafe; }
    .badge-gray   { background:#f9fafb; color:#4b5563; border: 1px solid #f3f4f6; }

    /* Accordion */
    .accordion-body { max-height:0; overflow:hidden; transition:max-height .3s ease; }
    .accordion-body.open { max-height:600px; }
    .accordion-chevron { transition:transform .25s ease; }
    .accordion-chevron.open { transform:rotate(180deg); }
</style>

