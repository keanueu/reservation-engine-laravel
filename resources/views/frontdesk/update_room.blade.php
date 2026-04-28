@extends('frontdesk.layouts.app')
@section('content')

<div class="p-4 sm:p-6 space-y-6">
    {{-- Changed title to "Update Room" --}}
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Update Room</h1>
    
    {{-- Form Action is updated to the 'edit_room' URL with data ID --}}
    <form action="{{ url('edit_room', $data->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white dark:bg-gray-800 p-6  shadow-lg space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- 1. Room Name (Input) --}}
                <div class="relative z-0 w-full mb-5 group">
                    <input type="text" name="name" id="room_name_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " value="{{ $data->room_name}}" required />
                    <label for="room_name_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Room Name
                    </label>
                </div>

                {{-- 2. Price (Input) --}}
                <div class="relative z-0 w-full mb-5 group">
                    <input type="number" name="price" id="price_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " value="{{ $data->price}}" required />
                    <label for="price_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Price (₱)
                    </label>
                </div>

                {{-- 3. Accommodates (Input) --}}
                <div class="relative z-0 w-full mb-5 group">
                    <input type="number" name="accommodates" id="accommodates_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " value="{{ $data->accommodates}}" required />
                    <label for="accommodates_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Accommodates (Max Guests)
                    </label>
                </div>
                
                {{-- 4. Beds (Input) --}}
                <div class="relative z-0 w-full mb-5 group">
                    <input type="text" name="beds" id="beds_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " value="{{ $data->beds}}" required />
                    <label for="beds_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Beds (e.g., 1 King, 2 Doubles)
                    </label>
                </div>

                {{-- 5. Room Type (Select) --}}
                <div>
                    <label for="room_type_select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Room Type</label>
                    <select name="room_type" id="room_type_select" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm  focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        {{-- Corrected the select options to use $data->room_type for selection --}}
                        <option value="regular" {{ $data->room_type == 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="premium" {{ $data->room_type == 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="deluxe" {{ $data->room_type == 'deluxe' ? 'selected' : '' }}>Deluxe</option>
                    </select>
                </div>
                
                {{-- 6. Check-in Time (Input Type Time) --}}
                <div class="relative z-0 w-full mb-5 group">
                    {{-- Added value attribute to pre-fill the time --}}
                    <input type="time" name="check_in" id="check_in_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " value="{{ $data->check_in}}" />
                    <label for="check_in_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Check-in Time
                    </label>
                </div>

                {{-- 7. Check-out Time (Input Type Time) --}}
                <div class="relative z-0 w-full mb-5 group">
                    {{-- Added value attribute to pre-fill the time --}}
                    <input type="time" name="check_out" id="check_out_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " value="{{ $data->check_out}}" />
                    <label for="check_out_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Check-out Time
                    </label>
                </div>

                {{-- 8. Amenities (Input) --}}
                <div class="relative z-0 w-full mb-5 group">
                    <input type="text" name="amenities" id="amenities_input"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " value="{{ $data->amenities}}" />
                    <label for="amenities_input"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        Amenities (comma separated)
                    </label>
                </div>

                {{-- 9. Upload Images (File Input & Current Images Preview) --}}
                <div class="md:col-span-2">
                    <label for="image_upload" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload Images</label>
                    <input type="file" name="images[]" id="image_upload" multiple
                        class="block w-full text-sm text-gray-900 border border-gray-300  cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 file:mr-4 file:py-2 file:px-4 file: file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">Leave blank if you don’t want to change the images. Current Images:</p>

                    @php
                        $images = [];
                        if (!empty($data->image)) {
                            $decoded = json_decode($data->image, true);
                            if (is_array($decoded)) {
                                $images = $decoded;
                            } else {
                                // comma separated list?
                                if (strpos($data->image, ',') !== false) {
                                    $images = array_map('trim', explode(',', $data->image));
                                } else {
                                    $images = [$data->image];
                                }
                            }
                        }
                    @endphp

                    <div class="flex flex-wrap gap-2 mt-2">
                        @forelse($images as $img)
                            <div class="w-28 h-20 overflow-hidden rounded shadow">
                                <img class="object-cover w-full h-full" src="{{ asset('room/' . $img) }}" alt="Room Image">
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 dark:text-gray-400">No current images.</p>
                        @endforelse
                    </div>
                </div>
                
            </div>
            
            {{-- 10. Description (Textarea, takes full width) --}}
            <div class="relative z-0 w-full group">
                <textarea name="description" id="description_input" rows="3"
                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" ">{{ $data->description}}</textarea>
                <label for="description_input"
                    class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                    Description
                </label>
            </div>
            
            {{-- 11. Terms (Textarea, takes full width) --}}
            <div class="relative z-0 w-full group">
                <textarea name="terms" id="terms_input" rows="3"
                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" ">{{ $data->terms}}</textarea>
                <label for="terms_input"
                    class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-full rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                    Terms & Conditions
                </label>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium  hover:bg-blue-700 transition duration-150 ease-in-out shadow-md">
                    {{-- Changed icon to Save/Update icon (a simple checkmark or disk would be better for update, but using an edit icon as in the original) --}}
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H10v-1.828l8.586-8.586z"></path>
                    </svg>
                    Update Room
                </button>
            </div>

        </div>
    </form>
</div>

@endsection