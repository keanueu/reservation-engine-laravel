@extends('frontdesk.layouts.app')
@section('content')

  <div class="p-4 sm:p-6 space-y-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Update Boat</h1>

    <form action="{{ url('edit_boat', $boat->id) }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="bg-white dark:bg-black p-6  shadow-lg space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          {{-- 1. Boat Name --}}
          <div class="relative z-0 w-full mb-5 group">
            <input type="text" name="name" id="boat_name_input" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 
                                     border-gray-300 appearance-none dark:text-white dark:border-black 
                                     dark:focus:border-blue-500 focus:outline-none focus:ring-0 
                                     focus:border-blue-600 peer" placeholder=" " value="{{ $boat->name }}" required />
            <label for="boat_name_input" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 
                                     duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] 
                                     peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 
                                     peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 
                                     peer-focus:scale-75 peer-focus:-translate-y-6">
              Boat Name
            </label>
          </div>

          {{-- 2. Price --}}
          <div class="relative z-0 w-full mb-5 group">
            <input type="number" name="price" id="price_input" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 
                                     border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black 
                                     dark:focus:border-blue-500 focus:outline-none focus:ring-0 
                                     focus:border-blue-600 peer" placeholder=" " value="{{ $boat->price }}" required />
            <label for="price_input" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 
                                     duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] 
                                     peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 
                                     peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 
                                     peer-focus:scale-75 peer-focus:-translate-y-6">
              Price (₱)
            </label>
          </div>

          {{-- 3. Capacity --}}
          <div class="relative z-0 w-full mb-5 group">
            <input type="number" name="capacity" id="capacity_input" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 
                                     border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black 
                                     dark:focus:border-blue-500 focus:outline-none focus:ring-0 
                                     focus:border-blue-600 peer" placeholder=" " value="{{ $boat->capacity }}"
              required />
            <label for="capacity_input" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 
                                     duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] 
                                     peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 
                                     peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 
                                     peer-focus:scale-75 peer-focus:-translate-y-6">
              Capacity (Max Guests)
            </label>
          </div>

          {{-- 4. Status --}}
          <div class="relative z-0 w-full mb-5 group">
            <input type="text" name="status" id="status_input" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 
                                     border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black 
                                     dark:focus:border-blue-500 focus:outline-none focus:ring-0 
                                     focus:border-blue-600 peer" placeholder=" " value="{{ $boat->status }}" required />
            <label for="status_input" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 
                                     duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] 
                                     peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 
                                     peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 
                                     peer-focus:scale-75 peer-focus:-translate-y-6">
              Status
            </label>
          </div>

          {{-- 5. Start Time --}}
          <div class="relative z-0 w-full mb-5 group">
            <input type="time" name="start_time" id="start_time_input" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 
                                     border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black 
                                     dark:focus:border-blue-500 focus:outline-none focus:ring-0 
                                     focus:border-blue-600 peer" placeholder=" " value="{{ $boat->start_time }}" />
            <label for="start_time_input" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 
                                     duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] 
                                     peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 
                                     peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 
                                     peer-focus:scale-75 peer-focus:-translate-y-6">
              Start Time
            </label>
          </div>

          {{-- 6. End Time --}}
          <div class="relative z-0 w-full mb-5 group">
            <input type="time" name="end_time" id="end_time_input" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 
                                     border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black 
                                     dark:focus:border-blue-500 focus:outline-none focus:ring-0 
                                     focus:border-blue-600 peer" placeholder=" " value="{{ $boat->end_time }}" />
            <label for="end_time_input" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 
                                     duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] 
                                     peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 
                                     peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 
                                     peer-focus:scale-75 peer-focus:-translate-y-6">
              End Time
            </label>
          </div>

          {{-- 7. Upload Image and Description (moved description below image) --}}
          <div class="md:col-span-2">
            <label for="image_upload" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
              Upload Image
            </label>
            <input type="file" name="image" id="image_upload"
              class="block w-full text-sm text-gray-900 border border-gray-300  cursor-pointer 
                                     bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-black dark:border-black 
                                     dark:placeholder-gray-400 file:mr-4 file:py-2 file:px-4 file: file:border-0 
                                     file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">
              Leave blank if you don’t want to change the image. Current Image:
            </p>
            <img width="120" class="rounded shadow mt-2" src="{{ asset('boats/' . $boat->image) }}" alt="Boat Image">

            {{-- Description placed below image --}}
            <div class="mt-4">
              <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Boat Description</h4>
              <textarea name="description" id="description_input" rows="3" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 
                  border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 
                  focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                placeholder=" ">{{ $boat->description }}</textarea>
              <label for="description_input" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 
                  duration-300 transform -translate-y-6 scale-75 top-[2.5rem] -z-10 origin-[0] peer-focus:start-0 
                  peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:translate-y-0 
                  peer-placeholder-shown:scale-100 peer-focus:-translate-y-6">
                Description
              </label>
            </div>
          </div>

        </div>

        {{-- Submit Button --}}
        <div class="pt-4">
          <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm 
                                 font-medium  hover:bg-blue-700 transition duration-150 ease-in-out shadow-md">
            <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 
                                  2.828L11.828 15H10v-1.828l8.586-8.586z"></path>
            </svg>
            Update Boat
          </button>
        </div>

      </div>
    </form>
  </div>

@endsection
