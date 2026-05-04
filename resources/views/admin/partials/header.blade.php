<!-- Header -->
<header
    class="flex justify-between items-center p-4 sm:p-6 bg-white dark:bg-black border-b dark:border-black shadow-sm">
    <!-- Mobile Menu Toggle -->
    <button id="sidebarToggle"
        class="lg:hidden p-2  text-gray-600 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900">
        <span class="material-symbols-outlined text-2xl">menu</span>
    </button>

    <div class="relative w-full max-w-xs sm:max-w-sm md:max-w-md ml-4 lg:ml-0">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <span class="material-symbols-outlined text-xl">search</span>
        </span>

        <input type="text" id="search-input" placeholder="Search bookings, guest..."
            class="w-full pl-10 pr-4 py-2 text-sm  border bg-gray-100 dark:bg-black dark:border-black focus:outline-none focus:ring-2 focus:ring-indigo-500">

        <div id="search-results"
            class="absolute z-50 mt-1 w-full bg-white dark:bg-black border border-gray-300 dark:border-black  shadow-lg hidden">
        </div>
    </div>

    <!-- Header Icons -->
    <div class="flex items-center space-x-3 sm:space-x-5 ml-auto">
        <!-- Dark Mode Toggle -->
        <button id="darkModeToggle"
            class="p-2  text-gray-600 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900">
            <span id="theme-icon-light" class="material-symbols-outlined text-xl hidden">light_mode</span>
            <span id="theme-icon-dark" class="material-symbols-outlined text-xl">dark_mode</span>
        </button>

        <div class="relative" id="admin-notifications">
            <button id="admin-notif-btn" aria-expanded="false"
                class="p-2  text-gray-600 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900 relative focus:outline-none">
                <span id="admin-notif-count"
                    class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-medium leading-none text-white bg-red-600  hidden">0</span>
                <span class="material-symbols-outlined text-xl">notifications</span>
            </button>

            {{-- REVISED DROPDOWN CLASSES: Right-aligned, max width set to prevent left overflow on small screens --}}
            <div id="admin-notif-dropdown"
                class="hidden absolute right-0 mt-2 w-72 max-w-xs sm:max-w-sm md:max-w-md bg-white dark:bg-black  shadow-lg z-50 border border-gray-200 dark:border-black">
                <div id="admin-notif-list" class="max-h-64 overflow-auto"></div>
                <div class="p-2 text-center text-xs text-gray-500">
                    <a href="/admin/chat" class="text-indigo-600 dark:text-indigo-400 hover:underline">View chat</a>
                </div>
            </div>
        </div>


        <div class="relative" x-data="{ open: false }">
            @auth
                <button @click="open = !open"
                    class="flex items-center space-x-2 text-sm font-medium text-gray-700 dark:text-white  p-1 transition-colors duration-200 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-black"
                    id="user-menu-button" :aria-expanded="open.toString()" aria-haspopup="true">

                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class=" h-8 w-8 object-cover" src="{{ Auth::user()->profile_photo_url}}"
                            alt="{{ Auth::user()->name}}">
                        <span class="hidden sm:inline">{{ Auth::user()->name}}</span>
                    @else
                        <span class="font-medium">{{ Auth::user()->name}}</span>
                    @endif

                    <span class="material-symbols-outlined text-base text-gray-500 dark:text-gray-400 transition-transform duration-200"
                        :class="{'rotate-180': open}">expand_more</span>
                </button>

                <div x-show="open" x-cloak @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-black  shadow-lg py-1 border border-gray-200 dark:border-black z-50"
                    role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">

                    <a href="{{ route('user.profile') }}"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900"
                        role="menuitem">Profile</a>

                    <div class="border-t border-gray-200 dark:border-black"></div>

                    <form method="POST" action="{{ route('logout') }}" x-data @submit="open = false" role="none">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900"
                            role="menuitem">Logout</button>
                    </form>
                </div>
            @endauth
        </div>

    </div>
</header>


