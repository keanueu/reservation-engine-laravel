@extends('auth.layouts.app')
@section('content')
    <div id="app" class="min-h-screen flex flex-col lg:flex-row">
        <div class="w-full h-1/3 min-h-[200px] lg:hidden bg-[#964B00] text-white flex flex-col items-center justify-center p-4 sm:p-12 shadow-xl">
            <div class="flex flex-col items-center justify-center">
                <a href="{{ url('/') }}" id="brand-name-mobile" class="flex items-center justify-center text-white flex-shrink-0">
                    <div class="w-16 h-16 sm:w-24 sm:h-24 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('LOGO-FINAL.png') }}" alt="Cabanas Logo" class="h-full w-full object-contain filter" />
                    </div>
                </a>
                <h1 class="text-2xl font-bold mt-3">Cabanas Beach Resort</h1>
                <p class="text-xs  text-white/80">The best place to stay in Tambobong Beach!</p>
            </div>
        </div>
        <!-- LEFT BRANDING COLUMN -->
        <div class="hidden lg:flex lg:w-1/2 p-10 bg-[#964B00] text-white flex-col justify-between relative shadow-2xl">
            <div class="flex items-center space-x-2">
                <!-- Brand Logo (use project logo image) -->
                <a href="{{ url('/') }}" id="brand-name" class="flex items-center justify-center text-white flex-shrink-0 py-2 xl:py-3">
                    <div class="w-12 h-12 sm:w-12 sm:h-12 md:w-14 md:h-14 lg:w-16 lg:h-16 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('LOGO-FINAL.png') }}" alt="Cabanas Logo" class="h-full w-full object-contain" />
                    </div>
                </a>
            </div>
            <div class="flex flex-col items-center justify-center space-y-12 my-auto">
                <div class="relative">
                    <div class="h-72 w-72 bg-white/10  flex items-center justify-center">
                        <img src="{{ asset('LOGO-FINAL.png') }}" alt="Brand Logo" class="h-56 w-56 object-contain opacity-100">
                    </div>
                </div>
               <blockquote class="text-center  text-md leading-tight max-w-sm mx-auto">
                    The best place to stay in Tambobong Beach! Nature's gift to city dwellers!
                </blockquote>

              <p class="text-center  text-md leading-tight font-serif mt-0">
                    Cabanas Beach Resort and  Hotel
                </p>


            </div>
            <p class="text-sm text-white/70">&copy; 2025 Cabanas Beach Resort. All Rights Reserved.</p>
        </div>
        <!-- RIGHT FORM COLUMN -->
        <div class="w-full lg:w-1/2 bg-dark-bg text-white flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-lg">
                <div class="form-fade-in bg-white p-8 shadow-2xl">
                    <h2 class="text-2xl sm:text-3xl sm:text-3xl text-black font-semibold mb-2">Reset Password</h2>
                    <p class="text-gray-700 text-sm mb-6">Enter your email and new password below to reset your account password.</p>
                    @if ($errors->any())
                        <div class="mb-4 text-sm text-red-500">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                        <div class="mt-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="Your email address" class="block w-full px-4 py-3 bg-dark-input border text-sm border-gray-700 text-gray-700 placeholder-gray-500 focus:ring-[#964B00] focus:border-[#964B00] focus:outline-none transition duration-150" />
                        </div>
                        <div class="mt-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="New password" class="block w-full px-4 py-3 bg-dark-input border border-gray-700 text-gray-700 text-sm placeholder-gray-500 focus:ring-[#964B00] focus:border-[#964B00] focus:outline-none transition duration-150" />
                        </div>
                        <div class="mt-4">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm new password" class="block w-full px-4 py-3 bg-dark-input text-sm border border-gray-700 text-gray-700 placeholder-gray-500 focus:ring-[#964B00] focus:border-[#964B00] focus:outline-none transition duration-150" />
                        </div>
                        <div class="mt-8">
                            <button type="submit" class="w-full bg-[#964B00] hover:bg-[#7a3c00] text-sm text-white font-semibold py-3 transition duration-300 shadow-lg shadow-orange-600/40">Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection