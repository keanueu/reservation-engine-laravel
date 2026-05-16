@extends('frontdesk.layouts.app')

@section('content')

<div class="p-4 sm:p-6 space-y-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Add Boat</h1>
    

    <form action="{{ url('add_boat') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="bg-white dark:bg-black p-6  shadow-lg space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Boat Name (Input) --}}
                <div class="relative z-0 w-full mb-5 group">
                    <input type="text" name="name" id="boat_name_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required />
                    <label for="boat_name_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Boat Name
                    </label>
                </div>

                {{-- Price (Input) --}}
                <div class="relative z-0 w-full mb-5 group">
                    <input type="number" name="price" id="price_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required />
                    <label for="price_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Price
                    </label>
                </div>

                {{-- Capacity (Input) --}}
                <div class="relative z-0 w-full mb-5 group">
                    <input type="number" name="capacity" id="capacity_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required />
                    <label for="capacity_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Capacity
                    </label>
                </div>
                
                {{-- Status (Input - Changed to a simple text input for flexibility) --}}
                <div class="relative z-0 w-full mb-5 group">
                    <input type="text" name="status" id="status_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required />
                    <label for="status_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Status
                    </label>
                </div>

                {{-- Start time (Input Type Time) --}}
                <div class="relative z-0 w-full mb-5 group">
                    <input type="text" name="start_time" id="start_time_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " />
                    <label for="start_time_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Start Time
                    </label>
                </div>

                {{-- End time (Input Type Time) --}}
                <div class="relative z-0 w-full mb-5 group">
                    <input type="text" name="end_time" id="end_time_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " />
                    <label for="end_time_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        End Time
                    </label>
                </div>

                {{-- Upload Image (File Input) --}}
                <div class="md:col-span-2"> {{-- Takes full width for better alignment --}}
                    <label for="image_upload" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload Image</label>
                    <input type="file" name="image" id="image_upload"
                        class="block w-full text-sm text-gray-900 border border-gray-300  cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-black dark:border-black dark:placeholder-gray-400 file:mr-4 file:py-2 file:px-4 file: file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">PNG, JPG or JPEG. Leave blank to keep existing image (if editing).</p>
                </div>
            </div>

            {{-- Description (Textarea, takes full width) --}}
            <div class="relative z-0 w-full mb-5 group">
                <textarea name="description" id="description_input" rows="3"
                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" "></textarea>
                <label for="description_input"
                    class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                    Description
                </label>
            </div>
            
            {{-- Terms (Added to match the Room form structure) --}}
            <div class="relative z-0 w-full mb-5 group">
                <textarea name="terms" id="terms_input" rows="3"
                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" "></textarea>
                <label for="terms_input"
                    class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                    Terms & Conditions
                </label>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium  hover:bg-blue-700 transition duration-150 ease-in-out shadow-md">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Boat
                </button>
            </div>

        </div>
    </form>
</div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#start_time_input", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            altInput: true,
            altFormat: "h:i K"
        });
        flatpickr("#end_time_input", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            altInput: true,
            altFormat: "h:i K"
        });
    });
  </script>
@endsection
