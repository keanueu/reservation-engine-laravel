@extends('home.layouts.app')
@section('content')

  <div class="relative w-full h-[35vh] md:h-[45vh]">
    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
      alt="Luxury Beach Resort" class="absolute inset-0 object-cover w-full h-full">
    <div class="relative z-10 flex items-end justify-center w-full h-full bg-black bg-opacity-50 px-4 pb-12 md:pb-16">
      <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl text-white text-center font-[Inter] font-medium ">
       Our resort amenities
      </h1>
    </div>
  </div>

  <section data-animate
    class="bg-white py-16 font-[Inter]  opacity-0 will-change-transform will-change-opacity">
    <div class="max-w-7xl mx-auto px-6">

      @include('home.amenity.service')

    </div>
  </section>

  <section data-animate
    class="bg-white py-2 font-[Inter]  opacity-0 will-change-transform will-change-opacity">
    <div class="max-w-7xl mx-auto px-6">

      @include('home.amenity.activity')
    </div>
  </section>

  <section data-animate class="bg-white py-8 font-[Inter] opacity-0 will-change-transform will-change-opacity">
    <div class="max-w-7xl mx-auto px-6">

      @include('home.amenity.desk')

    </div>
  </section>

  <section data-animate class="bg-gray-50 py-8 font-[Inter] opacity-0 will-change-transform will-change-opacity">
    <div class="max-w-7xl mx-auto px-6">

      @include('home.amenity.lgu')

    </div>
  </section>

  <section data-animate class="bg-white py-8 font-[Inter] opacity-0 will-change-transform will-change-opacity mb-12">
    <div class="max-w-7xl mx-auto px-6">

      @include('home.amenity.cr')

    </div>
  </section>


@endsection
