<div>
    <form method="POST" action="{{ route('user-password.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <label for="current_password" class="block text-xs font-bold  text-gray-500 mb-2">Current password</label>
            <input id="current_password" type="password" name="current_password" autocomplete="current-password"
                   class="w-full border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:border-[#964B00] transition-colors">
            @error('current_password')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password" class="block text-xs font-bold  text-gray-500 mb-2">New password</label>
            <input id="password" type="password" name="password" autocomplete="new-password"
                   class="w-full border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:border-[#964B00] transition-colors">
            @error('password')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block text-xs font-bold  text-gray-500 mb-2">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                   class="w-full border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:border-[#964B00] transition-colors">
            @error('password_confirmation')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-3">
            @if (session('status') === 'password-updated')
                <div class="text-sm text-green-600">
                    Saved.
                </div>
            @endif

            <button type="submit"
                    class="px-6 py-3 text-xs font-bold  bg-[#964B00] text-white hover:bg-[#7a3c00] transition-colors">
                Save changes
            </button>
        </div>
    </form>
</div>
