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
                    <h2 class="text-5xl font-bold leading-tight mb-6">Verify your email.</h2>
                    <p class="text-lg text-white/80 leading-relaxed">Almost there! Please verify your email to unlock all features of your account.</p>
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
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Check Your Inbox</h2>
                        <p class="text-gray-500 text-sm mb-8">We've sent a verification link to your email address.</p>

                        @if (session('status') == 'verification-link-sent')
                            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-sm animate-fade-up">
                                A new verification link has been sent to your email address.
                            </div>
                        @endif

                        <div class="space-y-6">
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="w-full py-4 bg-[#964B00] hover:bg-[#7a3c00] text-white font-bold rounded-xl shadow-lg shadow-[#964B00]/20 transform transition-all duration-300 hover:-translate-y-1 active:scale-95">
                                    Resend Email
                                </button>
                            </form>

                            <div class="flex items-center justify-between pt-4">
                                <a href="{{ route('user.profile') }}" class="text-sm font-bold text-[#964B00] hover:text-[#7a3c00] transition-colors underline underline-offset-4">Edit Profile</a>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-sm font-bold text-gray-500 hover:text-gray-900 transition-colors">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
