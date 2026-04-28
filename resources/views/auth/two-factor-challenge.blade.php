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
                <div class="form-fade-in bg-white p-8  shadow-2xl">
                    <h2 class="text-2xl sm:text-3xl text-black font-bold mb-2">Two Factor Authentication</h2>
                    <p class="text-gray-700 mb-6" x-show="! recovery">Please confirm access to your account by entering the authentication code provided by your authenticator application.</p>
                    <p class="text-gray-700 mb-6" x-cloak x-show="recovery">Please confirm access to your account by entering one of your emergency recovery codes.</p>
                    @if ($errors->any())
                        <div class="mb-4 text-sm text-red-500">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div x-data="{ recovery: false }">
                        <form method="POST" action="{{ route('two-factor.login') }}">
                            @csrf
                            <div class="mt-4" x-show="! recovery">
                                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                                <input id="code" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code" class="block w-full px-4 py-3 bg-dark-input border border-gray-700  text-gray-700 placeholder-gray-500 focus:ring-[#964B00] focus:border-[#964B00] focus:outline-none transition duration-150" />
                            </div>
                            <div class="mt-4" x-cloak x-show="recovery">
                                <label for="recovery_code" class="block text-sm font-medium text-gray-700 mb-1">Recovery Code</label>
                                <input id="recovery_code" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" class="block w-full px-4 py-3 bg-dark-input border border-gray-700  text-gray-700 placeholder-gray-500 focus:ring-[#964B00] focus:border-[#964B00] focus:outline-none transition duration-150" />
                            </div>
                            <div class="flex items-center justify-end mt-4 gap-2">
                                <button type="button" class="text-sm text-[#964B00] hover:text-orange-400 underline cursor-pointer" x-show="! recovery" x-on:click="recovery = true; $nextTick(() => { $refs.recovery_code.focus() })">{{ __('Use a recovery code') }}</button>
                                <button type="button" class="text-sm text-[#964B00] hover:text-orange-400 underline cursor-pointer" x-cloak x-show="recovery" x-on:click="recovery = false; $nextTick(() => { $refs.code.focus() })">{{ __('Use an authentication code') }}</button>
                                <button type="submit" class="bg-[#964B00] hover:bg-[#7a3c00] text-white font-semibold py-3 px-6  transition duration-300 shadow-lg shadow-orange-600/40 ms-4">Log in</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection