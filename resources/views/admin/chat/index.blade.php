@extends('admin.layouts.app')

@section('content')
    <!-- Main content wrapper - ALIGNED TO STANDARD LAYOUT -->
    <div class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-10">

        <!-- Page Header (Aligned to standard admin header) -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
                    Live Chat Conversations
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Monitor and respond to active customer support sessions in real-time.
                </p>
            </div>
            <!-- Empty div to maintain the justify-between spacing -->
            <div>
            </div>
        </div>

        <!-- Chat Layout Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

            <!-- Active Sessions List -->
            <div class="lg:col-span-1 bg-white dark:bg-gray-800  shadow-lg overflow-hidden h-full"
                style="max-height: 700px; display: flex; flex-direction: column;">
                <h2
                    class="text-lg font-semibold text-gray-800 dark:text-white p-5 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                    Active Sessions
                </h2>
                <div class="overflow-y-auto">
                    <ul id="sessionsList" class="divide-y divide-gray-200 dark:divide-gray-700">
                    </ul>
                </div>
            </div>

            <!-- Chat Window -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800  shadow-lg overflow-hidden"
                style="max-height: 700px; display: flex; flex-direction: column;">
                <div class="p-5 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 id="chatHeader" class="text-lg font-semibold text-gray-800 dark:text-white">
                        Select a conversation
                    </h2>
                </div>

                <div id="chatWindow" class="flex-1 p-4 sm:p-6 overflow-y-auto bg-gray-50 dark:bg-gray-900 space-y-4">
                    <div class="flex justify-center items-center h-full">
                        <p class="text-gray-500 dark:text-gray-400">Please select a session to start chatting.</p>
                    </div>
                </div>

                <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex space-x-0">
                        <input id="adminMsg"
                            class="flex-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-l-md p-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Type your reply..." />
                        <button id="sendAdmin"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold p-2 px-4 rounded-r-md transition duration-150 ease-in-out">
                            Send
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('admin-scripts')
    <script src="/js/admin-chat.js"></script>
@endpush
