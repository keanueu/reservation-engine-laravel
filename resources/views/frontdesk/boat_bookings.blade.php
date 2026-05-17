@extends('frontdesk.layouts.app')

@section('content')

    <div class="p-4 sm:p-6 space-y-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Boat Bookings</h1>

        {{-- REMOVED: overflow-x-auto class to prevent overflow behavior on desktop/large screens --}}
        <div class=" shadow-lg overflow-x-auto lg:overflow-visible">
            <table class="w-full min-w-max divide-y divide-gray-200 dark:divide-black">

                <thead class="bg-gray-50 dark:bg-black">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            Guest</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            Boat</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            Booking Date & Time</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            Payment</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            Status</th>
                        {{-- REDUCED PADDING: Changed px-6 to px-2 for tighter action column header --}}
                        <th scope="col"
                            class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            Actions</th>
                        {{-- REDUCED PADDING: Changed px-6 to px-2 for the final empty header --}}
                        <th scope="col" class="relative px-2 py-3">
                            <span class="sr-only">Details</span>
                        </th>
                    </tr>
                </thead>

                @foreach ($boatBookings as $booking)
                    <x-frontdesk.boat-booking-row :booking="$booking" />
                @endforeach
            </table>
        </div>
    </div>

@endsection

