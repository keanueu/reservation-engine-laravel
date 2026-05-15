@extends('home.layouts.app')
@section('content')

  <div class="relative w-full h-[35vh] md:h-[45vh]">
    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
      alt="Luxury Beach Resort" class="absolute inset-0 object-cover w-full h-full">
    <div class="relative z-10 flex items-end justify-center w-full h-full bg-black bg-opacity-50 px-4 pb-12 md:pb-16">
      <h1
        class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl  text-white text-center font-[Inter]">
        Our Exclusive Stays
      </h1>
    </div>
  </div>

  <section class="pt-12 pb-12 font-[Inter] bg-white"> {{-- Adjusted padding to match room blade --}}
    <div class="max-w-7xl mx-auto px-4" id="boat-details-section">

      <div class="pb-3"> {{-- Adjusted structure for heading and back link --}}
        <a href="{{ url('/home/roomcart') }}"
          class="flex items-center text-sm text-black hover:text-black transition mb-1 font-medium"> {{-- Color change to match room blade --}}
          <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
          </svg>
          Back to Cart
        </a>
        <h1 class="text-2xl md:text-3xl  text-black"> {{-- Size and styling change to match room blade --}}
          {{ $boat->name ?? 'Boat Details' }}
        </h1>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <div class="lg:col-span-2 flex flex-col space-y-8"> {{-- Adjusted spacing to match room blade --}}

          {{-- Single Image Display (Replaced the complex carousel structure with a simple image container) --}}
          <div class="h-[400px] overflow-hidden relative shadow-xl"> {{-- Matched height from room blade --}}
            <img src="{{ asset('boats/' . ($boat->image ?? 'placeholder.jpg')) }}" alt="{{ $boat->name }}"
              onerror="this.onerror=null;this.src='https://placehold.co/800x600/f3f4f6/1f2937?text=Boat+Image'"
              class="w-full h-full object-cover">
          </div>

          {{-- Overview/Description Section --}}
          <div>
            <h2 class="text-xl  text-black pb-2 flex items-center gap-2 border-b">
              Overview
            </h2>
            <p class="mt-2 text-black text-sm  leading-relaxed"> {{-- Matched text style from room blade --}}
              {{ $boat->description }}
            </p>
          </div>
          {{-- End of Overview/Description Section --}}

        </div>

        <div class="lg:col-span-1 flex flex-col space-y-8"> {{-- Added flex-col space-y to match room blade --}}

          {{-- Properties Section (Matching the look of the room blade's properties section) --}}
          <div class="p-5 bg-white shadow-sm border border-gray-300">
            <h3 class="text-xl  text-black mb-4 flex items-center gap-2">
              Boat Specifications
            </h3>

            <ul class="grid grid-cols-1 gap-y-3 text-sm text-black">
              {{-- Price --}}
              <li class="flex items-center justify-between">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-[18px] mr-1.5 font-medium text-black" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V4m0 12v-4m-6 0h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                </svg>
                <span class=" text-black flex-1">Rate:</span>
                <span class="">₱{{ number_format($boat->price ?? 0, 2) }}/hr</span>
              </li>

              {{-- Capacity --}}
              <li class="flex items-center justify-between">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-[18px] mr-1.5 font-medium text-black" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 0 0-3-3.87" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                <span class=" text-black flex-1">Max Capacity:</span>
                <span class="">{{ $boat->capacity }} Guests</span>
              </li>

              {{-- Type --}}
              <li class="flex items-center justify-between">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-[18px] mr-1.5 font-medium text-black" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 15.5l-2-2m0 0-2 2m2-2V4m7 11.5l-2-2m0 0-2 2m2-2V4m-5 8V4"/>
                  <path d="M11 21h2a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2zM3 12h18"/>
                </svg>
                <span class=" text-black flex-1">Type:</span>
                <span class="">{{ $boat->type ?? 'N/A' }}</span>
              </li>

              {{-- Status (Using a generic boat/calendar icon since the room blade uses check-in/out icons) --}}
              <li class="flex items-center justify-between">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-[18px] mr-1.5 font-medium text-black" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.25M19.5 14.25A2.25 2.25 0 0022 12V6a2.25 2.25 0 00-2.25-2.25H4.25M19.5 14.25v2.25M3 14.25v2.25M3 14.25A2.25 2.25 0 01.5 12V6a2.25 2.25 0 012.25-2.25H4.25M4.25 12V6M4.25 12h15.5M4.25 6h15.5M4.25 18H10a2.25 2.25 0 002.25 2.25h0a2.25 2.25 0 002.25-2.25H19.5m-15.25-3.75v3.75m15.5-3.75v3.75M12 18h12" />
                </svg>
                <span class=" text-black flex-1">Overall Status:</span>
                <span class="">{{ $boat->status }}</span>
              </li>

              {{-- Model/Make (Using a document/specs icon) --}}
              <li class="flex items-center justify-between">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-[18px] mr-1.5 font-medium text-black" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12h6m-6 4h6m-4-8h2M12 3v18M18 6h3M18 18h3M3 6h3M3 18h3M6 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2h-1"/>
                </svg>
                <span class=" text-black flex-1">Model/Make:</span>
                <span class="">{{ $boat->specs ?? 'N/A' }}</span>
              </li>
            </ul>
          </div>
          {{-- End of Properties Section --}}

        </div>

      </div>
    </div>
  </section>

@endsection
