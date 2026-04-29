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
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        <span>Home</span>
                    </a>
                </li>

                <li class="mt-2">
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm ">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        <span>Users</span>
                    </a>
                </li>

                <li class="mt-2">
                    <a href="/admin/settings"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm ">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Settings</span>
                    </a>
                </li>
                <li class="mt-2">
                    <a href="/admin/chat"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm ">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                        </svg>
                        <span>Chatbot</span>
                    </a>
                </li>
                <li class="mt-2">
                    <a href="{{ route('admin.discounts.index') }}"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm ">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                        </svg>
                        <span>Promotions</span>
                    </a>
                </li>
                
                <li class="mt-2">
                    <a href="{{ route('admin.calamity.index') }}"
                        class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm ">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
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
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
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