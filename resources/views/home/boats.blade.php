@extends('home.layouts.app')
@section('content')


  <div class="relative w-full h-[35vh] md:h-[45vh]">
    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
      alt="Luxury Beach Resort" class="absolute inset-0 object-cover w-full h-full">
    <div class="relative z-10 flex items-end justify-center w-full h-full bg-black bg-opacity-50 px-4 pb-12 md:pb-16">
      <h1
        class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl  text-white text-center font-[Manrope] tracking-wide">
        Our Exclusive Stays
      </h1>
    </div>
  </div>

  <!-- Boat Card Grid -->
  <div class="max-w-6xl mx-auto px-6 mt-16 font-[Manrope]">
    <div class="flex flex-wrap justify-center gap-10 lg:gap-16 mb-12">
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
                PHP {{ number_format($boat->price ?? 0, 2) }}
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

@endsection