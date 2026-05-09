@extends('home.layouts.app')

@section('content')
<div class="min-h-screen bg-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Page Header --}}
        <div class="mb-8" data-reveal>
            <h1 class="text-4xl text-gray-900 tracking-tight">Account settings</h1>
            <p class="text-base text-gray-600 mt-3 leading-relaxed">Manage your profile information and account security</p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-sm text-green-700" data-reveal data-reveal-delay="1">
                {{ session('status') }}
            </div>
        @endif

        {{-- Profile Information Section --}}
        <div class="bg-white border border-gray-200 shadow-sm mb-6" data-reveal data-reveal-delay="1">
            <div class="px-6 py-5">
                <h2 class="text-lg text-gray-900">Profile information</h2>
                <p class="text-sm text-gray-600 mt-1">Update your account's profile information and email address</p>
            </div>
            <div class="px-6 py-6 border-t border-gray-200">
                @livewire('profile.update-profile-information-form')
            </div>
        </div>

        {{-- Update Password Section --}}
        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
        <div class="bg-white border border-gray-200 shadow-sm mb-6" data-reveal data-reveal-delay="2">
            <div class="px-6 py-5">
                <h2 class="text-lg text-gray-900">Update password</h2>
                <p class="text-sm text-gray-600 mt-1">Ensure your account is using a long, random password to stay secure</p>
            </div>
            <div class="px-6 py-6 border-t border-gray-200">
                @livewire('profile.update-password-form')
            </div>
        </div>
        @endif

        {{-- Two Factor Authentication --}}
        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
        <div class="bg-white border border-gray-200 shadow-sm mb-6" data-reveal data-reveal-delay="3">
            <div class="px-6 py-5">
                <h2 class="text-lg text-gray-900">Two factor authentication</h2>
                <p class="text-sm text-gray-600 mt-1">Add additional security to your account using two factor authentication</p>
            </div>
            <div class="px-6 py-6 border-t border-gray-200">
                @livewire('profile.two-factor-authentication-form')
            </div>
        </div>
        @endif

        {{-- Browser Sessions --}}
        <div class="bg-white border border-gray-200 shadow-sm mb-6" data-reveal data-reveal-delay="4">
            <div class="px-6 py-5">
                <h2 class="text-lg text-gray-900">Browser sessions</h2>
                <p class="text-sm text-gray-600 mt-1">Manage and log out your active sessions on other browsers and devices</p>
            </div>
            <div class="px-6 py-6 border-t border-gray-200">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>
        </div>

        {{-- Delete Account Section --}}
        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
        <div class="bg-white border border-red-200 shadow-sm mb-6" data-reveal data-reveal-delay="5">
            <div class="px-6 py-5">
                <h2 class="text-lg text-red-900">Delete account</h2>
                <p class="text-sm text-red-700 mt-1">Permanently delete your account</p>
            </div>
            <div class="px-6 py-6 border-t border-red-200">
                @livewire('profile.delete-user-form')
            </div>
        </div>
        @endif

    </div>
</div>

<style>
/* Custom styles for Livewire profile forms - scoped to profile content only */
/* Use more specific selectors to avoid affecting sidebar and other elements */

/* Force white backgrounds on all profile sections and their children */
.max-w-7xl .bg-white,
.max-w-7xl .bg-white > div,
.max-w-7xl .bg-white div[class*="bg-gray"],
.max-w-7xl .bg-gray-50,
.max-w-7xl .bg-gray-100,
.max-w-7xl .bg-gray-700,
.max-w-7xl .bg-gray-800,
.max-w-7xl .bg-gray-900,
.max-w-7xl .dark\:bg-gray-700,
.max-w-7xl .dark\:bg-gray-800,
.max-w-7xl .dark\:bg-gray-900 {
    background-color: white !important;
}

/* Target Livewire component wrappers */
.max-w-7xl [wire\:id],
.max-w-7xl [wire\:id] > div {
    background-color: white !important;
}

/* Form inputs - only within profile page content */
.max-w-7xl > div > .bg-white input[type="text"],
.max-w-7xl > div > .bg-white input[type="email"],
.max-w-7xl > div > .bg-white input[type="password"],
.max-w-7xl > div > .bg-white input[type="file"],
.max-w-7xl > div > .bg-white select,
.max-w-7xl > div > .bg-white textarea {
    width: 100%;
    border: 1px solid #e5e7eb !important;
    padding: 0.75rem 1rem !important;
    font-size: 0.875rem !important;
    color: #111827 !important;
    background: white !important;
    transition: border-color 0.2s;
    border-radius: 0 !important;
}

.max-w-7xl > div > .bg-white input[type="text"]:focus,
.max-w-7xl > div > .bg-white input[type="email"]:focus,
.max-w-7xl > div > .bg-white input[type="password"]:focus,
.max-w-7xl > div > .bg-white select:focus,
.max-w-7xl > div > .bg-white textarea:focus {
    outline: none !important;
    border-color: #964B00 !important;
    box-shadow: none !important;
    background: white !important;
}

/* Labels - only within profile page content */
.max-w-7xl > div > .bg-white label {
    display: block;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.05em !important;
    text-transform: uppercase !important;
    color: #6b7280 !important;
    margin-bottom: 0.5rem !important;
}

