@extends('frontdesk.layouts.app')
@section('content')

    <div class="p-4 sm:p-6 space-y-8">

        <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                Media Gallery
            </h1>

            <form action="{{ url('upload_images') }}" method="POST" enctype="multipart/form-data"
                class="w-full md:w-auto bg-white dark:bg-black p-4  shadow-md border border-gray-200 dark:border-black">
                @csrf

                <div class="flex items-center gap-3">
                    <div class="relative flex-1 min-w-[200px]">
                        <input type="file" name="image" id="upload_image" required
                            class="block w-full text-sm h-[40px] px-4 py-2 text-slate-900 dark:text-gray-100 bg-white dark:bg-black  border border-gray-300 dark:border-black appearance-none
                                    focus:border-blue-500 focus:ring-1 focus:ring-blue-500 hover:border-blue-400 dark:focus:border-blue-400 transition duration-150 cursor-pointer" />
                    </div>

                    <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium  shadow-sm
                                    hover:bg-blue-700 hover:-translate-y-0.5 hover:scale-105 active:scale-95 transition-all duration-200">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Upload
                    </button>
                </div>
            </form>
        </div>

        <hr class="border-gray-200 dark:border-black">

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
            @foreach ($images as $image)
                <div
                    class="flex flex-col bg-white dark:bg-black  shadow-lg hover:shadow-xl overflow-hidden transition duration-300 transform hover:-translate-y-1 group">

                    <div class="relative w-full h-40 overflow-hidden flex-shrink-0">
                        <img src="{{ asset('images/' . $image->image) }}" alt="{{ $image->image }}"
                            class="w-full h-full **object-cover** transition-opacity duration-300 group-hover:opacity-80">
                    </div>

                    <div class="p-3 flex flex-col space-y-2 flex-grow">

                        <p class="text-gray-600 dark:text-gray-300 text-xs font-mono **truncate**" title="{{ $image->image }}">
                            {{ $image->image }}
                        </p>

                        <a href="{{ url('delete_images', $image->id) }}"
                            onclick="return confirm('Are you sure you want to delete the image: {{ $image->image }}?');"
                            class="mt-auto w-full inline-flex items-center justify-center px-3 py-2 bg-red-600 transition duration-150 ease-in-out hover:bg-red-700 text-white text-xs font-medium  shadow-sm">

                            <svg stroke="currentColor" viewBox="0 0 24 24" fill="none" class="h-4 w-4 mr-1">
                                <path
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    stroke-width="2" stroke-linejoin="round" stroke-linecap="round"></path>
                            </svg>
                            Delete
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection
