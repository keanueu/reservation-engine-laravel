<div x-data="{ showLogoutModal: false, isSidebarOpen: false }" class="h-full flex">
  <aside id="sidebar"
    class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 shadow-lg flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
    :class="{ 'translate-x-0': isSidebarOpen }">

    <div class="p-6">
      <a href="/home" class="flex items-center justify-center">
        <img src="{{ asset('/LOGO-FINAL.png') }}" alt="Bookify Logo" class="w-10 h-10">
        <span class="ml-3 text-3xl font-bold text-gray-800 dark:text-white">Cabanas</span>
      </a>
    </div>

    <nav class="px-4 py-2 flex-grow overflow-y-auto">
      <ul class="space-y-2">
        <li>
          <a href="{{ url('/home') }}"
            class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700  text-sm ">
            <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M5 12H3l9-9l9 9h-2M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 21v-6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v6" />
            </svg>
            Home
          </a>
        </li>
        <li>
          <a href="{{ url('bookings') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm  ">
            <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round">
              <path d="M8 12a3 3 0 0 1 3-3h26a3 3 0 0 1 3 3v11H8z" />
              <path d="M6 35v4" />
              <path d="M42 35v4" />
              <path fill="currentColor"
                d="M20 18h-6a3 3 0 0 0-3 3v2h12v-2a3 3 0 0 0-3-3m14 0h-6a3 3 0 0 0-3 3v2h12v-2a3 3 0 0 0-3-3" />
              <path d="M4 26a3 3 0 0 1 3-3h34a3 3 0 0 1 3 3v9H4z" />
            </svg>
            Room Bookings
          </a>
        </li>
        <li>
          <a href="{{ url('frontdesk/boat_bookings') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm  ">
            <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 512 512" fill="none" stroke="currentColor" stroke-width="28" stroke-linecap="round"
              stroke-linejoin="round" stroke-miterlimit="10">
              <path
                d="M461.93 261.05c-2-4.76-6.71-7.83-11.67-9.49l-187.18-74.48a23.8 23.8 0 0 0-14.17 0l-187 74.52c-5 1.56-9.83 4.77-11.81 9.53s-2.94 9.37-1 15.08l46.53 119.15a7.46 7.46 0 0 0 7.47 4.64c26.69-1.68 50.31-15.23 68.38-32.5a7.66 7.66 0 0 1 10.49 0C201.29 386 227 400 256 400s54.56-14 73.88-32.54a7.67 7.67 0 0 1 10.5 0c18.07 17.28 41.69 30.86 68.38 32.54a7.45 7.45 0 0 0 7.46-4.61l46.7-119.16c1.98-4.78.99-10.41-.99-15.18Z" />
              <path fill="currentColor"
                d="M416 473.14a6.84 6.84 0 0 0-3.56-6c-27.08-14.55-51.77-36.82-62.63-48a10.05 10.05 0 0 0-12.72-1.51c-50.33 32.42-111.61 32.44-161.95.05a10.09 10.09 0 0 0-12.82 1.56c-10.77 11.28-35.19 33.3-62.43 47.75a7.15 7.15 0 0 0-3.89 5.73a6.73 6.73 0 0 0 7.92 7.15c20.85-4.18 41-13.68 60.2-23.83a8.71 8.71 0 0 1 8-.06A185.14 185.14 0 0 0 340 456a8.82 8.82 0 0 1 8.09.06c19.1 10 39.22 19.59 60 23.8a6.72 6.72 0 0 0 7.95-6.71Z" />
              <path d="M320 96V72a24.07 24.07 0 0 0-24-24h-80a24.07 24.07 0 0 0-24 24v24" />
              <path d="M416 233v-89a48.14 48.14 0 0 0-48-48H144a48.14 48.14 0 0 0-48 48v92" />
              <path d="M256 180.6v212.85" />
            </svg>

            Boat Bookings
          </a>
        </li>
        <li>
          @php $pendingRefunds = \App\Models\Booking::where('refund_status', 'requested')->count(); @endphp
          <a href="{{ url('/frontdesk/settings') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm  ">
            <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Refunds
            @if($pendingRefunds > 0)
              <span class="ml-auto inline-flex items-center px-2 py-0.5  text-xs font-medium bg-red-100 text-red-800">{{ $pendingRefunds }}</span>
            @endif
          </a>
        </li>
        <li>
          <a href="{{ url('create_room') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm  ">
            <svg class="w-5 h-4.5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
              stroke-linejoin="round">
              <circle cx="10" cy="10" r="9" />
              <line x1="10" y1="6" x2="10" y2="14" />
              <line x1="6" y1="10" x2="14" y2="10" />
            </svg>


            Add Rooms
          </a>
        </li>
        <li>
          <a href="{{ url('view_room') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm  ">
            <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
              stroke-linejoin="round">
              <path
                d="M21.544 11.045c.304.426.456.64.456.955c0 .316-.152.529-.456.955C20.178 14.871 16.689 19 12 19c-4.69 0-8.178-4.13-9.544-6.045C2.152 12.529 2 12.315 2 12c0-.316.152-.529.456-.955C3.822 9.129 7.311 5 12 5c4.69 0 8.178 4.13 9.544 6.045Z" />
              <path d="M15 12a3 3 0 1 0-6 0a3 3 0 0 0 6 0Z" />
            </svg>

            View Rooms
          </a>
        </li>
        <li>
          <a href="{{ url('create_boat') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm  ">
            <svg class="w-5 h-4.5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
              stroke-linejoin="round">
              <circle cx="10" cy="10" r="9" />
              <line x1="10" y1="6" x2="10" y2="14" />
              <line x1="6" y1="10" x2="14" y2="10" />
            </svg>
            Add Boats
          </a>
        </li>
        <li>
          <a href="{{ url('view_boat') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm  ">
            <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
              stroke-linejoin="round">
              <path
                d="M21.544 11.045c.304.426.456.64.456.955c0 .316-.152.529-.456.955C20.178 14.871 16.689 19 12 19c-4.69 0-8.178-4.13-9.544-6.045C2.152 12.529 2 12.315 2 12c0-.316.152-.529.456-.955C3.822 9.129 7.311 5 12 5c4.69 0 8.178 4.13 9.544 6.045Z" />
              <path d="M15 12a3 3 0 1 0-6 0a3 3 0 0 0 6 0Z" />
            </svg>

            View Boat
          </a>
        </li>
        <li>
          <a href="{{ url('images_pages') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm  ">
            <svg class="w-5 h-4.5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 512 512" fill="none" stroke="currentColor" stroke-width="30" stroke-linecap="round"
              stroke-linejoin="round" stroke-miterlimit="10">
              <path d="M432 112V96a48.14 48.14 0 0 0-48-48H64a48.14 48.14 0 0 0-48 48v256a48.14 48.14 0 0 0 48 48h16" />
              <rect x="96" y="128" width="400" height="336" rx="45.99" ry="45.99" />
              <ellipse cx="372.92" cy="219.64" rx="30.77" ry="30.55" />
              <path d="M342.15 372.17L255 285.78a30.93 30.93 0 0 0-42.18-1.21L96 387.64" />
              <path d="M265.23 464l118.59-117.73a31 31 0 0 1 41.46-1.87L496 402.91" />
            </svg>

            Images
          </a>
        </li>
        <li>
          <a href="{{ url('all_messages') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm  ">
            <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
              stroke-linejoin="round">
              <path d="M19.5 4.5h-18l3 5v7a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3v-9a3 3 0 0 0-3-3Z" />
              <path d="M7.5 8h12" />
              <path d="M7.5 11.5h12" />
              <path d="M7.5 15H16" />
            </svg>


            Messages
          </a>
        </li>
      </ul>
    </nav>

    <!-- Bottom Section -->
    <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
      <a href="{{ route('profile.show') }}" class="block">
        <div class="flex items-center p-2 mb-2  hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
          <img class="w-10 h-10  mr-3" src="https://placehold.co/40x40/10B981/FFFFFF?text=FR"
            alt="Admin Avatar">
          <div>
            <p class="text-sm font-semibold text-gray-800 dark:text-white">Frontdesk User</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">View Profile</p>
          </div>
        </div>
      </a>

      <button @click="showLogoutModal = true"
        class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700  w-full text-left text-sm font-medium transition duration-150 ease-in-out">
        <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" fill="none"
          viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <g stroke-linecap="round" stroke-linejoin="round">
            <path d="M10 8V6a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-7a2 2 0 0 1-2-2v-2" />
            <path d="M15 12H3l3-3m0 6l-3-3" />
          </g>
        </svg>
        Logout
      </button>
    </div>
  </aside>
  <!-- Page overlay for sidebar (mobile only) -->
  <!-- overlay should be under the sidebar (lower z-index) so it doesn't block sidebar clicks -->
  <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-20 lg:hidden"></div>

  <!-- Logout Modal (Global Overlay) -->
  <div x-show="showLogoutModal" x-cloak x-transition.opacity.duration.200ms
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div x-transition.scale.duration.200ms class="bg-white dark:bg-gray-800  p-6 w-80 text-center shadow-xl">
      <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Confirm Logout</h2>
      <p class="text-sm text-gray-500 mb-6 dark:text-gray-400">Are you sure you want to log out?</p>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="bg-red-600 text-white px-4 py-2  hover:bg-red-700 w-full mb-2">
          Yes, Logout
        </button>
      </form>

      <button @click="showLogoutModal = false"
        class="bg-gray-200 text-gray-800 px-4 py-2  hover:bg-gray-300 w-full">
        Cancel
      </button>
    </div>
  </div>
</div>