/* Buttons - only within profile page content */
.max-w-7xl > div > .bg-white button[type="submit"],
.max-w-7xl > div > .bg-white button[wire\:click*="enable"],
.max-w-7xl > div > .bg-white button[wire\:click*="update"],
.max-w-7xl > div > .bg-white button[wire\:click*="save"] {
    background: #964B00 !important;
    color: white !important;
    padding: 0.75rem 1.5rem !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.1em !important;
    text-transform: uppercase !important;
    border: none !important;
    cursor: pointer;
    transition: background 0.2s;
    border-radius: 0 !important;
}

.max-w-7xl > div > .bg-white button[type="submit"]:hover,
.max-w-7xl > div > .bg-white button[wire\:click*="enable"]:hover,
.max-w-7xl > div > .bg-white button[wire\:click*="update"]:hover,
.max-w-7xl > div > .bg-white button[wire\:click*="save"]:hover {
    background: #7a3c00 !important;
}

.max-w-7xl > div > .bg-white button[type="button"],
.max-w-7xl > div > .bg-white button[wire\:click*="show"],
.max-w-7xl > div > .bg-white button[wire\:click*="regenerate"] {
    background: white !important;
    color: #6b7280 !important;
    padding: 0.75rem 1.5rem !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.1em !important;
    text-transform: uppercase !important;
    border: 1px solid #e5e7eb !important;
    cursor: pointer;
    transition: all 0.2s;
    border-radius: 0 !important;
}

.max-w-7xl > div > .bg-white button[type="button"]:hover,
.max-w-7xl > div > .bg-white button[wire\:click*="show"]:hover,
.max-w-7xl > div > .bg-white button[wire\:click*="regenerate"]:hover {
    background: #f9fafb !important;
    border-color: #d1d5db !important;
}

/* Danger buttons - only within profile page content */
.max-w-7xl > div > .bg-white button[wire\:click*="delete"],
.max-w-7xl > div > .bg-white button[wire\:click*="disable"],
.max-w-7xl > div > .bg-white button[wire\:click*="confirmUserDeletion"],
.max-w-7xl > div > .bg-white button[wire\:click*="deleteUser"] {
    background: #dc2626 !important;
    color: white !important;
    padding: 0.75rem 1.5rem !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.1em !important;
    text-transform: uppercase !important;
    border: none !important;
    cursor: pointer;
    transition: background 0.2s;
    border-radius: 0 !important;
}

.max-w-7xl > div > .bg-white button[wire\:click*="delete"]:hover,
.max-w-7xl > div > .bg-white button[wire\:click*="disable"]:hover,
.max-w-7xl > div > .bg-white button[wire\:click*="confirmUserDeletion"]:hover,
.max-w-7xl > div > .bg-white button[wire\:click*="deleteUser"]:hover {
    background: #b91c1c !important;
}

/* Logout buttons - only within profile page content */
.max-w-7xl > div > .bg-white button[wire\:click*="logout"],
.max-w-7xl > div > .bg-white button[wire\:click*="confirmLogout"],
.max-w-7xl > div > .bg-white button[wire\:click*="logoutOtherBrowserSessions"] {
    background: #964B00 !important;
    color: white !important;
    padding: 0.75rem 1.5rem !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.1em !important;
    text-transform: uppercase !important;
    border: none !important;
    cursor: pointer;
    transition: background 0.2s;
    border-radius: 0 !important;
}

.max-w-7xl > div > .bg-white button[wire\:click*="logout"]:hover,
.max-w-7xl > div > .bg-white button[wire\:click*="confirmLogout"]:hover,
.max-w-7xl > div > .bg-white button[wire\:click*="logoutOtherBrowserSessions"]:hover {
    background: #7a3c00 !important;
}

/* Profile photo - only within profile page content */
.max-w-7xl > div > .bg-white .size-20,
.max-w-7xl > div > .bg-white img[alt*="profile"],
.max-w-7xl > div > .bg-white img[class*="rounded"] {
    width: 5rem !important;
    height: 5rem !important;
    border-radius: 0 !important;
    object-fit: cover;
    border: 1px solid #e5e7eb !important;
}

/* Action messages - only within profile page content */
.max-w-7xl > div > .bg-white .text-green-600 {
    color: #059669 !important;
}

/* Error messages - only within profile page content */
.max-w-7xl > div > .bg-white .text-red-600 {
    color: #dc2626 !important;
    font-size: 0.875rem !important;
    margin-top: 0.5rem !important;
}

/* Remove rounded corners from modals - only profile modals */
.max-w-7xl > div > .bg-white [x-show],
.max-w-7xl > div > .bg-white .modal,
.max-w-7xl > div > .bg-white [role="dialog"] {
    border-radius: 0 !important;
    background: white !important;
}

/* Grid layout adjustments - only within profile page content */
.max-w-7xl > div > .bg-white .col-span-6,
.max-w-7xl > div > .bg-white .col-span-4 {
    margin-bottom: 1.5rem;
}

/* Remove max-width constraints - only within profile page content */
.max-w-7xl > div > .bg-white .max-w-xl {
    max-width: 100% !important;
}

/* QR Code styling - only within profile page content */
.max-w-7xl > div > .bg-white svg[viewBox] {
    max-width: 200px;
    height: auto;
}

/* Recovery codes - only within profile page content */
.max-w-7xl > div > .bg-white .font-mono {
    background: #f3f4f6 !important;
    padding: 1rem;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
}

/* Text colors within profile sections only */
.max-w-7xl > div > .bg-white .text-sm {
    color: #6b7280 !important;
}

.max-w-7xl > div > .bg-white h3,
.max-w-7xl > div > .bg-white h2,
.max-w-7xl > div > .bg-white h1 {
    color: #111827 !important;
}
</style>
@endsection
