@extends('home.layouts.app')
@section('content')
    <div class="relative w-full h-[35vh] md:h-[45vh]">
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80"
            alt="Luxury Beach Resort" class="absolute inset-0 object-cover w-full h-full">
        <div class="relative z-10 flex items-end justify-center w-full h-full bg-black bg-opacity-50 px-4 pb-12 md:pb-16">
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl  text-white text-center font-[Inter]">
                Confirm Your Booking
            </h1>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-6 py-10">
        <div class="lg:hidden space-y-2 mb-8 font-[Inter]"> <a href="{{ url('/home/roomcart') }}"
                class="flex items-center text-sm text-black hover:text-[#964B00] transition">
                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to Cart
            </a>
            
            <h1 class="text-2xl sm:text-3xl font-medium text-black font-[Inter]">
                Confirm & Book
            </h1>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-1 lg:order-2">
                <div class="bg-white p-6 border border-gray-300 shadow-md sticky top-28">
                    @include('home.partials.checkout-price-details', ['cartRooms' => $cartRooms, 'total' => $total, 'deposit' => $deposit ?? null])
                </div>
            </div>
            <div class="lg:col-span-2 lg:order-1 space-y-10 font-[Inter]">
                <div class="hidden lg:block space-y-2">
                    <a href="{{ url('/home/roomcart') }}"
                        class="flex items-center text-sm text-black hover:text-[#964B00] transition">
                        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Back to Cart
                    </a>
                    
                    <h1 class="text-2xl sm:text-3xl font-medium text-black">
                        Confirm & Book
                    </h1>
                </div>
                @include('home.checkout.form')
                @include('home.checkout.info')
            </div>
        </div>
    </div>
@endsection
