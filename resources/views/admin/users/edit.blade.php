@extends('admin.layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
            Edit User: <span class="text-indigo-600 dark:text-indigo-400">{{ $user->name }}</span>
        </h1>
        <a href="{{ route('admin.users.index') }}"
            class="inline-flex items-center px-4 py-2 bg-slate-600 border border-transparent font-semibold text-xs text-white hover:bg-slate-700 active:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to users
        </a>
    </div>

    <!-- Edit User Form Card -->
    <div class="bg-white dark:bg-black  shadow-lg overflow-hidden">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT') <!-- Or PATCH -->
            
            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                            class="mt-1 block w-full  border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-black dark:border-black dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                            class="mt-1 block w-full  border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-black dark:border-black dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                        <input type="password" name="password" id="password"
                            class="mt-1 block w-full  border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-black dark:border-black dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                            aria-describedby="password-help">
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" id="password-help">Leave blank to keep the current password.</p>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="mt-1 block w-full  border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-black dark:border-black dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400">
                    </div>

                    <!-- Role -->
                    <div class="md:col-span-2">
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                        <select name="role" id="role" required
                            class="mt-1 block w-full  border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-black dark:border-black dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400">
                            <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Form Footer -->
            <div class="bg-gray-50 dark:bg-black px-6 py-4 text-right">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent font-semibold text-xs text-white hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Update user
                </button>
            </div>
            
        </form>
    </div>
@endsection

