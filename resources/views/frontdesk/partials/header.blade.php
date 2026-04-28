<!-- Header -->
<header
    class="flex justify-between items-center p-4 sm:p-6 bg-white dark:bg-gray-800 border-b dark:border-gray-700 shadow-sm">
    <!-- Mobile Menu Toggle -->
    <button id="sidebarToggle"
        class="lg:hidden p-2  text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
    </button>

    <!-- Search Bar -->
    <div class="relative w-full max-w-xs sm:max-w-sm md:max-w-md ml-4 lg:ml-0"
        x-data="{ q: '', results: null, open: false, timer: null, searchUrl: '{{ route('search') }}', debounce(ms){ clearTimeout(this.timer); this.timer = setTimeout(()=> this.doSearch(), ms) }, doSearch(){ if(!this.q || this.q.length < 2){ this.results = null; this.open = false; return; } fetch(this.searchUrl + '?q=' + encodeURIComponent(this.q)).then(r=>r.json()).then(data=>{ this.results = data; this.open = true }).catch(()=>{ this.results = null; this.open = false }) } }"
        @click.away="open = false">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </span>
        <input type="text" x-model="q" @input="debounce(300)" @focus="q.length >= 2 && doSearch()"
            placeholder="Search bookings, customers..."
            class="w-full pl-10 pr-4 py-2 text-sm  border bg-gray-100 dark:bg-gray-700 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">

        <!-- Results dropdown -->
        <div x-show="open" x-cloak
            class="absolute left-0 right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-50 max-h-64 overflow-auto">
            <template x-if="results && (results.bookings.length || results.guests.length)">
                <div class="p-2">
                    <div class="text-xs text-gray-500 px-2">Bookings</div>
                    <template x-for="b in results.bookings" :key="b.id">
                        <a :href="`/bookings#booking-${b.id}`" @click="open=false"
                            class="block px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                            <div class="font-medium text-sm text-gray-800 dark:text-gray-100" x-text="b.name"></div>
                            <div class="text-xs text-gray-500" x-text="b.email"></div>
                        </a>
                    </template>

                    <div class="mt-2 text-xs text-gray-500 px-2">Guests</div>
                    <template x-for="g in results.guests" :key="g.id">
                        <a :href="`/admin/users/${g.id}/edit`" @click="open=false"
                            class="block px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                            <div class="font-medium text-sm text-gray-800 dark:text-gray-100" x-text="g.name"></div>
                            <div class="text-xs text-gray-500" x-text="g.email"></div>
                        </a>
                    </template>

                    <template x-if="!results.bookings.length && !results.guests.length">
                        <div class="px-3 py-2 text-sm text-gray-500">No results</div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    <!-- Header Icons -->
    <div class="flex items-center space-x-3 sm:space-x-5 ml-auto">
        <!-- Dark Mode Toggle -->
        <button id="darkModeToggle"
            class="p-2  text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
            <!-- Sun Icon (light mode) -->
            <svg id="theme-icon-light" class="w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <!-- Moon Icon (dark mode) -->
            <svg id="theme-icon-dark" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>

        <!-- Notifications -->
        <div class="relative" id="frontdesk-notifications">
            <button id="frontdesk-notif-btn" aria-expanded="false"
                class="p-2  text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 relative focus:outline-none">
                <span id="frontdesk-notif-count"
                    class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-medium leading-none text-white bg-red-600  hidden">0</span>
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>

            {{-- REVISED DROPDOWN CLASSES: w-72 for mobile, centered, then sm:w-96, right-aligned for desktop --}}
            <div id="frontdesk-notif-dropdown" class="hidden absolute mt-2 w-72 sm:w-96 bg-white dark:bg-gray-800  shadow-lg z-50 border border-gray-200 dark:border-gray-700
               left-1/2 -translate-x-1/2 sm:right-0 sm:left-auto sm:translate-x-0">
                <div id="frontdesk-notif-list" class="max-h-64 overflow-auto"></div>
                <div class="p-2 text-center text-xs text-gray-500">
                    <a href="{{ url('all_messages') }}"
                        class="text-indigo-600 dark:text-indigo-400 hover:underline">View all
                        messages</a>
                </div>
            </div>
        </div>

        <div class="relative" x-data="{ open: false }">
            @auth
                <button @click="open = !open"
                    class="flex items-center space-x-2 text-sm font-medium text-gray-700 dark:text-gray-300  p-1 transition-colors duration-200 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900"
                    id="user-menu-button" :aria-expanded="open.toString()" aria-haspopup="true">

                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class=" h-8 w-8 object-cover" src="{{ Auth::user()->profile_photo_url}}"
                            alt="{{ Auth::user()->name}}">
                        <span class="hidden sm:inline">{{ Auth::user()->name}}</span>
                    @else
                        <span class="font-medium">{{ Auth::user()->name}}</span>
                    @endif

                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform duration-200"
                        :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-cloak @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800  shadow-lg py-1 border border-gray-200 dark:border-gray-700 z-50"
                    role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">

                    <a href="{{ route('profile.show') }}"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                        role="menuitem">Profile</a>

                    <div class="border-t border-gray-200 dark:border-gray-700"></div>

                    <form method="POST" action="{{ route('logout') }}" x-data @submit="open = false" role="none">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                            role="menuitem">Logout</button>
                    </form>
                </div>
            @endauth
        </div>


    </div>
</header>


