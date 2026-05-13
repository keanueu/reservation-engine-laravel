@extends('auth.layouts.app')
@section('content')

    <div id="app" class="min-h-screen flex flex-col lg:flex-row bg-white">

        <!-- Left Branding Column -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden group">
            <img src="{{ asset('images/1758952332.jpg') }}" alt="Resort" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
            <div class="absolute inset-0 branding-overlay flex flex-col justify-between p-12 text-white">
                <div class="animate-fade-up">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <img src="{{ asset('LOGO-FINAL.png') }}" alt="Logo" class="h-16 w-auto brightness-110">
                        <span class="text-2xl font-bold tracking-tight">Cabanas</span>
                    </a>
                </div>
                
                <div class="max-w-md animate-fade-up" style="animation-delay: 0.2s">
                    <h2 class="text-5xl font-bold leading-tight mb-6">Nature's gift to city dwellers.</h2>
                    <p class="text-lg text-white/80 leading-relaxed">Experience the pristine beauty of Tambobong Beach in comfort and style.</p>
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
                    <!-- Decorative Element -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-[#bf6b1a]/5 rounded-bl-full -mr-16 -mt-16"></div>

                    <div class="relative">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h2>
                        <p class="text-gray-500 text-sm mb-8">Please enter your details to sign in.</p>

                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm animate-fade-up">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="space-y-6">
                            @csrf
                            <div class="space-y-2">
                                <label for="email" class="text-sm font-semibold text-gray-700">Email Address</label>
                                <div class="relative group">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-[#964B00] transition-colors">
                                        <span class="material-symbols-outlined text-xl">mail</span>
                                    </span>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                        placeholder="name@example.com"
                                        class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-4 focus:ring-[#964B00]/10 focus:border-[#964B00] outline-none transition-all duration-300">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label for="password" class="text-sm font-semibold text-gray-700">Password</label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#964B00] hover:text-[#7a3c00] transition-colors">Forgot Password?</a>
                                    @endif
                                </div>
                                <div class="relative group">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-[#964B00] transition-colors">
                                        <span class="material-symbols-outlined text-xl">lock</span>
                                    </span>
                                    <input id="password" type="password" name="password" required
                                        placeholder="••••••••"
                                        class="block w-full pl-10 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-4 focus:ring-[#964B00]/10 focus:border-[#964B00] outline-none transition-all duration-300">
                                    <button type="button" data-target="password" class="password-toggle absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                        <span class="material-symbols-outlined text-xl">visibility</span>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <label class="relative flex items-center cursor-pointer group">
                                    <input id="remember_me" type="checkbox" name="remember" class="peer sr-only">
                                    <div class="h-5 w-5 bg-gray-100 border border-gray-200 rounded-md peer-checked:bg-[#964B00] peer-checked:border-[#964B00] transition-all duration-200"></div>
                                    <span class="absolute text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200 left-[3px]">
                                        <span class="material-symbols-outlined text-sm font-bold">check</span>
                                    </span>
                                    <span class="ml-3 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Remember me</span>
                                </label>
                            </div>

                            <button type="submit" class="w-full py-4 bg-[#964B00] hover:bg-[#7a3c00] text-white font-bold rounded-xl shadow-lg shadow-[#964B00]/20 transform transition-all duration-300 hover:-translate-y-1 active:scale-95">
                                Sign In
                            </button>

                            <p class="text-center text-sm text-gray-600">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="font-bold text-[#964B00] hover:text-[#7a3c00] transition-colors underline underline-offset-4">Create account</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection