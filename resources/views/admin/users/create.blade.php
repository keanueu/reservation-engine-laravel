@extends('admin.layouts.app')

@section('content')
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
            Create New User
        </h1>
        <a href="{{ route('admin.users.index') }}"
            class="inline-flex items-center px-4 py-2 bg-slate-600 border border-transparent  font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 active:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Users
        </a>
    </div>

    <!-- Create User Form Card -->
    <div class="bg-white dark:bg-black  shadow-lg overflow-hidden">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            
            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full  border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-black dark:border-black dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="mt-1 block w-full  border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-black dark:border-black dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input type="password" name="password" id="password" required
                            class="mt-1 block w-full  border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-black dark:border-black dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="mt-1 block w-full  border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-black dark:border-black dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400">
                    </div>

                    <!-- Role -->
                    <div class="md:col-span-2">
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                        <select name="role" id="role" required
                            class="mt-1 block w-full  border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-black dark:border-black dark:text-white dark:focus:border-indigo-400 dark:focus:ring-indigo-400">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
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
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent  font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Save User
                </button>
            </div>
            
        </form>
    </div>
@endsection
