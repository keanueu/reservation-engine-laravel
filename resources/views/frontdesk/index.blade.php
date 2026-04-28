@extends('frontdesk.layouts.app')
@section('content')

    <div class="p-4 sm:p-6 space-y-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Dashboard</h1>
        @include('admin.partials.typhoon-card')

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">

      



            <div class="bg-white dark:bg-gray-800 p-5 sm:p-6  flex justify-between items-center">


                <div>
                    <div class="text-sm font-normal text-gray-500 dark:text-gray-400">Today's Check-Ins</div>
                    <div class="text-xl sm:text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                        {{ $todaysCheckInsCount ?? 0 }}
                    </div>
                </div>
                <div class="p-3  bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2">
                            <path stroke-dasharray="48" stroke-dashoffset="48"
                                d="M8 5v-1c0 -0.55 0.45 -1 1 -1h9c0.55 0 1 0.45 1 1v16c0 0.55 -0.45 1 -1 1h-9c-0.55 0 -1 -0.45 -1 -1v-1">
                                <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="48;0" />
                            </path>
                            <path stroke-dasharray="12" stroke-dashoffset="12" d="M4 12h11">
                                <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.7s" dur="0.2s"
                                    values="12;0" />
                            </path>
                            <path stroke-dasharray="6" stroke-dashoffset="6" d="M15 12l-3.5 -3.5M15 12l-3.5 3.5">
                                <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.9s" dur="0.2s"
                                    values="6;0" />
                            </path>
                        </g>
                    </svg>



                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 sm:p-6  flex justify-between items-center">
                <div>
                    <div class="text-sm font-normal text-gray-500 dark:text-gray-400">Today's Check-Outs</div>
                    <div class="text-xl sm:text-2xl font-bold text-red-600 dark:text-red-400 mt-1">
                        {{ $todaysCheckOutsCount ?? 0 }}
                    </div>
                </div>
                <div class="p-3  bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2">
                            <path stroke-dasharray="48" stroke-dashoffset="48"
                                d="M16 5v-1c0 -0.55 -0.45 -1 -1 -1h-9c-0.55 0 -1 0.45 -1 1v16c0 0.55 0.45 1 1 1h9c0.55 0 1 -0.45 1 -1v-1">
                                <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="48;0" />
                            </path>
                            <path stroke-dasharray="12" stroke-dashoffset="12" d="M10 12h11">
                                <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.7s" dur="0.2s"
                                    values="12;0" />
                            </path>
                            <path stroke-dasharray="6" stroke-dashoffset="6" d="M21 12l-3.5 -3.5M21 12l-3.5 3.5">
                                <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.9s" dur="0.2s"
                                    values="6;0" />
                            </path>
                        </g>
                    </svg>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 sm:p-6  flex justify-between items-center">
                <div>
                    <div class="text-sm font-normal text-gray-500 dark:text-gray-400">In-House Guests</div>
                    <div class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                        {{ $inHouseGuestsCount ?? 0 }}
                    </div>
                </div>
                <div class="p-3  bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
                            <path
                                d="M1 11C.08 11-.352 9.863.336 9.253l9-8a1 1 0 0 1 1.328 0l9 8C20.352 9.863 19.92 11 19 11h-1v7a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-7zm6 6v-5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v5h3v-7a1 1 0 0 1 .512-.873L10 3.337l-6.512 5.79A1 1 0 0 1 4 10v7zm2 0v-4h2v4z" />
                        </g>
                    </svg>

                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 sm:p-6  flex justify-between items-center">
                <div>
                    <div class="text-sm font-normal text-gray-500 dark:text-gray-400">Available Rooms</div>
                    <div class="text-xl sm:text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">
                        {{ $availableRoomsCount ?? 0 }}
                    </div>
                </div>
                <div class="p-3  bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
                            <path
                                d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2m0 16H5V9h14zM5 7V5h14v2zm5.56 10.46l5.93-5.93-1.06-1.06-4.87 4.87-2.11-2.11-1.06 1.06z" />
                        </g>
                    </svg>

                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

            <div class="lg:col-span-2 bg-white dark:bg-gray-800  overflow-hidden">
                <div x-data="{ tab: 'arrivals' }">
                    <nav class="flex border-b border-gray-200 dark:border-gray-700">
                        <button @click="tab = 'arrivals'"
                            :class="{'border-blue-500 text-blue-600 dark:text-blue-400 dark:border-blue-400': tab === 'arrivals', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': tab !== 'arrivals'}"
                            class="w-1/3 py-4 px-1 text-center border-b-2 font-normal text-sm">
                            Arrivals ({{ $todaysCheckInsCount ?? 0 }})
                        </button>
                        <button @click="tab = 'departures'"
                            :class="{'border-blue-500 text-blue-600 dark:text-blue-400 dark:border-blue-400': tab === 'departures', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': tab !== 'departures'}"
                            class="w-1/3 py-4 px-1 text-center border-b-2 font-normal text-sm">
                            Departures ({{ $todaysCheckOutsCount ?? 0 }})
                        </button>
                        <button @click="tab = 'inhouse'"
                            :class="{'border-blue-500 text-blue-600 dark:text-blue-400 dark:border-blue-400': tab === 'inhouse', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': tab !== 'inhouse'}"
                            class="w-1/3 py-4 px-1 text-center border-b-2 font-normal text-sm">
                            In-House ({{ $inHouseGuestsCount ?? 0 }})
                        </button>
                    </nav>

                    <div class="p-6">
                        <div x-show="tab === 'arrivals'">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-4">Today's Arrivals</h3>
                            <div class="space-y-4">
                                @forelse ($todaysArrivals ?? [] as $arrival)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 ">
                                        <div>
                                            <div class="font-normal text-gray-900 dark:text-white">{{ $arrival->name}}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{$arrival->room->room_name}}
                                                | {{ $arrival->adults}} Adults, {{ $arrival->children}} Children</div>
                                        </div>
                                        <a href="{{ url('bookings/check-in', $arrival->id)}}"
                                            class="px-4 py-2 bg-green-600 text-white text-sm font-normal  hover:bg-green-700">
                                            Check in
                                        </a>
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400">No arrivals scheduled for today.</p>
                                @endforelse
                            </div>
                        </div>

                        <div x-show="tab === 'departures'" x-cloak>
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-4">Today's Departures</h3>
                            <div class="space-y-4">
                                @forelse ($todaysDepartures ?? [] as $departure)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 ">
                                        <div>
                                            <div class="font-normal text-gray-900 dark:text-white">{{ $departure->name}}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">Room
                                                {{ optional($departure->room)->room_name ?? ($departure->room_name ?? 'Room') }}
                                                | Bill: ₱{{ number_format($departure->total_amount ?? 0, 2) }}
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if(isset($departure->status) && $departure->status === 'checked-in')
                                                <a href="{{ url('bookings/check-out', $departure->id) }}"
                                                    class="px-4 py-2 bg-red-600 text-white text-sm font-normal  hover:bg-red-700">Check
                                                    Out</a>
                                            @elseif(isset($departure->status) && $departure->status === 'checked-out')
                                                <a href="{{ url('delete_booking', $departure->id)}}"
                                                    onclick="return confirm('Delete this booking record? This cannot be undone.')"
                                                    class="px-4 py-2 bg-red-600 text-white dark:text-black text-sm font-normal  hover:bg-red-700 dark:hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 dark:focus:ring-offset-gray-900">Delete</a>
                                            @else
                                                <!-- Fallback: allow check-out if status unexpected -->
                                                <a href="{{ url('bookings/check-out', $departure->id)}}"
                                                    class="px-4 py-2 bg-red-600 text-white text-sm font-normal  hover:bg-red-700">Check
                                                    Out</a>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400">No departures scheduled for today.</p>
                                @endforelse
                            </div>
                        </div>

                        <div x-show="tab === 'inhouse'" x-cloak>
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-4">In-House Guests</h3>
                            <div class="space-y-4">
                                @forelse ($inHouseGuests ?? [] as $guest)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 ">
                                        <div>
                                            <div class="font-normal text-gray-900 dark:text-white">{{ $guest->name}}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ optional($guest->room)->room_name ?? 'Room' }} |
                                                {{ $guest->start_date ? \Carbon\Carbon::parse($guest->start_date)->format('M d, Y') : '-' }}
                                                -
                                                {{ $guest->end_date ? \Carbon\Carbon::parse($guest->end_date)->format('M d, Y') : '-' }}
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span
                                                class="text-sm text-gray-600 dark:text-gray-300">₱{{ number_format($guest->total_amount ?? 0, 2) }}</span>
                                            <a href="{{ url('bookings/check-out', $guest->id) }}"
                                                class="px-4 py-2 bg-blue-600 text-white text-sm font-normal  hover:bg-blue-700">Check
                                                Out</a>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400">No in-house guests currently.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800  p-6">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-4">Today's Boat Schedule</h3>
                    <div class="space-y-4">
                        @forelse ($todaysBoatTrips ?? [] as $trip)
                            <div class="flex items-center space-x-3">
                                {{-- Icon --}}
                                <div
                                    class="flex-shrink-0 p-2 bg-blue-100 dark:bg-blue-900  text-blue-600 dark:text-blue-300">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                {{-- Text --}}
                                <div class="flex-1">
                                    <div class="font-normal text-sm text-gray-800 dark:text-white">
                                        {{ $trip->boat->name }} ({{ $trip->guests }} pax)
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Guest: {{ $trip->name }}
                                    </div>
                                </div>
                                {{-- Time --}}
                                <div class="text-sm font-normal text-gray-700 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($trip->start_time)->format('g:i A') }}
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No boat trips scheduled for today.</p>
                        @endforelse
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800  p-6">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-4">Manage Inventory</h3>
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                        {{-- Button 1: Aligned with 'Available Rooms' card color --}}
                        <a href="{{ url('view_room') }}"
                            class="flex-1 px-4 py-2 text-center bg-yellow-600 text-white text-sm font-normal  hover:bg-yellow-700 dark:bg-yellow-500 dark:hover:bg-yellow-600 transition duration-150">
                            View Rooms
                        </a>
                        {{-- Button 2: Aligned with 'In-House Guests' card color (or general action blue) --}}
                        <a href="{{ url('view_boat') }}"
                            class="flex-1 px-4 py-2 text-center bg-blue-600 text-white text-sm font-normal  hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition duration-150">
                            View Boats
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="weather-app"
        class="w-full max-w-6xl xl:max-w-6xl mx-auto p-3 sm:p-4 lg:p-6 bg-white dark:bg-gray-900/30 border-0 backdrop-blur-md ">


        <!-- Main Layout: Current Day (Left) and Forecast (Right) -->
        <!-- Adjusted grid for better landscape flow on large screens -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-5">

            <!-- LEFT COLUMN (Span 2/3 on desktop) -->
            <div id="left-panel" class="lg:col-span-2 flex flex-col gap-4">

                <!-- ROW 1: Header, Main Temp, Details, and Sunrise/Sunset -->
                <div class="flex flex-col gap-4">

                    <!-- Header and Date -->
                    <div class="flex justify-between items-start">
                        <!-- Location -->
                        <div class="forecast-item p-1 flex items-center transition flex-1 min-w-0">
                            <div class="flex items-center text-gray-900 dark:text-white flex-1 min-w-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 text-gray-700 dark:text-white"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                <span id="current-city" class="truncate max-w-xs"></span>
                                <span class="ml-1 text-gray-600 dark:text-white truncate max-w-[6rem]"
                                    id="current-country"></span>

                                <!-- City search input -->
                                <div class="ml-3 flex items-center">
                                    <label id="city-input" placeholder="City">
                                        <button id="city-search"></button>
                                </div>
                            </div>
                        </div>

                        <!-- Date & Time -->
                        <div class="text-right flex-shrink-0 ml-4 whitespace-nowrap">
                            <p class="text-sm  text-gray-700 dark:text-white" id="current-date">Wednesday, Aug
                                27
                            </p>
                            <p class="text-xs text-gray-700 dark:text-white" id="current-time">11:32 AM</p>
                        </div>
                    </div>

                    <!-- Main Content Block (Temp, Icon, Details, Sunrise/Sunset) -->
                    <!-- Used a sub-grid here to arrange elements horizontally -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">

                        <!-- Col 1: Main Temp Block -->
                        <div
                            class="xl:col-span-1 flex flex-row items-center justify-between xl:flex-col xl:items-start p-3 sm:p-4 bg-white/3 dark:bg-gray-800/40 border border-white/8 dark:border-gray-800/40 ">
                            <!-- Temperature -->
                            <div class="text-center xl:text-left">
                                <p class="text-3xl sm:text-4xl text-gray-900 dark:text-white  relative">
                                    <span id="current-temp">17</span>
                                    <span
                                        class="absolute top-0 right-[-1.5rem] xl:static xl:ml-1 xl:text-2xl font-normal">°C</span>
                                </p>
                                <p class="text-sm sm:text-base  text-gray-600 dark:text-white/80 mt-1"
                                    id="current-description">Overcast
                                    Clouds</p>
                                <p class="text-xs text-gray-500 dark:text-white/60 mt-1">H: <span id="high-temp">17°</span>
                                    L: <span id="low-temp">17°</span></p>
                            </div>

                            <!-- Weather Icon -->
                            <div class="flex-shrink-0">
                                <!-- Placeholder SVG for Sun -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-orange-400"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="4" fill="currentColor" />
                                    <path
                                        d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                                </svg>
                            </div>
                        </div>

                        <!-- Col 2 & 3: Current Details Grid (Spans 2 columns on XL screens) -->
                        <div id="details-grid-container"
                            class="md:col-span-2 xl:col-span-2 grid grid-cols-2 sm:grid-cols-3 gap-2">
                            <!-- Detail Card Component - Adjusted padding to p-3 -->
                            <div id="visibility"
                                class="p-2 sm:p-3 bg-white/3 dark:bg-gray-800/40 border border-white/8 dark:border-gray-800/40 transition hover:bg-white/10 dark:hover:bg-gray-700/20 ">
                                <div class="flex items-center text-xs  text-gray-600 dark:text-white/80 mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M15 12c0 1.66-1.34 3-3 3s-3-1.34-3-3 1.34-3 3-3 3 1.34 3 3z" />
                                        <path d="M12 5.5s-6 7.5-12 7.5c6 0 12 7.5 12 7.5S18 13 24 13c-6 0-12-7.5-12-7.5z" />
                                    </svg>
                                    Visibility
                                </div>
                                <p class="text-base sm:text-sm text-gray-900 dark:text-white font-semibold"
                                    id="visibility-value">10.0 <span
                                        class="text-gray-600 dark:text-white ">km</span></p>
                            </div>

                            <div id="wind-speed"
                                class="p-2 sm:p-3 bg-white/3 dark:bg-gray-800/40 border border-white/8 dark:border-gray-800/40 transition hover:bg-white/10 dark:hover:bg-gray-700/20 ">
                                <div class="flex items-center text-xs  text-gray-600 dark:text-white/80 mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M17.65 14.83a8 8 0 1 0-13.44 0" />
                                        <path d="M6 14h.01M18 14h.01" />
                                        <path d="M12 18v2" />
                                    </svg>
                                    Wind Speed
                                </div>
                                <p class="text-base sm:text-sm text-gray-900 dark:text-white font-semibold"
                                    id="wind-speed-value">3.3 <span
                                        class="text-gray-600 dark:text-white ">m/s</span></p>
                            </div>

                            <div id="humidity"
                                class="p-2 sm:p-3 bg-white/3 dark:bg-gray-800/40 border border-white/8 dark:border-gray-800/40 transition hover:bg-white/10 dark:hover:bg-gray-700/20 ">
                                <div class="flex items-center text-xs  text-gray-600 dark:text-white/80 mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 22s-6-5-6-10a6 6 0 0 1 12 0c0 5-6 10-6 10z" />
                                        <path d="M12 10V6" />
                                    </svg>
                                    Humidity
                                </div>
                                <p class="text-base sm:text-sm text-gray-900 dark:text-white font-semibold"
                                    id="humidity-value">61<span class="text-gray-600 dark:text-white ">%</span>
                                </p>
                            </div>

                            <div id="pressure"
                                class="p-2 sm:p-3 bg-white/3 dark:bg-gray-800/40 border border-white/8 dark:border-gray-800/40 transition hover:bg-white/10 dark:hover:bg-gray-700/20 ">
                                <div class="flex items-center text-xs  text-gray-600 dark:text-white/80 mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M3 12a9 9 0 0 1 18 0" />
                                        <path d="M12 3a9 9 0 0 0 0 18" />
                                    </svg>
                                    Pressure
                                </div>
                                <p class="text-base sm:text-sm text-gray-900 dark:text-white font-semibold"
                                    id="pressure-value">1017 <span
                                        class="text-gray-600 dark:text-white ">hPa</span></p>
                            </div>

                            <div id="feels-like"
                                class="p-2 sm:p-3 bg-white/3 dark:bg-gray-800/40 border border-white/8 dark:border-gray-800/40 transition hover:bg-white/10 dark:hover:bg-gray-700/20 ">
                                <div class="flex items-center text-xs  text-gray-600 dark:text-white/80 mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="5" />
                                        <path
                                            d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                                    </svg>
                                    Feels Like
                                </div>
                                <p class="text-base sm:text-sm text-gray-900 dark:text-white font-semibold"
                                    id="feels-like-value">17<span class="text-gray-600 dark:text-white ">°C</span>
                                </p>
                            </div>
                        </div>

                        <!-- Col 4: Sunrise/Sunset (Spans 1 column on XL screens) -->
                        <div class="xl:col-span-1 grid grid-cols-2 xl:grid-cols-1 gap-2">
                            <!-- Sunrise -->
                            <div id="sunrise-card"
                                class="p-2 sm:p-3 text-gray-900 dark:text-white bg-orange-300 dark:bg-[#964B00] border border-orange-300 dark:border-orange-400 transition hover:bg-orange-400 dark:hover:bg-[#7a3c00] ">
                                <div class="flex items-center text-sm  mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 5.5s-6 7.5-12 7.5c6 0 12 7.5 12 7.5S18 13 24 13c-6 0-12-7.5-12-7.5z" />
                                        <path d="M12 16v-1" />
                                        <path d="M18 13h1" />
                                        <path d="M5 13H4" />
                                        <path d="M18 10h1M5 10H4M16.5 8.5l.7.7M7.5 8.5l-.7-.7M12 6.5V5" />
                                    </svg>
                                    Sunrise
                                </div>
                                <p class="text-sm font-normal" id="sunrise-time">03:18 <span
                                        class="text-xs font-normal">PM</span></p>
                            </div>
                            <!-- Sunset -->
                            <div id="sunset-card"
                                class="p-2 sm:p-3 text-gray-900 dark:text-white bg-gray-100 dark:bg-black border border-gray-200 dark:border-black transition hover:bg-gray-200 dark:hover:bg-black ">
                                <div class="flex items-center text-sm  mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 18s-6-5-6-10c0-5 6-10 6-10s6 5 6 10c0 5-6 10-6 10z" />
                                        <path d="M12 16v-1" />
                                        <path d="M18 13h1" />
                                        <path d="M5 13H4" />
                                        <path d="M18 10h1M5 10H4M16.5 8.5l.7.7M7.5 8.5l-.7-.7M12 6.5V5" />
                                    </svg>
                                    Sunset
                                </div>
                                <p class="text-sm font-normal" id="sunset-time">04:36 <span
                                        class="text-xs font-normal">AM</span></p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">

                    <div id="monthly-forecast-panel"
                        class="p-2 sm:p-3 bg-white/5 dark:bg-gray-800/40 border border-white/8 dark:border-gray-800/40 ">
                        <h3 class="text-gray-900 dark:text-white font-normal mb-3 flex items-center text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                            </svg>
                            Monthly Forecast - November 2025
                        </h3>

                        <div id="monthly-forecast-list" class="space-y-2">

                            <div
                                class="monthly-item p-2 sm:p-3 flex text-gray-900 dark:text-white justify-between items-center bg-white/50 dark:bg-gray-800/30 hover:bg-gray-100 dark:hover:bg-gray-700/20 transition ">
                                <div class="w-1/4 text-xs font-normal">Week 1 (Nov 1 - 7)</div>
                                <div class="flex items-center w-1/2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 text-orange-400"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 14.89a8 8 0 0 1 12-3.78" />
                                        <path d="M10.45 15a4 4 0 0 0-4-4H4" />
                                    </svg>
                                    <span class="text-xs text-gray-600 dark:text-white/70">Sunny & Warm</span>
                                </div>
                                <div class="w-1/4 text-right text-xs  text-gray-600 dark:text-white/60">
                                    Avg. High: 28° <span class="text-gray-500 dark:text-white/50 ml-1">Avg. Low:
                                        20°</span>
                                </div>
                            </div>

                            <div
                                class="monthly-item p-1 flex text-gray-900 dark:text-white justify-between items-center bg-white/50 hover:bg-gray-100 dark:hover:bg-gray-700/20 transition">
                                <div class="w-1/4 text-xs font-normal">Week 2 (Nov 8 - 14)</div>
                                <div class="flex items-center w-1/2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 text-sky-400"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 14.89a8 8 0 0 1 12-3.78" />
                                        <path d="M10.45 15a4 4 0 0 0-4-4H4" />
                                    </svg>
                                    <span class="text-xs text-gray-600 dark:text-white/70">Scattered Showers</span>
                                </div>
                                <div class="w-1/4 text-right text-xs  text-gray-600 dark:text-white/60">
                                    Avg. High: 25° <span class="text-gray-500 dark:text-white/50 ml-1">Avg. Low:
                                        18°</span>
                                </div>
                            </div>

                            <div
                                class="monthly-item p-1 flex text-gray-900 dark:text-white justify-between items-center bg-white/50 hover:bg-gray-100 dark:hover:bg-gray-700/20 transition">
                                <div class="w-1/4 text-xs font-normal">Week 3 (Nov 15 - 21)</div>
                                <div class="flex items-center w-1/2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 text-gray-300"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 17a5 5 0 0 1 0-10 4.5 4.5 0 0 1 4.5 4.5 4.5 4.5 0 0 0 0 9Z" />
                                    </svg>
                                    <span class="text-xs text-gray-600 dark:text-white/70">Cooler & Cloudy</span>
                                </div>
                                <div class="w-1/4 text-right text-xs  text-gray-600 dark:text-white/60">
                                    Avg. High: 22° <span class="text-gray-500 dark:text-white/50 ml-1">Avg. Low:
                                        15°</span>
                                </div>
                            </div>

                            <div
                                class="monthly-item p-1 flex text-gray-900 dark:text-white justify-between items-center bg-white/50 hover:bg-gray-100 dark:hover:bg-gray-700/20 transition">
                                <div class="w-1/4 text-xs font-normal">Week 4 (Nov 22 - 30)</div>
                                <div class="flex items-center w-1/2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 text-orange-400"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="4" fill="currentColor" />
                                        <path
                                            d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                                    </svg>
                                    <span class="text-xs text-gray-600 dark:text-white/70">Clearing Up</span>
                                </div>
                                <div class="w-1/4 text-right text-xs  text-gray-600 dark:text-white/60">
                                    Avg. High: 24° <span class="text-gray-500 dark:text-white/50 ml-1">Avg. Low:
                                        16°</span>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <!-- RIGHT COLUMN: 5 Day Forecast (1/3 width on desktop) -->
            <div id="forecast-panel"
                class="lg:col-span-1 flex flex-col gap-2 p-2 sm:p-3 bg-white dark:bg-gray-800/30 border border-gray-100 dark:border-gray-800/40 ">

                <h3 class="text-gray-900 dark:text-white font-normal mb-2 flex items-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                        <line x1="16" x2="16" y1="2" y2="6" />
                        <line x1="8" x2="8" y1="2" y2="6" />
                        <line x1="3" x2="21" y1="10" y2="10" />
                    </svg>
                    5 Day Forecast (Summary)
                </h3>

                <!-- Forecast Item Loop -->
                <div id="forecast-list" class="space-y-2">
                    <!-- Forecast Item Component -->
                    <div
                        class="forecast-item p-2 sm:p-3 flex justify-between items-center bg-white/50 dark:bg-gray-800/25 hover:bg-gray-100 dark:hover:bg-gray-700/20 transition ">
                        <div class="flex items-center text-gray-900 dark:text-white">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 mr-3 text-sky-400"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 14.89a8 8 0 0 1 12-3.78" />
                                <path d="M10.45 15a4 4 0 0 0-4-4H4" />
                            </svg>
                            <div
                                class="forecast-item p-1 flex justify-between items-center bg-white/50 hover:bg-gray-100 dark:hover:bg-gray-700/20 transition">
                                <div class="flex items-center text-gray-900 dark:text-white">
                                </div>
                            </div>
                            <p class="text-sm sm:text-base  text-gray-900 dark:text-white">
                                17° <span class="text-gray-500 dark:text-white/50 ml-1">17°</span>
                            </p>
                        </div>

                        <div
                            class="forecast-item p-1 flex justify-between items-center bg-white/50 hover:bg-gray-100 dark:hover:bg-gray-700/20 transition">
                            <div class="flex items-center text-gray-900 dark:text-white">
                                <!-- Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-3 text-sky-400"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 14.89a8 8 0 0 1 12-3.78" />
                                    <path d="M10.45 15a4 4 0 0 0-4-4H4" />
                                </svg>
                                <div>
                                    <p class="text-xs font-normal text-gray-900 dark:text-white">Thu, Aug 28</p>
                                    <p class="text-xs text-gray-600 dark:text-white/70">Broken Clouds</p>
                                </div>
                            </div>
                            <p class="text-xs  text-gray-900 dark:text-white">
                                25° <span class="text-gray-500 dark:text-white/50 ml-1">25°</span>
                            </p>
                        </div>

                        <div
                            class="forecast-item p-1 flex justify-between items-center bg-white/5 hover:bg-white/10 transition">
                            <div class="flex items-center">
                                <!-- Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-3 text-gray-300"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 17a5 5 0 0 1 0-10 4.5 4.5 0 0 1 4.5 4.5 4.5 4.5 0 0 0 0 9Z" />
                                </svg>
                                <div>
                                    <p class="text-xs font-normal text-gray-900 dark:text-white">Fri, Aug 29</p>
                                    <p class="text-xs text-gray-600 dark:text-white/70">Overcast Clouds</p>
                                </div>
                            </div>
                            <p class="text-xs  text-gray-900 dark:text-white">
                                24° <span class="text-gray-500 dark:text-white/50 ml-1">24°</span>
                            </p>
                        </div>

                        <div
                            class="forecast-item p-1 flex justify-between items-center bg-white/5 hover:bg-white/10 transition">
                            <div class="flex items-center">
                                <!-- Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-3 text-sky-400"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 14.89a8 8 0 0 1 12-3.78" />
                                    <path d="M10.45 15a4 4 0 0 0-4-4H4" />
                                </svg>
                                <div>
                                    <p class="text-xs font-normal text-gray-900 dark:text-white">Sat, Aug 30</p>
                                    <p class="text-xs text-gray-600 dark:text-white/70">Broken Clouds</p>
                                </div>
                            </div>
                            <p class="text-xs  text-gray-900 dark:text-white">
                                24° <span class="text-gray-500 dark:text-white/50 ml-1">24°</span>
                            </p>
                        </div>

                        <div
                            class="forecast-item p-1 flex justify-between items-center bg-white/50 hover:bg-gray-100 dark:hover:bg-gray-700/20 transition">
                            <div class="flex items-center text-gray-900 dark:text-white">
                                <!-- Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-3 text-orange-400"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="4" fill="currentColor" />
                                    <path
                                        d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                                </svg>
                                <div>
                                    <p class="text-xs font-normal text-gray-900 dark:text-white">Sun, Aug 31</p>
                                    <p class="text-xs text-gray-600 dark:text-white/70">Clear Sky</p>
                                </div>
                            </div>
                            <p class="text-xs  text-gray-900 dark:text-white">
                                24° <span class="text-gray-500 dark:text-white/50 ml-1">24°</span>
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection