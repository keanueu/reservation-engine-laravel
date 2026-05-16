@extends('home.layouts.app')
@section('content')

  <div class="relative w-full h-[35vh] md:h-[45vh]">
    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
      alt="Luxury Beach Resort" class="absolute inset-0 object-cover w-full h-full">
    <div class="relative z-10 flex items-end justify-center w-full h-full bg-black bg-opacity-50 px-4 pb-12 md:pb-16">
      <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl text-white text-center font-medium ">
        Our exclusive boats
      </h1>
    </div>
  </div>

  <div class="bg-white py-8 ">
    <div class="text-center mb-12">
      <p class="text-sm font-medium mb-4 section-label">Our fleet</p>
      <h2 class="text-4xl md:text-5xl font-medium leading-relaxed] text-black">
        Exclusive boats
      </h2>
      <p class="text-base text-black leading-relaxed mt-4 max-w-2xl mx-auto">
        Explore our fleet of boats for island hopping, fishing trips, and sunset cruises.
      </p>
    </div>

    <div class="max-w-7xl mx-auto px-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
        @foreach ($boats as $boat)
          <div
            class="group relative block bg-white border border-gray-100 shadow-lg transition duration-300 hover:shadow-xl overflow-hidden flex flex-col h-full">

            <div class="relative overflow-hidden h-56">
              <img src="{{ asset('boats/' . ($boat->image ?? 'placeholder.jpg')) }}" alt="{{ $boat->name }}"
                class="w-full h-full object-cover transition duration-500 group-hover:scale-105" />
              <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/70 to-transparent"></div>
              <div class="absolute bottom-4 left-4 z-10">
                <p class="text-lg font-medium text-white">
                  PHP {{ number_format($boat->price ?? 0, 2) }}
                </p>
                <span class="text-sm text-white">Per hour</span>
              </div>
            </div>

            <div class="p-4 flex flex-col flex-grow">
              <h3 class="text-lg font-medium text-black mb-2">
                {{ $boat->name }}
              </h3>

              <p class="text-sm text-black leading-relaxed mb-4 flex-grow">
                A comfortable boat perfect for island hopping and scenic cruises.
              </p>

              <div class="flex items-center space-x-4 text-sm text-black mb-5 pt-4 border-t border-gray-100">
                <div class="flex items-center" title="Capacity">
                  <span class="material-symbols-outlined text-base mr-1.5 text-black">group</span>
                  <span class="font-medium text-sm text-black">{{ $boat->capacity }}</span>
                </div>
              </div>

              <div class="mt-auto">
                <a href="{{ url('boat_details', $boat->id) }}"
                  class="flex justify-center items-center w-full bg-[#964B00] p-3 text-sm font-medium text-white border border-[#964B00] transition duration-300 hover:bg-black hover:border-black">
                  View details
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

@endsection


