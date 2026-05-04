<div x-data="{ showLogoutModal: false, isSidebarOpen: false }" class="h-full flex">
  <aside id="sidebar"
    class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-black shadow-lg flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out"
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
            class="flex items-center px-4 py-3 text-gray-700 dark:text-white bg-gray-100 dark:bg-black  text-sm ">
            <span class="material-symbols-outlined text-xl mr-3">home</span>
            Home
          </a>
        </li>
        <li>
          <a href="{{ url('bookings') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 text-sm  ">
            <span class="material-symbols-outlined text-xl mr-3">bed</span>
            Room Bookings
          </a>
        </li>
        <li>
          <a href="{{ url('frontdesk/boat_bookings') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 text-sm  ">
            <span class="material-symbols-outlined text-xl mr-3">sailing</span>

            Boat Bookings
          </a>
        </li>
        <li>
          @php $pendingRefunds = \App\Models\Booking::where('refund_status', 'requested')->count(); @endphp
          <a href="{{ url('/frontdesk/settings') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 text-sm  ">
            <span class="material-symbols-outlined text-xl mr-3">schedule</span>
            Refunds
            @if($pendingRefunds > 0)
              <span class="ml-auto inline-flex items-center px-2 py-0.5  text-xs font-medium bg-red-100 text-red-800">{{ $pendingRefunds }}</span>
            @endif
          </a>
        </li>
        <li>
          <a href="{{ url('create_room') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 text-sm  ">
            <span class="material-symbols-outlined text-xl mr-3">add_circle</span>


            Add Rooms
          </a>
        </li>
        <li>
          <a href="{{ url('view_room') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 text-sm  ">
            <span class="material-symbols-outlined text-xl mr-3">visibility</span>

            View Rooms
          </a>
        </li>
        <li>
          <a href="{{ url('create_boat') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 text-sm  ">
            <span class="material-symbols-outlined text-xl mr-3">add_circle</span>
            Add Boats
          </a>
        </li>
        <li>
          <a href="{{ url('view_boat') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 text-sm  ">
            <span class="material-symbols-outlined text-xl mr-3">visibility</span>

            View Boat
          </a>
        </li>
        <li>
          <a href="{{ url('images_pages') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 text-sm  ">
            <span class="material-symbols-outlined text-xl mr-3">image</span>

            Images
          </a>
        </li>
        <li>
          <a href="{{ url('all_messages') }}"
            class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900 text-sm  ">
            <span class="material-symbols-outlined text-xl mr-3">mail</span>


            Messages
          </a>
        </li>
      </ul>
    </nav>

    <!-- Bottom Section -->
    <div class="px-4 py-4 border-t border-gray-200 dark:border-black flex-shrink-0">
      <a href="{{ route('user.profile') }}" class="block">
        <div class="flex items-center p-2 mb-2  hover:bg-gray-100 dark:hover:bg-gray-900 cursor-pointer">
          <img class="w-10 h-10  mr-3" src="https://placehold.co/40x40/10B981/FFFFFF?text=FR"
            alt="Admin Avatar">
          <div>
            <p class="text-sm font-semibold text-gray-800 dark:text-white">Frontdesk User</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">View Profile</p>
          </div>
        </div>
      </a>

      <button @click="showLogoutModal = true"
        class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900  w-full text-left text-sm font-medium transition duration-150 ease-in-out">
        <span class="material-symbols-outlined text-xl mr-3">logout</span>
        Logout
      </button>
    </div>
  </aside>
  <!-- Page overlay for sidebar (mobile only) -->
  <!-- overlay should be under the sidebar (lower z-index) so it doesn't block sidebar clicks -->
  <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-20 lg:hidden" @click="isSidebarOpen = false"></div>

  <!-- Logout Modal (Global Overlay) -->
  <div x-show="showLogoutModal" x-cloak x-transition.opacity.duration.200ms
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div x-transition.scale.duration.200ms class="bg-white dark:bg-black  p-6 w-80 text-center shadow-xl">
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
