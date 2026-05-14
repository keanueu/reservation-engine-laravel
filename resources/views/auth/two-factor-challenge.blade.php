@extends('auth.layouts.app')
@section('content')
    <div id="app" class="min-h-screen flex flex-col lg:flex-row bg-white">
        <!-- Left Branding Column -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden group">
            <img src="{{ asset('images/1761967585.jpg') }}" alt="Resort" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
            <div class="absolute inset-0 branding-overlay flex flex-col justify-between p-12 text-white">
                <div class="animate-fade-up">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <img src="{{ asset('LOGO-FINAL.png') }}" alt="Logo" class="h-16 w-auto brightness-110">
                        <span class="text-2xl font-bold tracking-tight">Cabanas</span>
                    </a>
                </div>
                
                <div class="max-w-md animate-fade-up" style="animation-delay: 0.2s">
                    <h2 class="text-5xl font-bold leading-tight mb-6">Extra layer of security.</h2>
                    <p class="text-lg text-white/80 leading-relaxed">Please verify your identity using your authenticator app or recovery codes.</p>
                </div>

                <div class="animate-fade-up" style="animation-delay: 0.4s">
                    <p class="text-sm text-white/60">&copy; 2025 Cabanas Beach Resort. All Rights Reserved.</p>
                </div>
            </div>
        </div>

        <!-- Right Form Column -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-gray-50">
            <div class="w-full max-w-md animate-fade-up">
                <div class="bg-white p-10 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden">
                    <div class="relative" x-data="{ recovery: false }">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Two-Factor Auth</h2>
                        
                        <p class="text-gray-500 text-sm mb-8" x-show="!recovery">Enter the authentication code provided by your app.</p>
                        <p class="text-gray-500 text-sm mb-8" x-cloak x-show="recovery">Enter one of your emergency recovery codes.</p>

                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm animate-fade-up">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-6">
                            @csrf
                            <div class="space-y-2" x-show="!recovery">
                                <label for="code" class="text-sm font-semibold text-gray-700">Authentication Code</label>
                                <div class="relative group">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-[#964B00] transition-colors">
                                        <span class="material-symbols-outlined text-xl">qr_code_2</span>
                                    </span>
                                    <input id="code" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code"
                                        placeholder="000 000"
                                        class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-center text-2xl font-bold tracking-[0.3em] focus:bg-white focus:ring-4 focus:ring-[#964B00]/10 focus:border-[#964B00] outline-none transition-all duration-300">
                                </div>
                            </div>

                            <div class="space-y-2" x-cloak x-show="recovery">
                                <label for="recovery_code" class="text-sm font-semibold text-gray-700">Recovery Code</label>
                                <div class="relative group">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-[#964B00] transition-colors">
                                        <span class="material-symbols-outlined text-xl">shield</span>
                                    </span>
                                    <input id="recovery_code" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code"
                                        placeholder="Enter recovery code"
                                        class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-mono focus:bg-white focus:ring-4 focus:ring-[#964B00]/10 focus:border-[#964B00] outline-none transition-all duration-300">
                                </div>
                            </div>

                            <div class="flex flex-col gap-4">
                                <button type="submit" class="w-full py-4 bg-[#964B00] hover:bg-[#7a3c00] text-white font-bold rounded-xl shadow-lg shadow-[#964B00]/20 transform transition-all duration-300 hover:-translate-y-1 active:scale-95">
                                    Log In
                                </button>

                                <button type="button" class="text-sm font-bold text-[#964B00] hover:text-[#7a3c00] transition-colors underline underline-offset-4 cursor-pointer" 
                                    x-show="!recovery" x-on:click="recovery = true; $nextTick(() => { $refs.recovery_code.focus() })">
                                    Use a recovery code
                                </button>
                                
                                <button type="button" class="text-sm font-bold text-[#964B00] hover:text-[#7a3c00] transition-colors underline underline-offset-4 cursor-pointer" 
                                    x-cloak x-show="recovery" x-on:click="recovery = false; $nextTick(() => { $refs.code.focus() })">
                                    Use an authentication code
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
