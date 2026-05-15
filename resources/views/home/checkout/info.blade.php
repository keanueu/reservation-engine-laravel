<div class="bg-white p-8 border border-gray-300 shadow-md space-y-6">
    <h2 class="text-xl font-medium text-black border-b border-gray-300 pb-3">
        Important Information
    </h2>

    <div>
        <p class="text-sm font-medium text-black mb-2">Reservation & Cancellation Policy</p>
        <button type="button"
            class="px-4 py-2 text-sm  text-white bg-[#964B00] border border-[#964B00] hover:bg-[#7a3c00] transition duration-150  shadow-sm"
            aria-haspopup="dialog" aria-expanded="false" data-overlay="#policy-modal">
            Review Full Policy & Terms
        </button>
    </div>

    @foreach($cartRooms as $room)
        <div class="bg-white p-4 border border-gray-300 ">
            <p class=" text-sm text-black mb-2">{{ $room->room_name }} Schedule</p>
            <div class="flex justify-between  text-sm text-black">
                <div>
                    <p class=" text-sm text-black">Check-in Time</p>
                    <span class="text-green-600 text-sm ">
                        after {{ \Carbon\Carbon::parse($room->check_in)->format('h:i A') }}
                    </span>
                </div>
                <div class="text-right">
                    <p class=" text-sm text-black">Check-out Time</p>
                    <span class="text-red-600 text-sm ">
                        before {{ \Carbon\Carbon::parse($room->check_out)->format('h:i A') }}
                    </span>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div id="policy-modal"
    class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden z-50 transition-opacity font-[Inter]"
    role="dialog" aria-modal="true" tabindex="-1">
    <div class="bg-white w-full max-w-lg mx-4 p-6 relative shadow-2xl transform transition-transform duration-300">
        <button type="button" class="absolute top-4 right-4 text-white hover:text-black text-3xl  p-1"
            data-overlay="#policy-modal" title="Close">
            &times;
        </button>

        <h3 class="text-2xl font-medium mb-4 border-b pb-2 text-black font-[Inter]">Hotel Policy & Terms</h3>
        <div class="text-sm text-black  max-h-96 overflow-y-auto pr-2 custom-scrollbar">
            {{ $room->terms ?? 'No specific terms found for this selection. Please contact the hotel directly for their reservation and cancellation policy.' }}
        </div>

        <div class="mt-6 flex justify-end">
            <button type="button" class="px-5 py-2 bg-black text-white text-sm  hover:orange-700 transition "
                data-overlay="#policy-modal">I Understand</button>
        </div>
    </div>
</div>
