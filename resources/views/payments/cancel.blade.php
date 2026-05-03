@extends('home.layouts.app')

@section('content')
<div class="min-h-screen bg-white flex flex-col items-center justify-center py-16 px-4">
    <div class="max-w-lg w-full mx-auto text-center" data-reveal>

        <div class="flex justify-center mb-8">
            <div class="w-20 h-20 bg-red-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-red-500" style="font-size: 48px;">cancel</span>
            </div>
        </div>

        <h1 class="text-4xl text-gray-900 tracking-tight mb-3">Payment was cancelled</h1>
        <p class="text-base text-gray-600 leading-relaxed mb-8">
            Your payment was not completed and your booking has been released.
            No charges were made to your account.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('cart.show') }}" class="btn-primary px-8 py-3.5 text-sm font-bold tracking-widest uppercase flex items-center justify-center gap-2">
                <span class="material-symbols-outlined" style="font-size:16px;">shopping_cart</span>
                Return to cart
            </a>
            <a href="{{ url('/') }}" class="btn-outline px-8 py-3.5 text-sm font-bold tracking-widest uppercase flex items-center justify-center gap-2">
                <span class="material-symbols-outlined" style="font-size:16px;">home</span>
                Back to home
            </a>
        </div>

    </div>
</div>
@endsection
