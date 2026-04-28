<!-- Alpine data only needs to wrap the entire sidebar & modal -->
<div x-data="{ showLogoutModal: false }" class="h-full flex">
    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 shadow-lg flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">

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
                        class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700  text-sm hover:bg-gray-200 dark:hover:bg-gray-600">
                        <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 12H3l9-9l9 9h-2M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 21v-6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v6" />
                        </svg>
                        <span>Home</span>
                    </a>
                </li>

                <li class="mt-2">
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm ">
                        <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0-8 0M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                        <span>Users</span>
                    </a>
                </li>

                <li class="mt-2">
                    <a href="/admin/settings"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm ">
                        <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <g stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37c1 .608 2.296.07 2.572-1.065" />
                                <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0-6 0" />
                            </g>
                        </svg>
                        <span>Settings</span>
                    </a>
                </li>
                <li class="mt-2">
                    <a href="/admin/chat"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm ">
                        <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1-3 3h-5l-5 3v-3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3zM9.5 9h.01m4.99 0h.01" />
                            <path d="M9.5 13a3.5 3.5 0 0 0 5 0" />
                        </svg>

                        <span>Chatbot</span>
                    </a>
                </li>
                <li class="mt-2">
                    <a href="{{ route('admin.discounts.index') }}"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm ">
                        <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 16V8h2.5a2.5 2.5 0 1 1 0 5H10" />
                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0-18 0" />
                        </svg>

                        <span>Promotions</span>
                    </a>
                </li>
                
                <li class="mt-2">
                    <a href="{{ route('admin.calamity.index') }}"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm ">
                        <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            <path d="M12 9v4" />
                            <path d="M12 17h.01" />
                        </svg>
                        <span>Calamity Management</span>
                    </a>
                </li>
            
            </ul>
        </nav>

        <!-- Bottom Section -->
        <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <a href="{{ route('profile.show') }}" class="block">
                <div
                    class="flex items-center p-2 mb-2  hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                    <img class="w-10 h-10  mr-3" src="https://placehold.co/40x40/10B981/FFFFFF?text=AD"
                        alt="Admin Avatar">
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">Admin User</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">View Profile</p>
                    </div>
                </div>
            </a>

            <button @click="showLogoutModal = true"
                class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700  w-full text-left text-sm font-medium transition duration-150 ease-in-out">
                <svg class="w-5 h-5 mr-3 text-gray-700 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <g stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 8V6a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-7a2 2 0 0 1-2-2v-2" />
                        <path d="M15 12H3l3-3m0 6l-3-3" />
                    </g>
                </svg>
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
            class="bg-white dark:bg-gray-800  p-6 w-80 text-center shadow-xl">
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