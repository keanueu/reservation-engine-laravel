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
                    <h2 class="text-5xl font-bold leading-tight mb-6">Security & Peace of Mind.</h2>
                    <p class="text-lg text-white/80 leading-relaxed">Don't worry, we'll help you get back to your account in no time.</p>
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
                    <div class="relative">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Reset Password</h2>
                        <p class="text-gray-500 text-sm mb-8">Enter your email to receive a reset link.</p>

                        @if (session('status'))
                            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-sm animate-fade-up">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm animate-fade-up">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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

                            <button type="submit" class="w-full py-4 bg-[#964B00] hover:bg-[#7a3c00] text-white font-bold rounded-xl shadow-lg shadow-[#964B00]/20 transform transition-all duration-300 hover:-translate-y-1 active:scale-95">
                                Send Reset Link
                            </button>

                            <p class="text-center text-sm text-gray-600">
                                Remember your password? 
                                <a href="{{ route('login') }}" class="font-bold text-[#964B00] hover:text-[#7a3c00] transition-colors underline underline-offset-4">Log In</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection