<!-- Alpine data only needs to wrap the entire sidebar & modal -->
<div x-data="{ showLogoutModal: false }" class="h-full flex">
    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-black shadow-lg flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">

        <!-- Logo -->
        <div class="p-6 flex-shrink-0">
            <a href="/admin/dashboard" class="flex items-center justify-center">
                <img src="{{ asset('/LOGO-FINAL.png') }}" alt="Bookify Logo" class="w-10 h-10">
                <span class="ml-3 text-3xl font-bold text-gray-800 dark:text-white">Cabanas</span>
            </a>
        </div>

        <!-- Nav Links -->
        <nav class="flex-grow px-4 py-2 overflow-y-auto">
            <ul>
                <li>
                    <a href="/admin/dashboard"
                        class="flex items-center px-4 py-3 text-gray-700 dark:text-white bg-gray-100 dark:bg-black  text-sm hover:bg-gray-200 dark:hover:bg-gray-900">
                        <span class="material-symbols-outlined text-xl mr-3">home</span>
                        <span>Home</span>
                    </a>
                </li>

                <li class="mt-2">
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900 text-sm ">
                        <span class="material-symbols-outlined text-xl mr-3">group</span>
                        <span>Users</span>
                    </a>
                </li>

                <li class="mt-2">
                    <a href="/admin/settings"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900 text-sm ">
                        <span class="material-symbols-outlined text-xl mr-3">settings</span>
                        <span>Settings</span>
                    </a>
                </li>
                <li class="mt-2">
                    <a href="/admin/chat"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900 text-sm ">
                        <span class="material-symbols-outlined text-xl mr-3">chat</span>
                        <span>Chatbot</span>
                    </a>
                </li>
                <li class="mt-2">
                    <a href="{{ route('admin.discounts.index') }}"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900 text-sm ">
                        <span class="material-symbols-outlined text-xl mr-3">sell</span>
                        <span>Promotions</span>
                    </a>
                </li>
                
                <li class="mt-2">
                    <a href="{{ route('admin.calamity.index') }}"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900 text-sm ">
                        <span class="material-symbols-outlined text-xl mr-3">warning</span>
                        <span>Calamity Management</span>
                    </a>
                </li>
            
            </ul>
        </nav>

        <!-- Bottom Section -->
        <div class="px-4 py-4 border-t border-gray-200 dark:border-black flex-shrink-0">
            <a href="{{ route('profile.show') }}" class="block">
                <div
                    class="flex items-center p-2 mb-2  hover:bg-gray-100 dark:hover:bg-gray-900 cursor-pointer">
                    <img class="w-10 h-10  mr-3" src="https://placehold.co/40x40/10B981/FFFFFF?text=AD"
                        alt="Admin Avatar">
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">Admin User</p>
                        <p class="text-xs text-gray-500 dark:text-gray-300">View Profile</p>
                    </div>
                </div>
            </a>

            <button @click="showLogoutModal = true"
                class="flex items-center px-4 py-3 text-gray-600 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-900  w-full text-left text-sm font-medium transition duration-150 ease-in-out">
                <span class="material-symbols-outlined text-xl mr-3">logout</span>
                <span>Logout</span>
            </button>
        </div>

    </aside>

    <!-- Page overlay for sidebar (mobile only) -->
    <!-- overlay should be under the sidebar (lower z-index) so it doesn't block sidebar clicks -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-20 lg:hidden"></div>

    <!-- Logout Modal (Global Overlay) -->
    <div x-show="showLogoutModal" x-cloak x-transition.opacity.duration.200ms
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div x-transition.scale.duration.200ms
            class="bg-white dark:bg-black  p-6 w-80 text-center shadow-xl">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Confirm Logout</h2>
            <p class="text-sm text-gray-500 mb-6 dark:text-gray-300">Are you sure you want to log out?</p>

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
