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
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Guest</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Boat</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Booking Date & Time</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Payment</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status</th>
                        {{-- REDUCED PADDING: Changed px-6 to px-2 for tighter action column header --}}
                        <th scope="col"
                            class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions</th>
                        {{-- REDUCED PADDING: Changed px-6 to px-2 for the final empty header --}}
                        <th scope="col" class="relative px-2 py-3">
                            <span class="sr-only">Details</span>
                        </th>
                    </tr>
                </thead>

                @foreach ($boatBookings as $booking)
                    <tbody x-data="{ open: false }"
                        class="bg-white dark:bg-black divide-y divide-gray-200 dark:divide-black">

                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{-- Changed text-sm font-medium to text-xs font-normal --}}
                                <div class="text-xs font-normal text-gray-900 dark:text-white">{{ $booking->name }}</div>
                                {{-- Changed text-sm (default weight) to text-xs  --}}
                                <div class="text-xs  text-gray-500 dark:text-gray-400">{{ $booking->email }}</div>
                            </td>

                            {{-- Changed text-sm (default weight) to text-xs  --}}
                            <td class="px-6 py-4 whitespace-nowrap text-xs  text-gray-900 dark:text-white">
                                @php
                                    // Your safe access logic is preserved here
                                    $boatModel = (method_exists($booking, 'boat') && $booking->boat) ? $booking->boat : null;
                                @endphp
                                {{ $boatModel ? $boatModel->name : '-' }}
                            </td>

                            {{-- Changed text-sm on TD to text-xs . Changed inner span font-medium to font-normal. --}}
                            <td class="px-6 py-4 whitespace-nowrap text-xs  text-gray-900 dark:text-white">
                                <div><span class="font-normal">Date:</span> {{ $booking->booking_date }}</div>
                                <div><span class="font-normal">Time:</span> {{ $booking->start_time }} -
                                    {{ $booking->end_time }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $depositPercent = (float) config('booking.deposit_percentage', 50);
                                    $depositAmount = ($booking->total_amount ?? 0) * ($depositPercent / 100);
                                    $remaining = ($booking->total_amount ?? 0) - $depositAmount;
                                @endphp
                                {{-- Changed text-sm (default weight) to text-xs  --}}
                                <div class="text-xs  text-gray-900 dark:text-white">Total:
                                    ₱{{ number_format($booking->total_amount ?? 0, 2) }}</div>
                                @if($booking->payment_status == 'paid')
                                    {{-- Changed text-sm (default weight) to text-xs  --}}
                                    <div class="text-xs  text-green-700">Paid (Deposit {{ $depositPercent }}%):
                                        ₱{{ number_format($depositAmount, 2) }}</div>
                                    {{-- Changed text-sm (default weight) to text-xs  --}}
                                    <div class="text-xs  text-gray-600">Remaining: ₱{{ number_format($remaining, 2) }}</div>
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold  bg-green-100 text-green-800">Paid</span>
                                @elseif($booking->payment_status == 'pending')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold  bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($booking->payment_status == 'failed')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold  bg-red-100 text-red-800">Failed</span>
                                @else
                                    {{-- Changed text-sm (default weight) to text-xs  --}}
                                    <div class="text-xs  text-gray-900">Deposit Due: ₱{{ number_format($depositAmount, 2) }}</div>
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold  bg-gray-100 text-gray-800">Unpaid</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(in_array($booking->status, ['approve', 'confirmed']))
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold  bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Confirmed</span>
                                @elseif($booking->status == 'rejected')
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold  bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Rejected</span>
                                @else
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold  bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Pending</span>
                                @endif
                            </td>

                            {{-- REDUCED PADDING: Changed px-6 py-4 to px-2 py-2 for a tighter action column --}}
                            <td class="px-2 py-2 whitespace-nowrap text-xs">
                                <div class="flex flex-wrap items-center space-x-1 space-y-1">

                                    <div class="flex items-center space-x-1">
                                        <a href="{{ url('approve_boat_booking/' . $booking->id) }}"
                                            class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors"
                                            title="Approve">
                                            <svg class="w-5 h-5" stroke="currentColor" stroke-width="1.5" fill="none"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </a>

                                        <a href="{{ url('reject_boat_booking/' . $booking->id) }}"
                                            class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors"
                                            title="Reject">
                                            <svg class="w-5 h-5" stroke="currentColor" stroke-width="1.5" fill="none"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </a>
                                    </div>

                                    <div class="flex items-center space-x-1">
                                        <a href="{{ url('send_boat_booking_email', $booking->id) }}"
                                            class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors"
                                            title="Send Email">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M22 2L11 13M22 2L15 22l-4-9-9-4 20-7z" />
                                            </svg>
                                        </a>

                                        <a href="{{ url('delete_boat_booking/' . $booking->id) }}"
                                            class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors"
                                            title="Delete">
                                            <svg class="w-5 h-5" stroke="currentColor" stroke-width="1.5" fill="none"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4 7h16m-10 4v6m4-6v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3" />
                                            </svg>
                                        </a>
                                    </div>

                                    <div class="flex items-center space-x-1">
                                        {{-- NOTE: Removed window.confirm wrapper as per strict instructions against alerts/confirms. If this were a real application, you'd need a custom modal for confirmation. --}}
                                        <form action="{{ route('boat_bookings.markDepositPaid', $booking->id) }}" method="POST"
                                            class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors"
                                                title="Mark Deposit Paid">
                                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        </form>

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
                                </div>
                            </td>
                        </tr>

                        {{-- Apply text-xs to the detail row container and adjust font weights --}}
                        <tr x-show="open" x-cloak class="bg-gray-50 dark:bg-black">
                            <td colspan="7" class="px-6 py-4">
                                {{-- Changed text-sm to text-xs, font-semibold remains --}}
                                <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-200 mb-3">Additional Booking
                                    Details</h4>
                                {{-- Changed text-sm on grid container to text-xs --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">

                                    <div>
                                        <dl>
                                            {{-- Changed font-medium to font-normal --}}
                                            <dt class="font-normal text-gray-900 dark:text-white">Phone:</dt>
                                            {{-- Added  (was default weight) --}}
                                            <dd class=" text-gray-600 dark:text-gray-300">{{ $booking->phone }}</dd>
                                            {{-- Changed font-medium to font-normal --}}
                                            <dt class="font-normal text-gray-900 dark:text-white mt-2">Guests:</dt>
                                            {{-- Added  (was default weight) --}}
                                            <dd class=" text-gray-600 dark:text-gray-300">{{ $booking->guests }}</D>
                                        </dl>
                                    </div>

                                    <div>
                                        <dl>
                                            {{-- Changed font-medium to font-normal --}}
                                            <dt class="font-normal text-gray-900 dark:text-white">Booking ID:</dt>
                                            {{-- Added  (was default weight) --}}
                                            <dd class=" text-gray-600 dark:text-gray-300">{{ $booking->id }}</dd>
                                            {{-- Changed font-medium to font-normal --}}
                                            <dt class="font-normal text-gray-900 dark:text-white mt-2">Paid At:</dt>
                                            {{-- Added  (was default weight) --}}
                                            <dd class=" text-gray-600 dark:text-gray-300">
                                                @if(!empty($booking->paid_at))
                                                    {{ \Carbon\Carbon::parse($booking->paid_at)->format('Y-m-d H:i:s') }}
                                                @else
                                                    -
                                                @endif
                                            </dd>
                                        </dl>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    </tbody>
                @endforeach
            </table>
        </div>
    </div>

@endsection
