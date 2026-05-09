{{-- Boats listing --}}
@php
  $boats = $boats ?? collect();
@endphp

<div class="bg-white py-16 font-[Inter] mt-16">
  <!-- Featured Boats Header -->
  <div class="max-w-7xl mx-auto px-6 mb-12">
    <h1 class="text-3xl sm:text-4xl md:text-5xl text-center  text-black tracking-wider">
      Featured Boats
    </h1>
    <div class="w-16 h-0.5 bg-gray-900 mx-auto mt-4"></div>
  </div>

  <!-- Boat Card Grid -->
  <div class="max-w-7xl mx-auto px-6">
    <div class="flex flex-wrap justify-center gap-10 lg:gap-16">
      @foreach ($boats as $boat)
        <div
          class="group relative block bg-white border border-gray-100 shadow-lg transition duration-300 hover:shadow-xl overflow-hidden flex flex-col h-full w-full sm:w-[45%] lg:w-[22%]">

          <!-- Image Area -->
          <div class="relative overflow-hidden h-48">
            <img src="{{ asset('boats/' . ($boat->image ?? 'placeholder.jpg')) }}" alt="{{ $boat->name }}"
              class="w-full h-full object-cover transition duration-500 group-hover:scale-105" />
          </div>

          <!-- Content Area -->
          <div class="p-4 flex flex-col flex-grow">
            <div class="mb-3">
              <h3 class="text-md  text-black tracking-wide uppercase">
                {{ $boat->name }}
              </h3>
              <p class="mt-1 text-md  text-black">
                ₱{{ number_format($boat->price ?? 0, 2) }}
                <span class="text-sm text-black tracking-tight">/ HOUR</span>
              </p>
            </div>

            <div class="mt-auto">
              <div class="flex items-center text-xs  text-black mb-4 border-t border-gray-100 pt-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-black" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 13h18l-2 8H5l-2-8zm9-9a4 4 0 110 8 4 4 0 010-8z" />
                </svg>
                <span class="tracking-widest">CAPACITY: {{ $boat->capacity }}</span>
              </div>

              <a href="{{ url('boat_details', $boat->id) }}"
                class="flex justify-center items-center w-full bg-white p-3 text-sm font-normal text-black tracking-wider border border-gray-900 transition duration-300 hover:bg-gray-900 hover:text-white hover:border-gray-900">
                View Details
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

</div>
