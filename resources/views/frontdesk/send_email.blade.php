@extends('frontdesk.layouts.app')

@section('content')
<div class="p-4 sm:p-6 space-y-6">
    {{-- Page Title --}}
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
        Send Mail to {{ $email->name }}
    </h1>

    <form action="{{ url('email', $email->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- Form Container --}}
        <div class="bg-white dark:bg-black p-6  shadow-lg space-y-6">

            {{-- Grid for inputs --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Greeting (Input) --}}
                <div class="relative z-0 w-full mb-5 group">
                    <input type="text" name="greeting" id="greeting_input"
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
                        placeholder=" " required />
                    <label for="action_text_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Action Text (e.g., Visit Site)
                    </label>
                </div>

                {{-- Action URL (Input) --}}
                <div class="relative z-0 w-full mb-5 group">
                    <input type="text" name="action_url" id="action_url_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required />
                    <label for="action_url_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Action URL (https://...)
                    </label>
                </div>

                {{-- Endline (Input) --}}
                <div class="relative z-0 w-full mb-5 group">
                    <input type="text" name="endline" id="endline_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required />
                    <label for="endline_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Endline
                    </label>
                </div>
            </div>

            {{-- Mail Body (Textarea, full-width) --}}
            <div class="relative z-0 w-full mb-5 group">
                <textarea name="body" id="body_input" rows="4"
                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-black dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" " required></textarea>
                <label for="body_input"
                    class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                    Mail Body
                </label>
            </div>

            {{-- Submit Button --}}
            <div class="pt-4">
                <button type="submit"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium  hover:bg-blue-700 transition duration-150 ease-in-out shadow-md">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293z">
                        </path>
                    </svg>
                    Send Mail
                </button>
            </div>

        </div>
    </form>
</div>
@endsection
