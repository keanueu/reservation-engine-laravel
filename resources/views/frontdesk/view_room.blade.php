@extends('frontdesk.layouts.app')

@section('content')

    <div class="p-4 sm:p-6 space-y-6">

        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
            Room Inventory Management
        </h1>

        <div class="overflow-x-auto  shadow-md border border-gray-200 dark:border-black">
            <table class="w-full min-w-max divide-y divide-gray-200 dark:divide-black">

                <thead class="bg-gray-50 dark:bg-black">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Room & Image</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Type & Price (₱)</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Specs (Guests/Beds)</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions</th>
                        <th class="relative px-6 py-3">
                            <span class="sr-only">Details</span>
                        </th>
                    </tr>
                </thead>

                @foreach ($datas as $data)
                    <tbody x-data="{ open: false }"
                        class="bg-white dark:bg-black divide-y divide-gray-200 dark:divide-black">

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition duration-150">

                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img src="{{ asset('room/' . $data->image) }}" alt="Room Image"
                                        class="w-12 h-12 object-cover  shadow-sm mr-4">
                                    {{-- Change: text-sm font-semibold -> text-xs font-normal --}}
                                    <div class="text-xs font-normal text-gray-900 dark:text-white">{{ $data->room_name }}
                                    </div>
                                </div>
                            </td>

                            {{-- Change: text-sm font-medium -> text-xs  (on price) --}}
                            <td class="px-6 py-4 text-xs">
                                <div class="text-xs text-gray-900 dark:text-white ">
                                    ₱{{ number_format($data->price, 2) }}
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5  text-[10px] 
                                                    @if($data->room_type == 'deluxe') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                                    @elseif($data->room_type == 'premium') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                    @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @endif capitalize mt-1">
                                    {{ $data->room_type}}
                                </span>
                            </td>

                            {{-- Change: text-sm -> text-xs. Inside strong tags: font-medium ->  --}}
                            <td class="px-6 py-4 text-xs text-center text-gray-700 dark:text-gray-300">
                                <div><strong class=" text-gray-900 dark:text-white">Guests:</strong>
                                    {{ $data->accommodates }}</div>
                                <div><strong class=" text-gray-900 dark:text-white">Beds:</strong> {{ $data->beds }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1">

                                    <a href="{{ url('update_room/' . $data->id) }}"
                                        class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors"
                                        title="Edit Room">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </a>

                                    <a href="{{ url('delete_room/' . $data->id) }}"
                                        onclick="return confirm('Are you sure you want to delete this room?');"
                                        class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors"
                                        title="Delete Room">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 7h16m-10 4v6m4-6v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3" />
                                        </svg>
                                    </a>

                                    <button @click="open = !open"
                                        class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors relative"
                                        title="Toggle Details">
                                        <svg x-show="!open" class="h-5 w-5 absolute inset-0" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <svg x-show="open" x-cloak class="h-5 w-5 absolute inset-0" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Expanded Details Row --}}
                        <tr x-show="open" x-transition.opacity.duration.200ms x-cloak
                            class="bg-gray-50 dark:bg-black border-t border-gray-200 dark:border-black">
                            <td colspan="5" class="px-6 py-3">
                                {{-- Change: outer div is now text-xs --}}
                                <div class="text-xs space-y-2 text-gray-600 dark:text-gray-300">

                                    <p>
                                        {{-- Change: strong tags font-normal/light --}}
                                        <strong class="font-normal text-gray-800 dark:text-white">Check-in:</strong>
                                        {{ \Carbon\Carbon::parse($data->check_in)->format('h:i A') }}
                                        |
                                        <strong class="font-normal text-gray-800 dark:text-white">Check-out:</strong>
                                        {{ \Carbon\Carbon::parse($data->check_out)->format('h:i A') }}
                                    </p>

                                    <p>
                                        <strong class="font-normal text-gray-800 dark:text-white">Amenities:</strong>
                                        @if($data->amenities)
                                            @foreach(explode(',', $data->amenities) as $amenity)
                                                <span
                                                    class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 text-[10px] px-2 py-0.5  mr-1 mb-1 whitespace-nowrap">
                                                    {{ trim($amenity) }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-gray-400 italic">None listed</span>
                                        @endif
                                    </p>

                                    <p class="leading-relaxed text-justify">
                                        <strong class="font-normal text-gray-800 dark:text-white">Description:</strong>
                                        {!! nl2br(e(strip_tags($data->description))) ?? 'No detailed description available.' !!}
                                    </p>

                                    <p class="leading-relaxed text-justify">
                                        <strong class="font-normal text-gray-800 dark:text-white">Terms & Conditions:</strong>
                                        {!! nl2br(e(strip_tags($data->terms))) ?? 'No specific terms and conditions listed.' !!}
                                    </p>

                                </div>
                            </td>
                        </tr>

                    </tbody>
                @endforeach

            </table>
        </div>
    </div>

@endsection
