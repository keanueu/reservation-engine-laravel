<div x-data="{ mobileOpen: false, weatherOpen: false }"
     @keydown.escape.window="mobileOpen = false; weatherOpen = false;">
     <div class="w-full bg-white border-b">
        <div class="max-w-7xl mx-auto py-2 px-6 flex justify-between items-center text-xs md:text-sm">
            <div class="text-gray-900 font-semibold"> <span class="flex items-center gap-1.5 text-black">
                        <span class="material-symbols-outlined" style="font-size: 14px;">location_on</span>
                        Tambobong, Dasol, Pangasinan
                    </span></div>
            <div class="flex items-center gap-2">
                <span class="text-gray-900 font-semibold">Use Code <span class="bg-[#4ade80] text-white px-2 py-0.5 font-bold">"QUICKBUY"</span> and Get Extra <span class="text-green-500 font-bold">15%</span> Discount Today!</span>
            </div>
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-5 text-gray-600 font-medium">
                    @if(Route::has('login'))
                        @auth
                            <div class="relative" x-data="{ open: false }">
                                <button @click.stop="open = !open" class="flex items-center gap-2 text-gray-700 hover:text-gray-900 transition-opacity">
                                    <img class="w-5 h-5 object-cover border border-gray-200"
                                         src="{{ Auth::user()->profile_photo_url ?? 'https://placehold.co/40x40/A15D1A/ffffff?text=U' }}"
                                         alt="{{ Auth::user()->name }}">
                                    <span class="">{{ Auth::user()->name }}</span>
                                    <span class="material-symbols-outlined text-xs transition-transform" :class="{'rotate-180':open}">expand_more</span>
                                </button>
                                <div x-show="open" x-cloak @click.outside="open=false"
                                     class="absolute right-0 mt-2 w-44 bg-white shadow-xl z-[99999] overflow-hidden border border-gray-100">
                                    <a href="{{ route('user.profile') }}" class="flex items-center gap-2 px-4 py-3 text-xs text-gray-700 hover:bg-gray-50 transition-colors">
                                        <span class="material-symbols-outlined text-base">person</span>
                                        Profile
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 w-full px-4 py-3 text-xs text-red-600 hover:bg-red-50 transition-colors text-left">
                                            <span class="material-symbols-outlined text-base">logout</span>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ url('login') }}" class="hover:text-gray-900 transition-opacity">Login</a>
                            <a href="{{ url('register') }}" class="hover:text-gray-900 transition-opacity border-l pl-4 border-gray-200">Register</a>
                        @endauth
                    @endif
                </div>

                <a href="{{ route('cart.show') }}" class="bg-[#ff5a3c] text-white px-4 py-1 rounded flex items-center gap-1 hover:bg-red-600 transition text-xs font-bold">
                    <span class="material-symbols-outlined text-sm">shopping_bag</span>
                    Cart
                </a>
            </div>
        </div>
    </div>

 
    @php
        $navLinks = [
            ['label'=>'Home',      'url'=>url('/')],
            ['label'=>'Bookings',  'url'=>route('my.bookings')],
            ['label'=>'Rooms',     'url'=>url('/home/rooms')],
            ['label'=>'Amenities', 'url'=>url('/home/amenities')],
            ['label'=>'Contact',   'url'=>url('/home/contact')],
        ];
    @endphp

    <div class="relative">
        {{-- Header overlay --}}
        <div class="absolute top-0 left-0 w-full z-30">
            <header class="max-w-7xl mx-auto flex justify-between items-center px-6 py-6">
                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-14 h-14 overflow-hidden">
                        <img src="{{ asset('LOGO-FINAL.png') }}" alt="Cabanas" class="w-full h-full object-contain">
                    </div>
                </a>

                <div class="flex items-center gap-6">
                    {{-- Right actions (Weather) --}}
                    <div class="hidden lg:flex items-center gap-3">
                        @include('home.partials.weather-popover')
                    </div>

                    {{-- Main Nav --}}
                    <nav class="hidden lg:flex items-center bg-[#63360D] text-white rounded-sm overflow-hidden shadow-md">
                        @foreach($navLinks as $link)
                            <a href="{{ $link['url'] }}"
                               class="px-6 py-3 hover:text-gray-300 transition-colors text-sm">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                        <a href="{{ route('booking.dates') }}" class="px-8 py-3 bg-white text-black font-semibold hover:bg-gray-100 transition">Book Now</a>
                    </nav>

                    {{-- Mobile Toggle --}}
                    <div class="lg:hidden flex items-center gap-4">
                         @include('home.partials.weather-popover')
                         <button @click="mobileOpen = true" class="p-2 text-white hover:text-gray-300 transition-colors">
                            <span class="material-symbols-outlined text-3xl">menu</span>
                        </button>
                    </div>
                </div>
            </header>
        </div>

        {{-- Only show hero on homepage or authenticated home --}}
        @if(Request::is('/') || Request::is('home'))
            @include('home.sections.hero')
        @endif
    </div>

    {{-- ── Mobile drawer (slide from right) ── --}}
    <div class="h-20 md:h-28"></div>

    <div x-show="mobileOpen"
         x-cloak
         class="fixed inset-0 z-[99998] lg:hidden"
         @keydown.escape.window="mobileOpen = false">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-250"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileOpen = false">
        </div>

        {{-- Drawer panel (from right) --}}
        <div class="absolute top-0 right-0 h-full w-80 max-w-[85vw] bg-white shadow-2xl flex flex-col"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-250 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">

            {{-- Drawer header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-[#63360D] to-[#8B4E14]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/10 backdrop-blur-sm flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('LOGO-FINAL.png') }}" alt="Cabanas" class="w-8 h-8 object-contain">
                    </div>
                    <div>
                        <p class="text-base text-white">Cabanas</p>
                        <p class="text-[10px] text-white/70">Beach Resort</p>
                    </div>
                </div>
                <button @click="mobileOpen = false" class="p-2 text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            {{-- User section (if authenticated) --}}
            @auth
            <div class="px-6 py-4 bg-gradient-to-br from-gray-50 to-white border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <img class="w-12 h-12 object-cover border-2 border-[#63360D]/20"
                         src="{{ Auth::user()->profile_photo_url ?? 'https://placehold.co/48x48/63360D/ffffff?text=U' }}"
                         alt="{{ Auth::user()->name }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
            @endauth

            {{-- Nav links --}}
            <nav class="flex-1 overflow-y-auto py-2">
                @php
                    $drawerLinks = [
                        ['label'=>'Home',      'url'=>url('/'),                'icon'=>'home'],
                        ['label'=>'Bookings',  'url'=>route('my.bookings'),    'icon'=>'receipt_long'],
                        ['label'=>'Rooms',     'url'=>url('/home/rooms'),      'icon'=>'hotel'],
                        ['label'=>'Amenities', 'url'=>url('/home/amenities'), 'icon'=>'pool'],
                        ['label'=>'Contact',   'url'=>url('/home/contact'),   'icon'=>'mail'],
                    ];
                @endphp

                @foreach($drawerLinks as $i => $link)
                    <a href="{{ $link['url'] }}"
                       @click="mobileOpen = false"
                       class="flex items-center gap-4 px-6 py-4 text-sm text-gray-700 hover:text-[#63360D] hover:bg-orange-50/50 transition-all duration-200 border-b border-gray-50 group"
                       x-transition:enter="transition ease-out duration-300 delay-{{ $i * 50 }}"
                       x-transition:enter-start="opacity-0 translate-x-4"
                       x-transition:enter-end="opacity-100 translate-x-0">
                        <span class="flex h-10 w-10 items-center justify-center bg-gray-100 text-[#63360D] transition-all duration-200 group-hover:bg-[#63360D] group-hover:text-white">
                            <span class="material-symbols-outlined text-xl">{{ $link['icon'] }}</span>
                        </span>
                        <span class="flex-1">{{ $link['label'] }}</span>
                        <span class="material-symbols-outlined text-base text-gray-300 group-hover:text-[#63360D] group-hover:translate-x-1 transition-all duration-200">chevron_right</span>
                    </a>
                @endforeach

                @auth
                    <a href="{{ route('user.profile') }}"
                       @click="mobileOpen = false"
                       class="flex items-center gap-4 px-6 py-4 text-sm text-gray-700 hover:text-[#63360D] hover:bg-orange-50/50 transition-all duration-200 border-b border-gray-50 group">
                        <span class="flex h-10 w-10 items-center justify-center bg-gray-100 text-[#63360D] transition-all duration-200 group-hover:bg-[#63360D] group-hover:text-white">
                            <span class="material-symbols-outlined text-xl">person</span>
                        </span>
                        <span class="flex-1">Profile</span>
                        <span class="material-symbols-outlined text-base text-gray-300 group-hover:text-[#63360D] group-hover:translate-x-1 transition-all duration-200">chevron_right</span>
                    </a>
                @endauth

                {{-- Quick Actions --}}
                <div class="px-6 py-3 bg-gray-50">
                    <p class="text-xs text-gray-500 mb-3">Quick actions</p>
                    <div class="space-y-2">
                        <a href="{{ route('cart.show') }}"
                           @click="mobileOpen = false"
                           class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 bg-white hover:bg-orange-50 border border-gray-200 hover:border-[#63360D] transition-all duration-200 group">
                            <span class="material-symbols-outlined text-xl text-[#63360D]">shopping_bag</span>
                            <span>View Cart</span>
                        </a>
                        <button @click="weatherOpen = true; mobileOpen = false"
                                class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 bg-white hover:bg-orange-50 border border-gray-200 hover:border-[#63360D] transition-all duration-200 group">
                            <span class="material-symbols-outlined text-xl text-[#63360D]">partly_cloudy_day</span>
                            <span>Weather Forecast</span>
                        </button>
                    </div>
                </div>
            </nav>

            {{-- Drawer footer --}}
            <div class="px-6 py-5 border-t border-gray-200 bg-gradient-to-br from-gray-50 to-white space-y-3">
                <a href="{{ route('booking.dates') }}"
                   @click="mobileOpen = false"
                   class="block w-full bg-[#63360D] py-3.5 text-center text-sm text-white shadow-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#4D290A] hover:shadow-lg">
                    <span class="material-symbols-outlined text-xl inline-block mr-2 -mt-1">calendar_month</span>
                    Book Now
                </a>
                @guest
                    <div class="flex gap-2">
                        <a href="{{ url('login') }}" 
                           class="flex-1 text-center py-3 text-xs border-2 border-gray-300 text-gray-700 hover:border-[#964B00] hover:text-[#964B00] transition-all duration-200">
                            Login
                        </a>
                        <a href="{{ url('register') }}" 
                           class="flex-1 text-center py-3 text-xs text-white bg-gray-900 hover:bg-gray-800 transition-all duration-200">
                            Register
                        </a>
                    </div>
                @else
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 py-3 text-sm text-red-600 border-2 border-red-200 hover:bg-red-50 hover:border-red-300 transition-all duration-200">
                            <span class="material-symbols-outlined text-xl">logout</span>
                            Logout
                        </button>
                    </form>
                @endguest
                <div class="flex items-center justify-center gap-2 pt-2">
                    <span class="material-symbols-outlined text-base text-gray-400">location_on</span>
                    <p class="text-[10px] text-gray-500">Tambobong, Dasol, Pangasinan</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Remove the old weather slide-over since we now have the popover --}}
    @include('home.partials.alerts-drawer-content')
</div>
