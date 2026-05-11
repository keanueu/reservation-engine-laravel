<div>
    <div class="max-w-xl text-sm text-gray-700 mb-6">
        Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
    </div>

    <div>
        <button type="button" onclick="document.getElementById('delete-account-modal').classList.remove('hidden')"
                class="px-6 py-3 text-xs font-bold  bg-red-600 text-white hover:bg-red-700 transition-colors">
            Delete account
        </button>
    </div>

    {{-- Delete User Confirmation Modal --}}
    <div id="delete-account-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="document.getElementById('delete-account-modal').classList.add('hidden')"></div>
            
            <div class="inline-block align-bottom bg-white text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('current-user.destroy') }}">
                    @csrf
                    @method('DELETE')
                    
                    <div class="bg-white px-6 pt-6 pb-4">
                        <h3 class="text-lg text-gray-900 mb-4">Delete account</h3>
                        
                        <p class="text-sm text-gray-600 mb-4">
                            Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
                        </p>

                        <div class="mb-4">
                            <label for="delete_password" class="block text-xs font-bold  text-gray-500 mb-2">Password</label>
                            <input id="delete_password" type="password" name="password" required
                                   class="w-full border border-gray-200 px-4 py-3 text-sm text-gray-700 focus:outline-none focus:border-[#964B00] transition-colors"
                                   placeholder="Password">
                            @error('password')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <button type="button" onclick="document.getElementById('delete-account-modal').classList.add('hidden')"
                                    class="px-4 py-2 text-xs font-bold  border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-6 py-3 text-xs font-bold  bg-red-600 text-white hover:bg-red-700 transition-colors">
                                Delete account
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
