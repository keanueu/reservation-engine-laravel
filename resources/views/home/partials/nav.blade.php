<div x-data="{ mobileOpen: false, weatherOpen: false }"
     @keydown.escape.window="mobileOpen = false; weatherOpen = false;">

    {{-- ── Sticky header wrapper ── --}}
    <div class="fixed inset-x-0 top-0 z-50 shadow-sm">
        {{-- ── Top utility bar ── --}}
        <div class="hidden bg-[#964B00] text-xs md:block">
            <div class="max-w-6xl mx-auto px-6 py-2 flex justify-between items-center">
                <div class="flex items-center gap-6 text-white/80">
                    <span class="flex items-center gap-1.5 text-white">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Tambobong, Dasol, Pangasinan
                    </span>
                    <span class="flex items-center gap-1.5 text-white">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498A1 1 0 0121 15v2a2 2 0 01-2 2h-1C9.716 19 3 12.284 3 6V5z"/></svg>
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
                                    <span class="font-medium">{{ Auth::user()->name }}</span>
                                    <svg class="w-3 h-3 transition-transform" :class="{'rotate-180':open}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" x-cloak @click.outside="open=false"
                                     class="absolute right-0 mt-2 w-44 bg-white shadow-xl z-[99999] overflow-hidden border border-gray-100">
                                    <a href="{{ route('profile.show') }}" class="flex items-center gap-2 px-4 py-3 text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Profile
                                    </a>
                                    <button type="button" data-open-mybookings class="flex items-center gap-2 w-full px-4 py-3 text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors text-left">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        My Bookings
                                    </button>
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 w-full px-4 py-3 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors text-left">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ url('login') }}" class="text-white hover:text-white/70 font-medium transition-opacity">Login</a>
                            <a href="{{ url('register') }}" class="text-white hover:text-white/70 font-medium transition-opacity">Register</a>
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
                    @endphp
                    @foreach($navLinks as $link)
                        <a href="{{ $link['url'] }}"
                           class="group relative text-xs font-semibold text-gray-600 transition-colors hover:text-[#964B00]">
                            {{ $link['label'] }}
                            <span class="absolute -bottom-1 left-0 h-0.5 w-0 bg-[#964B00] transition-all duration-300 group-hover:w-full"></span>
                        </a>
                    @endforeach
                </div>

                {{-- Right actions --}}
                <div class="flex items-center gap-3">
                    {{-- Weather --}}
                    <button @click="weatherOpen = true"
                            class="hidden md:flex items-center gap-1.5 px-3 py-2 text-xs font-semibold border border-gray-200 text-gray-600 hover:border-[#964B00] hover:text-[#964B00] transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                        Weather
                    </button>

                    {{-- Cart --}}
                    <a href="{{ url('/home/roomcart') }}"
                       class="hidden md:flex items-center gap-1.5 px-3 py-2 text-xs font-semibold border border-gray-200 text-gray-600 hover:border-[#964B00] hover:text-[#964B00] transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Cart
                    </a>

                    {{-- Book Now CTA --}}
                    <a href="{{ url('/home/roomcart') }}"
                       class="hidden md:block btn-primary px-6 py-2.5 text-xs 
             
                       ">
                        Book Now
                    </a>

                    {{-- Mobile toggle --}}
                    <button @click="mobileOpen = true" class="lg:hidden p-2 text-gray-700 hover:text-[#964B00] transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
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
                        <p class="font-playfair text-base font-bold tracking-wider text-white">CABANAS</p>
                        <p class="text-[10px] font-semibold text-white/70">Beach Resort</p>
                    </div>
                </div>
                <button @click="mobileOpen = false" class="p-2 text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
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
                        <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
            @endauth

            {{-- Nav links --}}
            <nav class="flex-1 overflow-y-auto py-2">
                @php
                    $drawerLinks = [
                        ['label'=>'Home',      'url'=>url('/'),                'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['label'=>'Rooms',     'url'=>url('/home/rooms'),      'icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        ['label'=>'Amenities', 'url'=>url('/home/amenities'), 'icon'=>'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                        ['label'=>'Contact',   'url'=>url('/home/contact'),   'icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ];
                @endphp

                @foreach($drawerLinks as $i => $link)
                    <a href="{{ $link['url'] }}"
                       @click="mobileOpen = false"
                       class="flex items-center gap-4 px-6 py-4 text-sm font-semibold text-gray-700 hover:text-[#964B00] hover:bg-orange-50/50 transition-all duration-200 border-b border-gray-50 group"
                       x-transition:enter="transition ease-out duration-300 delay-{{ $i * 50 }}"
                       x-transition:enter-start="opacity-0 translate-x-4"
                       x-transition:enter-end="opacity-100 translate-x-0">
                        <span class="flex h-10 w-10 items-center justify-center bg-gray-100 text-[#964B00] transition-all duration-200 group-hover:bg-[#964B00] group-hover:text-white">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/>
                            </svg>
                        </span>
                        <span class="flex-1">{{ $link['label'] }}</span>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-[#964B00] group-hover:translate-x-1 transition-all duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endforeach

                @auth
                    <a href="{{ route('profile.show') }}"
                       @click="mobileOpen = false"
                       class="flex items-center gap-4 px-6 py-4 text-sm font-semibold text-gray-700 hover:text-[#964B00] hover:bg-orange-50/50 transition-all duration-200 border-b border-gray-50 group">
                        <span class="flex h-10 w-10 items-center justify-center bg-gray-100 text-[#964B00] transition-all duration-200 group-hover:bg-[#964B00] group-hover:text-white">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        <span class="flex-1">Profile</span>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-[#964B00] group-hover:translate-x-1 transition-all duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <button type="button" data-open-mybookings @click="mobileOpen = false"
                            class="w-full flex items-center gap-4 px-6 py-4 text-sm font-semibold text-gray-700 hover:text-[#964B00] hover:bg-orange-50/50 transition-all duration-200 border-b border-gray-50 group">
                        <span class="flex h-10 w-10 items-center justify-center bg-gray-100 text-[#964B00] transition-all duration-200 group-hover:bg-[#964B00] group-hover:text-white">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </span>
                        <span class="flex-1 text-left">My Bookings</span>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-[#964B00] group-hover:translate-x-1 transition-all duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @endauth

                {{-- Quick Actions --}}
                <div class="px-6 py-3 bg-gray-50">
                    <p class="text-xs font-bold text-gray-500 tracking-wider mb-3">Quick Actions</p>
                    <div class="space-y-2">
                        <a href="{{ url('/home/roomcart') }}"
                           @click="mobileOpen = false"
                           class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 bg-white hover:bg-orange-50 border border-gray-200 hover:border-[#964B00] transition-all duration-200 group">
                            <svg class="w-5 h-5 text-[#964B00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span>View Cart</span>
                        </a>
                        <button @click="weatherOpen = true; mobileOpen = false"
                                class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-700 bg-white hover:bg-orange-50 border border-gray-200 hover:border-[#964B00] transition-all duration-200 group">
                            <svg class="w-5 h-5 text-[#964B00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                            </svg>
                            <span>Weather Forecast</span>
                        </button>
                    </div>
                </div>
            </nav>

            {{-- Drawer footer --}}
            <div class="px-6 py-5 border-t border-gray-200 bg-gradient-to-br from-gray-50 to-white space-y-3">
                <a href="{{ url('/home/roomcart') }}"
                   @click="mobileOpen = false"
                   class="block w-full bg-[#964B00] py-3.5 text-center text-sm font-bold tracking-wider text-white shadow-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#6b3500] hover:shadow-lg">
                    <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Book Now
                </a>
                @guest
                    <div class="flex gap-2">
                        <a href="{{ url('login') }}" 
                           class="flex-1 text-center py-3 text-xs font-bold tracking-wider border-2 border-gray-300 text-gray-700 hover:border-[#964B00] hover:text-[#964B00] transition-all duration-200">
                            Login
                        </a>
                        <a href="{{ url('register') }}" 
                           class="flex-1 text-center py-3 text-xs font-bold tracking-wider text-white bg-gray-900 hover:bg-gray-800 transition-all duration-200">
                            Register
                        </a>
                    </div>
                @else
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 py-3 text-sm font-bold tracking-wider text-red-600 border-2 border-red-200 hover:bg-red-50 hover:border-red-300 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                @endguest
                <div class="flex items-center justify-center gap-2 pt-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-[10px] text-gray-500 font-medium">Tambobong, Dasol, Pangasinan</p>
                </div>
            </div>
        </div>
    </div>

    @include('home.partials.alerts-drawer-content')

    {{-- Weather slide-over --}}
    <div x-show="weatherOpen" x-cloak
         x-transition:enter="transform transition ease-in-out duration-400"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-0 z-[99999] flex" role="dialog" aria-modal="true">
        <div @click="weatherOpen = false" class="fixed inset-0 bg-black/40"></div>
        <div class="relative ml-auto w-screen max-w-sm bg-white shadow-2xl flex flex-col overflow-y-auto">
            <div class="px-6 py-5 flex justify-between items-center border-b border-gray-100">
                <div>
                    <h2 class="font-playfair text-lg font-semibold text-gray-900">Weather Forecast</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Dasol, Pangasinan</p>
                </div>
                <button @click="weatherOpen = false" class="p-1.5 text-gray-400 hover:text-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 px-6 py-6 bg-gray-50">
                @include('home.hero.weather')
            </div>
        </div>
    </div>
</div>
