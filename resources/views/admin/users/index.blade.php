@extends('admin.layouts.app')
@section('content')
    <!-- Main content wrapper -->
    <div class="p-4 sm:p-6 space-y-6">

        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                    Manage Users
                </h1>
                <p class="text-sm text-gray-500 mt-1">Manage users, their roles, and permissions.</p>
            </div>
            <div>
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium ">
                    <svg class="w-4 h-4 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                    </svg>
                    Add new user
                </a>
            </div>
        </div>

        <!-- User Table Card -->
        <div class="bg-white dark:bg-black  shadow-lg overflow-hidden">

            <!-- Responsive table wrapper -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-max divide-y divide-gray-200 dark:divide-black">
                    <thead class="bg-gray-50 dark:bg-black">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300  tracking-wider">
                                User</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300  tracking-wider">
                                Role</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300  tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-black divide-y divide-gray-200 dark:divide-black">

                        @foreach ($users as $user)
                            <tr>
                                <!-- User Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <!-- Using placeholder image with user initials -->
                                            <img class="h-10 w-10 "
                                                src="https://placehold.co/40x40/6366F1/FFFFFF?text={{ strtoupper(substr($user->name, 0, 1)) }}"
                                                onerror="this.src='https://placehold.co/40x40/6366F1/FFFFFF?text=U'"
                                                alt="{{ $user->name }}'s profile picture">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Role Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($user->role == 'admin')
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold  bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold  bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions Column -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end items-center gap-1">
                                        <!-- Edit Action -->
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors"
                                            aria-label="Edit user {{ $user->name }}">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </a>

                                        <!-- Delete Action -->
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors"
                                                aria-label="Delete user {{ $user->name }}">
                                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M4 7h16m-10 4v6m4-6v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <!-- Show a message if there are no users -->
                        @if ($users->isEmpty())
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    No users found.
                                </td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="p-6 border-t border-gray-200 dark:border-black">
                    <!-- Laravel's default pagination links will need to be styled for Tailwind -->
                    <!-- Publish pagination views with `php artisan vendor:publish --tag=laravel-pagination` and select tailwind.css -->
                    {{ $users->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection

