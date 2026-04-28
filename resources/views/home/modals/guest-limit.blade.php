   <!-- Guest Limit Modal -->
        <div id="guestLimitModal"
            class="fixed inset-0 bg-black/50 hidden justify-center items-center z-50 transition-opacity duration-300 ease-out">
            <!-- Modal Content -->
            <div id="guestLimitContent"
                class="bg-white shadow-2xl p-8 w-[28rem] max-w-[90%] text-center transform scale-95 opacity-0 transition-all duration-300 ease-out">
                <h2 class="text-xl font-medium text-black mb-3">Maximum Room Capacity Reached</h2>
                <p class="text-black  mb-6 leading-relaxed">
                    Kindly note that only one of our rooms can accommodate up to <span class="font-medium text-black">13
                        guests.</span>
                    You may adjust <br> your current selection or proceed to your
                    <a href="{{ url('/home/roomcart') }}"><span
                            class="text-yellow-500 font-normal border-b border-yellow-500 pb-1">Cart</span></a> to <br>
                    reserve
                    multiple
                    rooms.
                </p>
                <button id="closeModalBtn"
                    class="px-5 py-2.5 bg-yellow-500 text-white  hover:bg-yellow-600 uppercase transition">
                    Close
                </button>
            </div>
        </div>