<div class="font-[Inter]">
    <button id="alerts-toggle-button" aria-controls="alerts-sidebar" aria-expanded="false" class="fixed top-1/4 z-[60] transition-all duration-300 ease-in-out 
                   focus:outline-none p-3 rounded-r-lg 
                   bg-[#63360D] text-white shadow-xl 
                   hover:bg-[#8B4E14] active:ring-4 active:ring-[#A15D1A]/50">
        <!-- Menu Icon -->
        <svg id="alerts-menu-icon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <!-- Close Icon (Hidden by default) -->
        <svg id="alerts-close-icon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 hidden" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <!-- Unread badge (updated in JS) -->
        <span id="alerts-unread-badge"
            class="absolute -right-0.5 top-0 inline-flex items-center justify-center px-1.5 py-0.5 text-sm font-medium leading-relaxed text-white bg-[#A15D1A]  hidden">0</span>
    </button>

    <!-- Side Panel -->
    <aside id="alerts-sidebar"
        class="fixed top-0 left-0 w-64 h-full bg-white text-white shadow-2xl z-50 transform -translate-x-full transition-transform duration-300 ease-in-out">

        <div class="p-6 h-full overflow-y-auto">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-medium mb-1 text-black">Alerts & Status</h2>
                    <p class="text-sm text-black">Personal and system alerts</p>
                </div>
            </div>

            <!-- Global banner (preserve existing Alpine bindings if available) -->
            <div id="global-alert-banner"
                class="mb-6 shadow p-4 transition-all text-white duration-500 text-sm"
                x-data="{ status: 'Normal', message: 'All clear. No current disaster warnings for Cabanas Beach Resort.', severity: 'normal' }"
                :class="{
                    'bg-green-500 text-md font-medium text-black': status === 'Normal',
                    'bg-yellow-500 text-md font-medium text-black': status === 'Advisory',
                    'bg-[#7a3c00] text-md font-medium text-black': status === 'Immediate Danger',
                }">
                <div class="flex items-center space-x-4">

                    <!-- Normal -->
                    <template x-if="status === 'Normal'">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </template>

                    <!-- Advisory -->
                    <template x-if="status === 'Advisory'">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.487 0l5.5 9.75A2 2 0 0115.5 16H4.5a2 2 0 01-1.743-3.151l5.5-9.75zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </template>

                    <!-- Immediate Danger -->
                    <template x-if="status === 'Immediate Danger'">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L9.586 10l-2.293 2.293a1 1 0 001.414 1.414L11 11.414l2.293 2.293a1 1 0 001.414-1.414L12.414 10l2.293-2.293a1 1 0 00-1.414-1.414L11 8.586 8.707 7.293z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </template>

                    <div>
                        <h3 class="text-lg font-medium" x-text="status + ' Status'"></h3>
                        <p class="mt-1 text-sm" x-text="message"></p>
                    </div>
                </div>

            </div>

            @auth
                <div id="personal-alerts" class="bg-white p-4 shadow-md bg-gray-200 mb-6">
                    <h4 class="text-lg font-medium text-black mb-3">My Alerts</h4>
                    <div id="personal-alerts-list" class="space-y-3 text-sm text-black">
                        <div class="text-sm text-black">Loading your alerts…</div>
                    </div>
                </div>
            @endauth


            <div class="text-sm text-black">System generated alerts appear here. Refreshes every 45s.</div>

        </div>
    </aside>

</div>
