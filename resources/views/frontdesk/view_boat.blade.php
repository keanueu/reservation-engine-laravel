@extends('frontdesk.layouts.app')

@section('content')

    <div class="p-4 sm:p-6 space-y-6">

        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
            Boat Inventory Management
        </h1>

        <div class="overflow-x-auto  shadow">
            <table class="w-full min-w-max divide-y divide-gray-200 dark:divide-gray-700">

                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Boat Name / Info</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Pricing / Capacity</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Inventory / Status</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Image</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions</th>
                        <th scope="col" class="px-6 py-3 text-right">
                            <span class="sr-only">Details</span>
                        </th>
                    </tr>
                </thead>

                @foreach ($boats as $boat)
                    <tbody x-data="{ open: false }"
                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">

                            {{-- Boat Name / Info --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{-- Changed text-sm font-medium to text-xs font-normal --}}
                                <div class="text-xs font-normal text-gray-900 dark:text-white">{{ $boat->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Duration:
                                    {{-- Changed font-medium to  --}}
                                    <span class="">
                                        {{ \Carbon\Carbon::parse($boat->start_time)->format('h:i A') }} -
                                        {{ \Carbon\Carbon::parse($boat->end_time)->format('h:i A') }}
                                    </span>
                                </div>
                            </td>

                            {{-- Pricing / Capacity --}}
                            {{-- Changed text-sm to text-xs --}}
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900 dark:text-white">
                                {{-- Changed font-medium to  --}}
                                <div><span class="">Price:</span> ₱{{ number_format($boat->price ?? 0, 2) }}</div>
                                <div><span class="">Capacity:</span> {{ $boat->capacity }} pax</div>
                            </td>

                            {{-- Inventory / Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{-- Changed text-sm to text-xs --}}
                                <div class="text-xs text-gray-900 dark:text-white">Quantity: {{ $boat->quantity }}</div>
                                {{-- Status badge: font-medium changed to  --}}
                                <span class="mt-1 inline-flex items-center px-2.5 py-0.5  text-xs  
                                                     @if(strtolower($boat->status) == 'available') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                     @elseif(strtolower($boat->status) == 'maintenance') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                     @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                     @endif capitalize">
                                    {{ $boat->status }}
                                </span>
                            </td>

                            {{-- Image (No changes needed here) --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img src="{{ asset('boats/' . ($boat->image ?? 'placeholder.jpg')) }}" alt="Boat Image"
                                    class="w-12 h-12 object-cover  shadow-sm">
                            </td>

                            {{-- Actions (No changes needed to button icons/wrappers) --}}
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <div class="flex items-center space-x-1">

                                    <a href="{{ url('update_boat/' . $boat->id) }}"
                                        class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors"
                                        title="Edit Boat">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </a>

                                    <a href="{{ url('delete_boat/' . $boat->id) }}"
                                        onclick="return confirm('Are you sure you want to delete this boat?');"
                                        class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors"
                                        title="Delete Boat">
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

                        {{-- Description Row --}}
                        <tr x-show="open" x-transition.opacity.duration.200ms x-cloak class="bg-gray-50 dark:bg-gray-900">
                            <td colspan="6" class="px-6 py-4">
                                {{-- Changed text-sm font-semibold to text-xs font-normal --}}
                                <h4 class="text-xs font-normal text-gray-700 dark:text-gray-200 mb-3">
                                    Boat Description
                                </h4>
                                {{-- Kept text-xs but made description text lighter --}}
                                <p
                                    class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed text-justify max-w-3xl ">
                                    {!! nl2br(e(strip_tags($boat->description))) !!}
                                </p>
                            </td>
                        </tr>


                    </tbody>
                @endforeach

            </table>
        </div>
    </div>

@endsection