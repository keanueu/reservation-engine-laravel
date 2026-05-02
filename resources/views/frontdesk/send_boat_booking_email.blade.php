@extends('frontdesk.layouts.app')

@section('content')
  <div class="p-4 sm:p-6 space-y-6">
    {{-- Page Title --}}
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
      Send Email to <span class="text-blue-600 dark:text-blue-500">{{ $booking->name }}</span>
    </h1>

    <form action="{{ url('boat_booking_email', $booking->id) }}" method="POST">
      @csrf
      {{-- Form Container --}}
      <div class="bg-white dark:bg-black p-6  shadow-lg space-y-6">

        {{-- Grid for inputs --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          {{-- Greeting (Input) --}}
          <div class="relative z-0 w-full mb-5 group">
            <input type="text" name="greeting" id="greeting_input" value="Hello {{ $booking->name }},"
              class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
              placeholder=" " required />
            <label for="greeting_input"
              class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
              Greeting
            </label>
          </div>

          {{-- Action Text (Input) --}}
          <div class="relative z-0 w-full mb-5 group">
            <input type="text" name="action_text" id="action_text_input"
              class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
              placeholder=" " />
            <label for="action_text_input"
              class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
              Action Text (e.g., View Booking)
            </label>
          </div>

          {{-- Action URL (Input) --}}
          <div class="relative z-0 w-full mb-5 group">
            <input type="text" name="action_url" id="action_url_input" placeholder=" "
              class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
            <label for="action_url_input"
              class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
              Action URL (e.g., https://...)
            </label>
          </div>

          {{-- Closing Line (Input) --}}
          <div class="relative z-0 w-full mb-5 group">
            <input type="text" name="endline" id="endline_input"
              class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
              placeholder=" " />
            <label for="endline_input"
              class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
              Closing Line
            </label>
          </div>

        </div>

        {{-- Message Body (Textarea, full-width) --}}
        <div class="relative z-0 w-full mb-5 group">
          <textarea name="body" id="body_input" rows="4"
            class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
            placeholder=" "></textarea>
          <label for="body_input"
            class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-1Player origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
            Message Body
          </label>
        </div>

        {{-- Button Container --}}
        <div class="pt-4 flex justify-end gap-3">
          <a href="{{ url()->previous() }}"
            class="inline-flex items-center justify-center px-4 py-2.5 bg-white text-gray-700 text-sm font-medium border border-gray-300  hover:bg-gray-50 transition duration-150 ease-in-out shadow-sm dark:bg-black dark:text-gray-300 dark:border-black dark:hover:bg-gray-900">
            Cancel
          </a>

          <button type="submit"
            class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium  hover:bg-blue-700 transition duration-150 ease-in-out shadow-md">
            <svg class="h-4 w-4 mr-1.5 -ml-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
              fill="currentColor">
              <path
                d="M3.105 3.105a1 1 0 011.414 0L10 8.586l5.48-5.481a1 1 0 111.415 1.414L11.414 10l5.481 5.48a1 1 0 11-1.414 1.415L10 11.414l-5.48 5.481a1 1 0 11-1.415-1.414L8.586 10 3.105 4.519a1 1 0 010-1.414z"
                clip-rule="evenodd" fill-rule="evenodd"
                d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
              <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
            </svg>
            Send Email
          </button>
        </div>

      </div>
    </form>
  </div>
@endsection
