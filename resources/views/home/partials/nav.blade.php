<div x-data="{ mobileOpen: false, weatherOpen: false }"
     @keydown.escape.window="mobileOpen = false; weatherOpen = false;">

    {{-- ── Sticky header wrapper ── --}}
    <div class="fixed inset-x-0 top-0 z-50 shadow-sm">
        {{-- ── Top utility bar ── --}}
        <div class="hidden bg-[#964B00] text-xs md:block">
            <div class="max-w-6xl mx-auto px-6 py-2 flex justify-between items-center">
                <div class="flex items-center gap-6 text-white/80">
                    <span class="flex items-center gap-1.5 text-white">
                        <span class="material-symbols-outlined" style="font-size: 14px;">location_on</span>
                        Tambobong, Dasol, Pangasinan
                    </span>
                    <span class="flex items-center gap-1.5 text-white">
                        <span class="material-symbols-outlined" style="font-size: 14px;">call</span>
                        +63 912 345 6789
                    </span>
                </div>
                <div class="flex items-center gap-5 text-white/80">
                    @if(Route::has('login'))
                        @auth
                            <div class="relative" x-data="{ open: false }">
                                <button @click.stop="open = !open" class="flex items-center gap-2 text-white hover:text-white/70 transition-opacity">
                                    <img class="w-5 h-5 object-cover border border-white/30"
                                         src="{{ Auth::user()->profile_photo_url ?? 'https://placehold.co/40x40/964B00/ffffff?text=U' }}"
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
                            <a href="{{ url('login') }}" class="text-white hover:text-white/70 transition-opacity">Login</a>
                            <a href="{{ url('register') }}" class="text-white hover:text-white/70 transition-opacity">Register</a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Main navbar ── --}}
        <nav id="navbar" class="w-full bg-white border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex items-center justify-between h-20">

                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-14 h-14 overflow-hidden">
                        <img src="{{ asset('LOGO-FINAL.png') }}" alt="Cabanas" class="w-full h-full object-contain">
                    </div>
                </a>

                {{-- Desktop nav links --}}
                <div class="hidden lg:flex items-center gap-8">
                    @php
                        $navLinks = [
                            ['label'=>'Home',      'url'=>url('/')],
                            ['label'=>'Rooms',     'url'=>url('/home/rooms')],
                            ['label'=>'Amenities', 'url'=>url('/home/amenities')],
                            ['label'=>'Contact',   'url'=>url('/home/contact')],
                        ];
                        if(auth()->check()) {
                            $navLinks[] = ['label'=>'Bookings', 'url'=>route('my.bookings')];
                        }
                    @endphp
                    @foreach($navLinks as $link)
                        <a href="{{ $link['url'] }}"
                           class="group relative text-xs text-gray-600 transition-colors hover:text-[#964B00]">
                            {{ $link['label'] }}
                            <span class="absolute -bottom-1 left-0 h-0.5 w-0 bg-[#964B00] transition-all duration-300 group-hover:w-full"></span>
                        </a>
                    @endforeach
                </div>

                {{-- Right actions --}}
                <div class="flex items-center gap-3">
                    {{-- Weather Popover --}}
                    @include('home.partials.weather-popover')

                    {{-- Cart --}}
                    <a href="{{ route('cart.show') }}"
                       class="hidden md:flex items-center gap-1.5 px-3 py-2 text-xs border border-gray-200 text-gray-600 hover:border-[#964B00] hover:text-[#964B00] transition-colors">
                        <span class="material-symbols-outlined text-base">shopping_bag</span>
                        Cart
                    </a>

                    {{-- Book Now CTA --}}
                    <a href="{{ route('booking.dates') }}"
                       class="hidden md:block btn-primary px-6 py-2.5 text-xs 
             
                       ">
                        Book Now
                    </a>

                    {{-- Mobile toggle --}}
                    <button @click="mobileOpen = true" class="lg:hidden p-2 text-gray-700 hover:text-[#964B00] transition-colors">
                        <span class="material-symbols-outlined text-2xl">menu</span>
                    </button>
                </div>
            </div>
        </div>
        </nav>
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
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-[#964B00] to-[#bf6b1a]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/10 backdrop-blur-sm flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('LOGO-FINAL.png') }}" alt="Cabanas" class="w-8 h-8 object-contain">
                    </div>
                    <div>
                        <p class="text-base tracking-wider text-white">CABANAS</p>
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
                    <img class="w-12 h-12 object-cover border-2 border-[#964B00]/20"
                         src="{{ Auth::user()->profile_photo_url ?? 'https://placehold.co/48x48/964B00/ffffff?text=U' }}"
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
                        ['label'=>'Rooms',     'url'=>url('/home/rooms'),      'icon'=>'hotel'],
                        ['label'=>'Amenities', 'url'=>url('/home/amenities'), 'icon'=>'pool'],
                        ['label'=>'Contact',   'url'=>url('/home/contact'),   'icon'=>'mail'],
                    ];
                    if(auth()->check()) {
                        $drawerLinks[] = ['label'=>'Bookings', 'url'=>route('my.bookings'), 'icon'=>'receipt_long'];
                    }
                @endphp

                @foreach($drawerLinks as $i => $link)
                    <a href="{{ $link['url'] }}"
                       @click="mobileOpen = false"
                       class="flex items-center gap-4 px-6 py-4 text-sm text-gray-700 hover:text-[#964B00] hover:bg-orange-50/50 transition-all duration-200 border-b border-gray-50 group"
                       x-transition:enter="transition ease-out duration-300 delay-{{ $i * 50 }}"
                       x-transition:enter-start="opacity-0 translate-x-4"
                       x-transition:enter-end="opacity-100 translate-x-0">
                        <span class="flex h-10 w-10 items-center justify-center bg-gray-100 text-[#964B00] transition-all duration-200 group-hover:bg-[#964B00] group-hover:text-white">
                            <span class="material-symbols-outlined text-xl">{{ $link['icon'] }}</span>
                        </span>
                        <span class="flex-1">{{ $link['label'] }}</span>
                        <span class="material-symbols-outlined text-base text-gray-300 group-hover:text-[#964B00] group-hover:translate-x-1 transition-all duration-200">chevron_right</span>
                    </a>
                @endforeach

                @auth
                    <a href="{{ route('user.profile') }}"
                       @click="mobileOpen = false"
                       class="flex items-center gap-4 px-6 py-4 text-sm text-gray-700 hover:text-[#964B00] hover:bg-orange-50/50 transition-all duration-200 border-b border-gray-50 group">
                        <span class="flex h-10 w-10 items-center justify-center bg-gray-100 text-[#964B00] transition-all duration-200 group-hover:bg-[#964B00] group-hover:text-white">
                            <span class="material-symbols-outlined text-xl">person</span>
                        </span>
                        <span class="flex-1">Profile</span>
                        <span class="material-symbols-outlined text-base text-gray-300 group-hover:text-[#964B00] group-hover:translate-x-1 transition-all duration-200">chevron_right</span>
                    </a>
                @endauth

                {{-- Quick Actions --}}
                <div class="px-6 py-3 bg-gray-50">
                    <p class="text-xs text-gray-500 tracking-wider mb-3">Quick actions</p>
                    <div class="space-y-2">
                        <a href="{{ route('cart.show') }}"
                           @click="mobileOpen = false"
                           class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 bg-white hover:bg-orange-50 border border-gray-200 hover:border-[#964B00] transition-all duration-200 group">
                            <span class="material-symbols-outlined text-xl text-[#964B00]">shopping_bag</span>
                            <span>View Cart</span>
                        </a>
                        <button @click="weatherOpen = true; mobileOpen = false"
                                class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 bg-white hover:bg-orange-50 border border-gray-200 hover:border-[#964B00] transition-all duration-200 group">
                            <span class="material-symbols-outlined text-xl text-[#964B00]">partly_cloudy_day</span>
                            <span>Weather Forecast</span>
                        </button>
                    </div>
                </div>
            </nav>

            {{-- Drawer footer --}}
            <div class="px-6 py-5 border-t border-gray-200 bg-gradient-to-br from-gray-50 to-white space-y-3">
                <a href="{{ route('booking.dates') }}"
                   @click="mobileOpen = false"
                   class="block w-full bg-[#964B00] py-3.5 text-center text-sm tracking-wider text-white shadow-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#6b3500] hover:shadow-lg">
                    <span class="material-symbols-outlined text-xl inline-block mr-2 -mt-1">calendar_month</span>
                    Book Now
                </a>
                @guest
                    <div class="flex gap-2">
                        <a href="{{ url('login') }}" 
                           class="flex-1 text-center py-3 text-xs tracking-wider border-2 border-gray-300 text-gray-700 hover:border-[#964B00] hover:text-[#964B00] transition-all duration-200">
                            Login
                        </a>
                        <a href="{{ url('register') }}" 
                           class="flex-1 text-center py-3 text-xs tracking-wider text-white bg-gray-900 hover:bg-gray-800 transition-all duration-200">
                            Register
                        </a>
                    </div>
                @else
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 py-3 text-sm tracking-wider text-red-600 border-2 border-red-200 hover:bg-red-50 hover:border-red-300 transition-all duration-200">
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
