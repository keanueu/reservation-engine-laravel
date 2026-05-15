<div x-show="tab === 'boats'" x-transition>

  <div class="max-w-7xl font-[Inter] mx-auto mb-10">
    <h2 class="text-3xl  text-black mb-6">
      Schedule Your Boat Trip
    </h2>
    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-white p-6 border border-gray-200 shadow-sm">
      <div>
        <label for="booking_date" class="block text-sm font-medium text-black mb-1">Date</label>
        <input id="booking_date" type="date" name="booking_date" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
          x-model="boat_booking_date" class="mt-1 block w-full border-gray-300 shadow-sm sm:text-sm h-11 px-3" required>
      </div>
      <div>
        <label for="guests" class="block text-sm font-medium text-black mb-1">Guests</label>
        <input type="number" name="guests" id="guests" min="1" max="7" x-model.number="boat_guests"
          class="mt-1 block w-full border-gray-300 shadow-sm sm:text-sm h-11 px-3" required>
      </div>
      <div>
        <label for="start_time" class="block text-sm font-medium text-black mb-1">Start Time</label>
        <input id="start_time" type="time" name="start_time" x-model="boat_start_time"
          class="mt-1 block w-full border-gray-300 shadow-sm sm:text-sm h-11 px-3" required>
      </div>
      <div>
        <label for="end_time" class="block text-sm font-medium text-black mb-1">End Time</label>
        <input id="end_time" type="time" name="end_time" x-model="boat_end_time"
          class="mt-1 block w-full border-gray-300 shadow-sm sm:text-sm h-11 px-3" required>
      </div>
    </div>
  </div>

  <form id="add-boat-form" action="{{ url('add_boat_booking') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="boat_id" id="boat_id_input">
    <input type="hidden" name="booking_date" id="boat_date_input">
    <input type="hidden" name="guests" id="boat_guests_input">
    <input type="hidden" name="start_time" id="boat_start_time_input">
    <input type="hidden" name="end_time" id="boat_end_time_input">
  </form>

  <div class="mt-6 space-y-8 font-[Inter] text-black">
    @foreach ($boats as $boat)
      <div class="group bg-white border border-gray-200 shadow-sm overflow-hidden flex flex-col md:flex-row h-full">
        <div class="relative overflow-hidden h-56 md:h-auto md:w-2/5">
          <img src="{{ asset('boats/' . ($boat->image ?? 'placeholder.jpg')) }}" alt="{{ $boat->name }}"
            onerror="this.onerror=null;this.src='{{ $placeholderImage }}';"
            class="w-full h-full object-cover transition duration-500 group-hover:scale-105" />
          <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-black/70 to-transparent">
          </div>

          <div class="absolute bottom-4 left-4">
            <p class="text-xl font-medium text-white">

              PHP <span class="text-white">{{ number_format($boat->price ?? 0, 2) }}</span>
            </p>
            <span class="text-sm  text-white ">PER TRIP</span>
          </div>
        </div>

        <div class="p-4 flex flex-col flex-grow md:w-3/5">
          <h2 class="text-xl  text-black mb-2">{{ $boat->name }}</h2>
          <p class="text-sm text-black  mb-3">
            This boat has a maximum capacity of {{ $boat->capacity }} persons.
          </p>

          <div class="flex flex-wrap gap-x-4 gap-y-3 text-black text-sm  mb-4 pt-3 border-t border-gray-200">

            <div class="flex items-center gap-2" title="Capacity">
              <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17 20v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m8-10a4 4 0 100-8 4 4 0 000 8z" />
              </svg>
              <span class=" text-sm text-black">{{ $boat->capacity }} Persons</span>
            </div>

            <div class="flex items-center gap-2" title="Available Start Time">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-black" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class=" text-sm text-black">Starts:
                {{ $boat->start_time ? \Carbon\Carbon::parse($boat->start_time)->format('h:i A') : 'N/A' }}</span>
            </div>

            <div class="flex items-center gap-2" title="Available End Time">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-black" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class=" text-sm text-black">Ends:
                {{ $boat->end_time ? \Carbon\Carbon::parse($boat->end_time)->format('h:i A') : 'N/A' }}</span>
            </div>
          </div>
          <div class="mt-auto flex items-center justify-between">
            <a href="{{ url('boat_details', $boat->id) }}"
              class="text-[#964B00] underline text-sm hover:text-yellow-600">
              View Details
            </a>

            <button data-boat-id="{{ $boat->id }}"
              @click.prevent="submitBoatBooking({{ $boat->id }}, {{ $boat->capacity }})"
              class="book-now-btn bg-[#964B00] px-6 py-2.5 text-sm text-white hover:bg-black  transition flex items-center justify-center ">
              <span class="btn-text">BOOK NOW</span>
              <svg class="btn-loader hidden animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
            </button>
          </div>
        </div>

      </div>
    @endforeach
  </div>
</div>
