@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-200 bg-gray-50 text-gray-900 text-sm focus:bg-white focus:border-[#964B00] focus:ring-4 focus:ring-[#964B00]/10 transition-all duration-300 outline-none']) !!}>
