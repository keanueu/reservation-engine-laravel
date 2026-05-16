@extends('frontdesk.layouts.app')

@section('content')

    <div class="p-4 sm:p-6 space-y-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Room Bookings</h1>

        <div class="overflow-x-auto  shadow lg:overflow-visible"> {{-- Added lg:overflow-visible to match boat
            table --}}
            <table class="w-full min-w-max divide-y divide-gray-200 dark:divide-black">

                <thead class="bg-gray-50 dark:bg-black">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            Guest</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            Booking Dates</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            Room</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            Payment</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            Status</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            Refund</th>
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

                @foreach ($datas as $data)
                    <tbody id="booking-{{ $data->id }}" x-data="{ open: false }"
                        class="bg-white dark:bg-black divide-y divide-gray-200 dark:divide-black">

                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{-- Changed text-sm font-medium to text-xs font-normal --}}
                                <div class="text-xs font-normal text-gray-900 dark:text-white">{{ $data->name }}</div>
                                {{-- Changed text-sm (default weight) to text-xs  --}}
                                <div class="text-xs  text-gray-500 dark:text-gray-400">{{ $data->email }}</div>
                            </td>

                            {{-- Changed text-sm to text-xs, changed inner span font-medium to font-normal, changed <div>
                                text-xs to  (combined) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-xs  text-gray-900 dark:text-white">
                                    <div><span class="font-normal">From:</span> {{ $data->start_date }}</div>
                                    <div><span class="font-normal">To:</span> {{ $data->end_date }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 ">{{ $data->nights }} nights
                                    </div>
                                </td>

                                {{-- Changed text-sm (default weight) to text-xs  --}}
                                <td class="px-6 py-4 whitespace-nowrap text-xs  text-gray-900 dark:text-white">
                                    {{ optional($data->room)->room_name }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $depositPercent = (float) \App\Models\Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
                                        $depositAmount = ($data->total_amount ?? 0) * ($depositPercent / 100);
                                        $remaining = ($data->total_amount ?? 0) - $depositAmount;
                                    @endphp
                                    {{-- Changed text-sm (default weight) to text-xs  --}}
                                    <div class="text-xs  text-gray-900 dark:text-white">Total:
                                        ₱<span class="booking-total" data-booking-id="{{ $data->id }}">{{ number_format($data->total_amount, 2) }}</span></div>
                                    @if(!empty($data->promo_label))
                                        <div class="mt-1">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold  bg-blue-100 text-blue-800">Promo:
                                                {{ $data->promo_label }}</span>
                                        </div>
                                    @endif
                                    @if($data->payment_status == 'paid')
                                        {{-- Changed text-sm (default weight) to text-xs  --}}
                                        <div class="text-xs  text-green-700">Paid (Deposit {{ $depositPercent }}%):
                                            ₱<span class="booking-deposit" data-booking-id="{{ $data->id }}">{{ number_format($depositAmount, 2) }}</span></div>
                                        {{-- Changed text-sm (default weight) to text-xs  --}}
                                        <div class="text-xs  text-gray-600">Remaining: ₱<span class="booking-remaining" data-booking-id="{{ $data->id }}">{{ number_format($remaining, 2) }}</span>
                                        </div>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold  bg-green-100 text-green-800">Paid</span>
                                    @elseif($data->payment_status == 'pending')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold  bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif($data->payment_status == 'failed')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold  bg-red-100 text-red-800">Failed</span>
                                    @else
                                        {{-- Changed text-sm (default weight) to text-xs  --}}
                                        <div class="text-xs  text-gray-900">Deposit Due:
                                            ₱{{ number_format($depositAmount, 2) }}</div>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold  bg-gray-100 text-gray-800">Unpaid</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(in_array($data->status, ['approve', 'confirmed']))
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold  bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Confirmed</span>
                                    @elseif($data->status == 'rejected' || $data->status == 'cancelled')
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold  bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">{{ ucfirst($data->status) }}</span>
                                    @else
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold  bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Waiting</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    @if(!empty($data->refund_status))
                                        @if($data->refund_status === 'refunded')
                                            <div class="flex flex-col items-start space-y-1">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <svg class="mr-1 w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Refunded
                                                </span>
                                                <span class="text-sm font-bold text-gray-900">₱{{ number_format($data->refund_amount, 2) }}</span>
                                                <span class="text-[10px] text-gray-400 font-medium ">{{ $data->refunded_at ? \Carbon\Carbon::parse($data->refunded_at)->format('M d, Y') : '' }}</span>
                                            </div>
                                        @elseif($data->refund_status === 'requested')
                                            <div class="flex flex-col items-start space-y-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">
                                                    Requested
                                                </span>
                                                <div class="text-xs font-semibold text-gray-700">Amt: ₱{{ number_format($data->refund_amount, 2) }}</div>
                                                <div class="flex space-x-1">
                                                    <form method="POST" action="{{ route('admin.bookings.refund.approve', $data->id) }}">
                                                        @csrf
                                                        <button class="px-2 py-1 bg-emerald-600 text-white rounded-md text-[10px] font-bold hover:bg-emerald-700 shadow-sm transition-all">Approve</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.bookings.refund.reject', $data->id) }}">
                                                        @csrf
                                                        <button class="px-2 py-1 bg-rose-600 text-white rounded-md text-[10px] font-bold hover:bg-rose-700 shadow-sm transition-all">Reject</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold text-gray-500  tracking-wider">{{ $data->refund_status }}</span>
                                                <span class="text-xs font-medium text-gray-700">₱{{ number_format($data->refund_amount ?? 0, 2) }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-300 text-[10px]  tracking-widest font-medium">No activity</span>
                                    @endif
                                </td>

                                {{-- REDUCED PADDING: Changed px-6 py-4 to px-2 py-2 for a tighter action column --}}
                                <td class="px-2 py-2 whitespace-nowrap text-xs">
                                    <div class="flex flex-wrap items-center space-x-1 space-y-1">

                                        <div class="flex items-center space-x-1">
                                            <a href="{{ url('approve_booking/' . $data->id) }}"
                                                class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors"
                                                title="Approve">
                                                <svg class="w-5 h-5" stroke="currentColor" stroke-width="1.5" fill="none"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </a>

                                            <a href="{{ url('reject_booking/' . $data->id) }}"
                                                class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors"
                                                title="Reject">
                                                <svg class="w-5 h-5" stroke="currentColor" stroke-width="1.5" fill="none"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </a>
                                        </div>

                                        <div class="flex items-center space-x-1">
                                            <a href="{{ url('send_booking_email', $data->id) }}"
                                                class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors"
                                                title="Send Email">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M22 2L11 13M22 2L15 22l-4-9-9-4 20-7z" />
                                                </svg>
                                            </a>

                                            <a href="{{ url('delete_booking/' . $data->id) }}"
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
                                            {{-- NOTE: Removed window.confirm wrapper as per strict instructions against
                                            alerts/confirms. If this were a real application, you'd need a custom modal for
                                            confirmation. --}}
                                            <form action="{{ route('bookings.markDepositPaid', $data->id) }}" method="POST"
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
                                                <svg x-show="!open" class="h-5 w-5 absolute inset-0" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    xmlns="http://www.w3.org/2000/svg">
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
                                        
                                        <div class="flex items-center">
                                            @php
                                                $latestExt = optional($data->extensions)->sortByDesc('created_at')->first();
                                            @endphp
                                            @if($latestExt)
                                       <div id="latest-ext-{{ $data->id }}" class="latest-extension-indicator ml-3 px-3 py-1 rounded text-xs"
                                           data-extension-id="{{ $latestExt->id }}" data-booking-id="{{ $data->id }}" data-hours="{{ $latestExt->hours }}" data-price="{{ $latestExt->price }}">
                                                    @if($latestExt->status !== 'paid')
                                                        <div class="bg-yellow-50 text-yellow-800">Guest requested extension: <strong>{{ $latestExt->hours }}h</strong>
                                                            @if($latestExt->status === 'pending_payment')
                                                                · Waiting online payment
                                                            @elseif($latestExt->status === 'pending_frontdesk')
                                                                · Pay at frontdesk
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="bg-green-50 text-green-800">Extension paid: <strong>{{ $latestExt->hours }}h</strong></div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                        </tr>

                        {{-- Apply text-xs to the detail row container and adjust font weights --}}
                        <tr x-show="open" x-cloak class="bg-gray-50 dark:bg-black">
                            {{-- Increased colspan to 8 to match table columns. --}} 
                            <td colspan="8" class="px-6 py-4">
                                {{-- Changed text-sm to text-xs, font-semibold remains --}}
                                <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-200 mb-3">Booking Details</h4>
                                {{-- Changed text-sm on grid container to text-xs --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">

                                    <div>
                                        <dl>
                                            {{-- Changed font-medium to font-normal --}}
                                            <dt class="font-normal text-gray-900 dark:text-white">Phone:</dt>
                                            {{-- Added  (was default weight) --}}
                                            <dd class=" text-gray-600 dark:text-gray-300">{{ $data->phone }}</dd>
                                            {{-- Changed font-medium to font-normal --}}
                                            <dt class="font-normal text-gray-900 dark:text-white mt-2">Adults:</dt>
                                            {{-- Added  (was default weight) --}}
                                            <dd class=" text-gray-600 dark:text-gray-300">{{ $data->adults }}</dd>
                                            {{-- Changed font-medium to font-normal --}}
                                            <dt class="font-normal text-gray-900 dark:text-white mt-2">Children:</dt>
                                            {{-- Added  (was default weight) --}}
                                            <dd class=" text-gray-600 dark:text-gray-300">{{ $data->children }}</dd>
                                        </dl>
                                    </div>

                                    <div>
                                        <dl>
                                            {{-- Changed font-medium to font-normal --}}
                                            <dt class="font-normal text-gray-900 dark:text-white">Room ID:</dt>
                                            {{-- Added  (was default weight) --}}
                                            <dd class=" text-gray-600 dark:text-gray-300">{{ $data->room_id }}</dd>
                                            {{-- Changed font-medium to font-normal --}}
                                            <dt class="font-normal text-gray-900 dark:text-white mt-2">Paid At:</dt>
                                            {{-- Added  (was default weight) --}}
                                            <dd class=" text-gray-600 dark:text-gray-300">
                                                @if(!empty($data->paid_at))
                                                    {{ \Carbon\Carbon::parse($data->paid_at)->format('Y-m-d H:i:s') }}
                                                @else
                                                    -
                                                @endif
                                            </dd>
                                        </dl>
                                    </div>

                                    <div>
                                        <dl>
                                            <dt class="font-normal text-gray-900 dark:text-white">Scheduled Check-in / Check-out</dt>
                                            <dd class=" text-gray-600 dark:text-gray-300">
                                                <div>{{ optional($data->scheduled_checkin_at) ? \Carbon\Carbon::parse($data->scheduled_checkin_at)->format('M d, Y h:i A') : ($data->start_date ? \Carbon\Carbon::parse($data->start_date . ' 13:00')->format('M d, Y h:i A') : '-') }}</div>
                                                <div class="mt-1">→ {{ optional($data->scheduled_checkout_at) ? \Carbon\Carbon::parse($data->scheduled_checkout_at)->format('M d, Y h:i A') : ($data->end_date ? \Carbon\Carbon::parse($data->end_date . ' 11:00')->format('M d, Y h:i A') : '-') }}</div>
                                            </dd>

                                            <dt class="font-normal text-gray-900 dark:text-white mt-2">Actual Check-in / Check-out</dt>
                                            <dd class=" text-gray-600 dark:text-gray-300">
                                                <div>{{ $data->actual_checkin_at ? \Carbon\Carbon::parse($data->actual_checkin_at)->format('M d, Y h:i A') : '-' }}</div>
                                                <div class="mt-1">→ {{ $data->actual_checkout_at ? \Carbon\Carbon::parse($data->actual_checkout_at)->format('M d, Y h:i A') : '-' }}</div>
                                            </dd>

                                            <dt class="font-normal text-gray-900 dark:text-white mt-3">Update Actual Times</dt>
                                            <dd class=" text-gray-600 dark:text-gray-300 mt-1">
                                                <form method="POST" action="{{ route('admin.bookings.set_actual_times', $data->id) }}" class="flex flex-col space-y-2">
                                                    @csrf
                                                    <input type="text" name="actual_checkin_at" id="actual_checkin_{{ $data->id }}" class="px-2 py-1 border rounded text-xs" placeholder="Actual check-in" />
                                                    <input type="text" name="actual_checkout_at" id="actual_checkout_{{ $data->id }}" class="px-2 py-1 border rounded text-xs" placeholder="Actual check-out" />
                                                    <div class="flex space-x-2">
                                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs rounded">Save</button>
                                                        <a href="{{ url('bookings/check-in', $data->id) }}" class="px-3 py-1 bg-blue-600 text-white text-xs rounded">Set Check-in Now</a>
                                                        <a href="{{ url('bookings/check-out', $data->id) }}" class="px-3 py-1 bg-red-600 text-white text-xs rounded">Set Check-out Now</a>
                                                    </div>
                                                </form>
                                            </dd>
                                        </dl>
                                    </div>

                                    <div>
                                        <dl>
                                            {{-- Changed font-medium to font-normal --}}
                                            <dt class="font-normal text-gray-900 dark:text-white">Room Price:</dt>
                                            {{-- Added  (was default weight) --}}
                                            <dd class=" text-gray-600 dark:text-gray-300">
                                                ₱{{ number_format(optional($data->room)->price, 2) }} / night</dd>
                                            {{-- Changed font-medium to font-normal --}}
                                            <dt class="font-normal text-gray-900 dark:text-white mt-2">Accommodates:</dt>
                                            {{-- Added  (was default weight) --}}
                                            <dd class=" text-gray-600 dark:text-gray-300">
                                                {{ optional($data->room)->accommodates }}
                                            </dd>
                                            {{-- Changed font-medium to font-normal --}}
                                            <dt class="font-normal text-gray-900 dark:text-white mt-2">Room Image:</dt>
                                            <dd>
                                                @if($data->room && $data->room->image)
                                                    <img class="h-16 w-16 object-cover  mt-1"
                                                        src="{{ asset('room/' . $data->room->image) }}" alt="Room">
                                                @else
                                                    <span class="text-gray-500 ">-</span>
                                                @endif
                                            </dd>
                                        </dl>
                                    </div>

                                </div>
                                
                                {{-- Extensions list (if any) --}}
                                <div class="mt-4">
                                    <h5 class="text-xs font-semibold text-gray-700 dark:text-gray-200 mb-2">Extensions</h5>
                                    <div class="space-y-2 text-xs">
                                        @foreach(optional($data->extensions)->sortByDesc('created_at') ?? [] as $ext)
                                               <div class="flex items-center justify-between bg-white dark:bg-black p-2 rounded extension-row"
                                                         data-extension-id="{{ $ext->id }}" data-booking-id="{{ $data->id }}" data-price="{{ $ext->price }}">
                                                <div>
                                                    <div>Hours: <strong>{{ $ext->hours }}</strong></div>
                                                    <div>Price: ₱{{ number_format($ext->price,2) }}</div>
                                                    <div>Status: <span class="font-semibold ext-status">{{ $ext->status }}</span></div>
                                                    <div>Requested: {{ $ext->created_at }}</div>
                                                </div>
                                                <div class="ext-actions">
                                                    @if($ext->status === 'pending_frontdesk')
                                                        <form method="POST" action="{{ route('admin.bookings.extension.approve', $ext->id) }}">
                                                            @csrf
                                                            <button class="px-3 py-1 bg-green-600 text-white rounded text-xs">Approve (Mark Paid)</button>
                                                        </form>
                                                    @endif
                                                    @if($ext->status === 'pending_payment')
                                                        <button data-ext-id="{{ $ext->id }}" class="px-3 py-1 bg-blue-500 text-white rounded text-xs ml-2 ext-refresh-btn">Check Payment</button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                @endforeach

            </table>
        </div>
    </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fpOptions = {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            altInput: true,
            altFormat: "M j, Y h:i K"
        };
        @foreach($datas as $data)
            flatpickr("#actual_checkin_{{ $data->id }}", fpOptions);
            flatpickr("#actual_checkout_{{ $data->id }}", fpOptions);
        @endforeach
    });
  </script>
@endsection

