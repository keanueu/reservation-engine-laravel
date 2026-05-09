@extends('home.layouts.app')
@section('content')

  <section>
    <div class="relative w-full h-[35vh] md:h-[45vh]">
      <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
        alt="Luxury Beach Resort" class="absolute inset-0 object-cover w-full h-full">
      <div class="relative z-10 flex items-end justify-center w-full h-full bg-black bg-opacity-50 px-4 pb-12 md:pb-16">
        <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl text-white text-center font-[Inter] font-bold tracking-tight">
          Get in touch
        </h1>
      </div>
    </div>
  </section>

  <section data-animate class="py-8 bg-gray-50 mt-6 font-[Inter] opacity-0 will-change-transform will-change-opacity">
    <div class="max-w-7xl mx-auto px-6">
      @include('home.contact.form-map')
    </div>
  </section>

  <section data-animate class="py-8 bg-gray-50 mt-6 font-[Inter] opacity-0 will-change-transform will-change-opacity">
    <div class="max-w-7xl mx-auto px-6">
      @include('home.contact.map')
    </div>
  </section>
@endsection
