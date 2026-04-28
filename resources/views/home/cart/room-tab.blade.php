<div x-show="tab === 'rooms'" x-transition>


  <div class="max-w-4xl mx-auto mb-10">
    <h2 class="text-3xl  font-[Manrope] text-black tracking-wider mb-6">
      Check Room Availability
    </h2>
    <div
      class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 font-[Manrope] bg-white p-6 border border-gray-200 shadow-sm">

      <div>
        <label class="block text-sm text-gray-600 mb-1 flex items-center gap-1">

          Check-in
        </label>
        <input id="startDate" type="date" required x-model="room_startDate"
          class="mt-1 block w-full border-gray-300 shadow-sm sm:text-sm h-11 px-3">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1 flex items-center gap-1">

          Check-out
        </label>
        <input id="endDate" type="date" required x-model="room_endDate"
          class="mt-1 block w-full border-gray-300 shadow-sm sm:text-sm h-11 px-3">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Checkin time</label>
        <input id="room_checkin_time" type="time" required x-model="room_checkin_time"
          class="relative z-10 mt-1 block w-full border-gray-300 shadow-sm sm:text-sm h-11 px-3">
      </div>


      <div>
        <label class="block text-sm text-gray-600 mb-1">Checkout time </label>
        <input id="room_checkout_time" type="time" required x-model="room_checkout_time"
          class="relative z-10 mt-1 block w-full border-gray-300 shadow-sm sm:text-sm h-11 px-3">
      </div>

      <div>
        <label class="block text-sm text-gray-600 mb-1">Adults</label>
        <select id="adults" required x-model.number="room_adults"
          class="mt-1 block w-full border-gray-300 shadow-sm sm:text-sm h-11 px-3">
          @for ($i = 1; $i <= 13; $i++)
            <option value="{{ $i }}">{{ $i }} Adult{{ $i > 1 ? 's' : '' }}</option>
          @endfor
        </select>
      </div>

      <div>
        <label class="block text-sm text-gray-600 mb-1">Children</label>
        <select id="children" required x-model.number="room_children"
          class="mt-1 block w-full border-gray-300 shadow-sm sm:text-sm h-11 px-3">
          @for ($i = 0; $i <= 13; $i++)
            <option value="{{ $i }}">{{ $i }} Child{{ $i > 1 ? 'ren' : '' }}</option>
          @endfor
        </select>
      </div>

    </div>
  </div>

  <form id="add-room-form" action="{{ url('add_to_cart') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="room_id" id="room_id_input">
    <input type="hidden" name="startDate" id="room_startDate_input">
    <input type="hidden" name="endDate" id="room_endDate_input">
    <input type="hidden" name="start_time" id="room_start_time_input">
    <input type="hidden" name="end_time" id="room_end_time_input">
    <input type="hidden" name="adults" id="room_adults_input">
    <input type="hidden" name="children" id="room_children_input">
  </form>

  <div class="mt-6 font-[Manrope] text-black">
    <div x-cloak class="mb-4">

      <div class="flex items-center justify-between">
        <div class="text-xs text-gray-500">Filter by category</div>

        <button @click="filtersOpen = true"
          class="group inline-flex items-center gap-2 px-4 py-2 border border-gray-300  text-sm font-normal 
         text-black bg-white hover:text-white hover:bg-[#964B00] focus:outline-none focus:ring-2 focus:ring-[#964B00] focus:ring-offset-2">

          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-black group-hover:text-white" viewBox="0 0 24 24">
            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="1.5"
              d="M21.25 12H8.895m-4.361 0H2.75m18.5 6.607h-5.748m-4.361 0H2.75m18.5-13.214h-3.105m-4.361 0H2.75m13.214 2.18a2.18 2.18 0 1 0 0-4.36a2.18 2.18 0 0 0 0 4.36Zm-9.25 6.607a2.18 2.18 0 1 0 0-4.36a2.18 2.18 0 0 0 0 4.36Zm6.607 6.608a2.18 2.18 0 1 0 0-4.361a2.18 2.18 0 0 0 0 4.36Z" />
          </svg>

          <span>Filters</span>
        </button>

      </div>

      <div x-show="filtersOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 z-40" aria-hidden="true">
      </div>

      <div x-show="filtersOpen" @click.outside="filtersOpen = false"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full md:translate-y-0 md:opacity-0 md:scale-95"
        x-transition:enter-end="translate-y-0 md:opacity-100 md:scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0 md:opacity-100 md:scale-100"
        x-transition:leave-end="translate-y-full md:translate-y-0 md:opacity-0 md:scale-95"
        class="fixed bottom-0 left-0 right-0 z-50 md:inset-0 md:flex md:items-center md:justify-center" role="dialog"
        aria-modal="true" aria-labelledby="filter-modal-title">
        <div class="bg-white shadow-xl w-full max-w-lg max-h-[80vh] flex flex-col">

          <div class="flex items-center justify-between p-4 border-b">
            <h2 id="filter-modal-title" class="text-lg font-medium">Filters</h2>
            <button @click="filtersOpen = false" class="text-gray-400 hover:text-red-600" aria-label="Close filters">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="p-6 space-y-6 overflow-y-auto">

            <div>
              <h3 class="text-sm font-normal text-black mb-2">Location</h3>
              <div class="flex flex-wrap gap-2">
                <button @click.prevent="roomLocation='all'"
                  :class="{ 'bg-[#964B00] text-white': roomLocation === 'all', 'bg-white text-black': roomLocation !== 'all' }"
                  class="flex-shrink-0 px-3 py-2  border border-gray-300  text-sm">All
                  Locations</button>
                <button @click.prevent="roomLocation='beach'"
                  :class="{ 'bg-[#964B00] text-white': roomLocation === 'beach', 'bg-white text-black': roomLocation !== 'beach' }"
                  class="flex-shrink-0 px-3 py-2  border border-gray-300  text-sm">Beach
                  Front</button>
                <button @click.prevent="roomLocation='nonbeach'"
                  :class="{ 'bg-[#964B00] text-white': roomLocation === 'nonbeach', 'bg-white text-black': roomLocation !== 'nonbeach' }"
                  class="flex-shrink-0 px-3 py-2  border border-gray-300  text-sm">Non Beach
                  Front</button>
              </div>
            </div>

            <div>
              <h3 class="text-sm font-normal text-black mb-2">Promo</h3>
              <div class="flex flex-wrap gap-2">
                <button @click.prevent="roomPromo='all'"
                  :class="{ 'bg-[#964B00] text-white': roomPromo === 'all', 'bg-white text-black': roomPromo !== 'all' }"
                  class="flex-shrink-0 px-3 py-2  border border-gray-300  text-sm">All
                  Promos</button>
                <button @click.prevent="roomPromo='christmas'"
                  :class="{ 'bg-[#964B00] text-white': roomPromo === 'christmas', 'bg-white text-black': roomPromo !== 'christmas' }"
                  class="flex-shrink-0 px-3 py-2  border border-gray-300  text-sm">Christmas
                  Sale</button>
                <button @click.prevent="roomPromo='rainy'"
                  :class="{ 'bg-[#964B00] text-white': roomPromo === 'rainy', 'bg-white text-black': roomPromo !== 'rainy' }"
                  class="flex-shrink-0 px-3 py-2  border border-gray-300  text-sm">Rainy Day
                  Sale</button>
                <button @click.prevent="roomPromo='seniorpwd'"
                  :class="{ 'bg-[#964B00] text-white': roomPromo === 'seniorpwd', 'bg-white text-black': roomPromo !== 'seniorpwd' }"
                  class="flex-shrink-0 px-3 py-2  border border-gray-300  text-sm">Senior &
                  PWD</button>
              </div>
            </div>

            <div>
              <h3 class="text-sm font-normal text-black mb-2">Room Type</h3>
              <div class="flex flex-wrap gap-2">
                <button @click.prevent="roomFilter='all'"
                  :class="{ 'bg-[#964B00] text-white': roomFilter === 'all', 'bg-white text-black': roomFilter !== 'all' }"
                  class="flex-shrink-0 px-3 py-2  border border-gray-300  text-sm">All</button>
                <button @click.prevent="roomFilter='regular'"
                  :class="{ 'bg-[#964B00] text-white': roomFilter === 'regular', 'bg-white text-black': roomFilter !== 'regular' }"
                  class="flex-shrink-0 px-3 py-2  border border-gray-300  text-sm">Regular</button>
                <button @click.prevent="roomFilter='premium'"
                  :class="{ 'bg-[#964B00] text-white': roomFilter === 'premium', 'bg-white text-black': roomFilter !== 'premium' }"
                  class="flex-shrink-0 px-3 py-2  border border-gray-300  text-sm">Premium</button>
                <button @click.prevent="roomFilter='deluxe'"
                  :class="{ 'bg-[#964B00] text-white': roomFilter === 'deluxe', 'bg-white text-black': roomFilter !== 'deluxe' }"
                  class="flex-shrink-0 px-3 py-2  border border-gray-300  text-sm">Deluxe</button>
              </div>
            </div>

          </div>

          <div class="flex items-center justify-between p-4 border-t bg-gray-50 ">
            <button @click="roomLocation='all'; roomPromo='all'; roomFilter='all'"
              class="text-sm font-normal text-black hover:text-[#964B00]">
              Clear all
            </button>
            <button @click="filtersOpen = false"
              class="px-5 py-2 bg-black text-white text-sm  shadow-sm hover:bg-[#964B00] focus:outline-none focus:ring-2 focus:ring-[#964B00] focus:ring-offset-2">
              Show Results
            </button>
          </div>

        </div>
      </div>
    </div>


    <div
      class="flex overflow-x-auto space-x-4 snap-x snap-mandatory px-4 sm:block sm:px-0 md:space-y-6 lg:overflow-visible lg:space-x-0"
      style="-webkit-overflow-scrolling: touch; touch-action: pan-x;">

      @foreach ($rooms as $room)
        @php
          $type = strtolower($room->room_type ?? '');
          $beachFrontNames = [
            'Beach Front - Beach Front Kubo - Terrace Cabana',
            'Beach Front Kubo Units - Room B or C',
            'Beach Front Kubo - Twin Cabana',
            'Beach Front Kubo - Family Cabana',
            'Vacation House',
            'Britannia Room w/ Sand Kubo',
            'Beach Front-Penthouse Suite w/ Sand Kubo',
            'Beach Front - VIP STUDIO w/ Open Cabana',
          ];
          $nonBeachFrontNames = [
            'Luxury Unit - King Suite w/ Sand Kubo',
            'Luxury Unit - Queen Suite w/ Sand Kubo',
            'Luxury Unit - Mini Queen w/ Sand Kubo',
            'Lovers Room w/ Sand Kubo',
            'Sunset Rooftop w/ Sand Kubo',
          ];

          $location = in_array($room->room_name, $beachFrontNames) ? 'beach' : (in_array($room->room_name, $nonBeachFrontNames) ? 'nonbeach' : 'other');
        @endphp

        <div
          x-show="(roomFilter === 'all' || roomFilter === '{{ $type }}') && (roomLocation === 'all' || roomLocation === '{{ $location }}')"
          x-cloak data-room-id="{{ $room->id }}"
          class="room-card snap-start min-w-full sm:min-w-0 sm:w-full bg-white border border-gray-200 shadow-sm overflow-hidden flex flex-col md:flex-row h-full">

          <div class="relative room-image-container overflow-hidden h-56 md:h-full md:w-2/5 flex-shrink-0">
            <img draggable="false" src="{{ asset('room/' . ($room->image ?? 'placeholder.jpg')) }}"
              onerror="this.src='{{ $placeholderImage }}'"
              style="-webkit-user-drag: none; -webkit-user-select: none; user-select: none;"
              class="w-full h-full object-cover transition duration-500 hover:scale-105">
            <div class="absolute bottom-4 left-4">
              <p class="text-lg font-bold text-white">PHP {{ number_format($room->price ?? 0, 2) }}</p>
              <span class="text-xs text-gray-200 tracking-wide">PER NIGHT</span>
            </div>
          </div>

          <div class="p-4 flex flex-col flex-grow md:w-3/5">
            <h2 class="text-lg  text-black mb-2">{{ $room->room_name }}</h2>

            <div
              class="flex flex-wrap gap-x-4 gap-y-3 text-gray-600 text-xs  mb-4 pt-3 border-t border-gray-200">

              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 20v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m8-10a4 4 0 100-8 4 4 0 000 8z" />
                </svg>
                <span>{{ $room->accommodates }} Guests</span>
              </div>

              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 18H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2zM9 10a1 1 0 100 2 1 1 0 000-2z" />
                </svg>
                <span>{{ $room->beds }}</span>
              </div>

              @include('home.cart.room-tab.amenity-svgs')

              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Check-in: {{ \Carbon\Carbon::parse($room->check_in)->format('h:i A') }}</span>
              </div>

              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Check-out: {{ \Carbon\Carbon::parse($room->check_out)->format('h:i A') }}</span>
              </div>
            </div>

            <div class="mt-auto flex justify-between items-center">
              <a href="{{ url('room_details', $room->id) }}" class="text-[#964B00] underline text-xs hover:text-black">
                View Details
              </a>
              <button type="button"
                onclick="addRoomToCart('{{ $room->id }}')"
                class="bg-[#964B00] px-6 py-2.5 text-xs text-white hover:bg-black transition flex items-center justify-center tracking-wide">
                BOOK NOW
              </button>
            </div>
          </div>
        </div>
      @endforeach

    </div>

  </div>
</div>