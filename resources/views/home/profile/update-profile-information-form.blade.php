<div>
    <form method="POST" action="{{ route('user-profile-information.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="mb-6">
                <input type="file" id="photo" name="photo" class="hidden"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <label for="photo" class="block text-sm font-medium text-black mb-2">Profile photo</label>

                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div x-show="! photoPreview">
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-20 h-20 object-cover border border-gray-200">
                        </div>
                        <div x-show="photoPreview" style="display: none;">
                            <span class="block w-20 h-20 bg-cover bg-no-repeat bg-center border border-gray-200"
                                  x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <button type="button" x-on:click.prevent="$refs.photo.click()"
                                class="px-4 py-2 text-sm font-medium border border-gray-300 text-black hover:bg-gray-50 transition-colors">
                            Select new photo
                        </button>
                    </div>
                </div>

                @error('photo')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-black mb-2">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required autocomplete="name"
                   class="w-full border border-gray-200 px-4 py-3 text-sm text-black focus:outline-none focus:border-[#964B00] transition-colors">
            @error('name')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-black mb-2">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required autocomplete="username"
                   class="w-full border border-gray-200 px-4 py-3 text-sm text-black focus:outline-none focus:border-[#964B00] transition-colors">
            @error('email')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! Auth::user()->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200">
                    <p class="text-sm text-yellow-800">
                        Your email address is unverified.
                    </p>
                </div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3">
            @if (session('status') === 'profile-information-updated')
                <div class="text-sm text-green-600">
                    Saved.
                </div>
            @endif

            <button type="submit"
                    class="px-6 py-3 text-sm font-medium bg-[#964B00] text-white hover:bg-[#7a3c00] transition-colors">
                Save changes
            </button>
        </div>
    </form>
</div>
