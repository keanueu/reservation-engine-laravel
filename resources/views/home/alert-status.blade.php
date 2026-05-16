@extends('home.layouts.app')
@section('content')

<div class="relative w-full h-[35vh] md:h-[45vh]">
    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
        alt="Luxury Beach Resort" class="absolute inset-0 object-cover w-full h-full">
    <div class="relative z-10 flex items-end justify-center w-full h-full bg-black bg-opacity-50 px-4 pb-12 md:pb-16">
        <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl text-white text-center font-medium ">
            Alerts and advisories
        </h1>
    </div>
</div>

    <div class="min-h-screen bg-white py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            @include('home.partials.alerts-drawer-content')
        </div>
    </div>
@endsection


