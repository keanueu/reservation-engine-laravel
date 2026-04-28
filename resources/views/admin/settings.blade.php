@extends('admin.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-10">
    
    <!-- Title for the entire page (aligned with standard admin page headers) -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Profile Settings</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your account information, security, and preferences.</p>
        </div>
        <!-- No action button needed here, but kept the flex structure for consistency -->
        <div>
        </div>
    </div>

    <!-- 1. Update Profile Information Card -->
    @if (Laravel\Fortify\Features::canUpdateProfileInformation())
        <div class="bg-white dark:bg-gray-800  shadow-xl p-6 sm:p-8">
            {{-- Assuming this is a Livewire component for updating profile information --}}
            @livewire('profile.update-profile-information-form')
        </div>
    @endif

    <!-- 2. Update Password Card -->
    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
        <div class="bg-white dark:bg-gray-800  shadow-xl p-6 sm:p-8">
            {{-- Assuming this is a Livewire component for updating password --}}
            @livewire('profile.update-password-form')
        </div>
    @endif

    <!-- 3. Two Factor Authentication Card -->
    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
        <div class="bg-white dark:bg-gray-800  shadow-xl p-6 sm:p-8">
            {{-- Assuming this is a Livewire component for 2FA --}}
            @livewire('profile.two-factor-authentication-form')
        </div>
    @endif

    <!-- 4. Logout Other Browser Sessions Card -->
    <div class="bg-white dark:bg-gray-800  shadow-xl p-6 sm:p-8">
        {{-- Assuming this is a Livewire component for browser sessions --}}
        @livewire('profile.logout-other-browser-sessions-form')
    </div>

    <!-- 5. Delete Account Card (with a warning style) -->
    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
        <div class="bg-white dark:bg-gray-800  shadow-xl p-6 sm:p-8 border border-red-500 dark:border-red-600">
            {{-- Assuming this is a Livewire component for deleting the user account --}}
            @livewire('profile.delete-user-form')
        </div>
    @endif
</div>
@endsection