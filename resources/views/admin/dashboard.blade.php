@extends('admin.layouts.app')
@section('content')

  <div class="p-4 sm:p-6 space-y-6">

    {{-- 1. Dashboard Title (CLEAN HEADER) --}}
    <div class="flex items-center justify-between">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Dashboard</h1>
    </div>

    {{-- ⭐️ Typhoon status component (replaces iframe) --}}
    @include('admin.partials.typhoon-card')


    {{-- DATE VARIABLES (KEPT AS IS) --}}
    @php
      use Carbon\Carbon;
      $today = Carbon::today();
      $weekStart = (clone $today)->startOfWeek();
      $weekEnd = (clone $weekStart)->endOfWeek();
      $monthStart = (clone $today)->startOfMonth();
      $monthEnd = (clone $today)->endOfMonth();
      $quarter = (int) ceil($today->month / 3);
      $quarterStartMonth = ($quarter - 1) * 3 + 1;
      $quarterStart = Carbon::create($today->year, $quarterStartMonth, 1)->startOfMonth();
      $quarterEnd = (clone $quarterStart)->addMonths(3)->subDay();
      $yearStart = (clone $today)->startOfYear();
      $yearEnd = (clone $today)->endOfYear();
    @endphp

    {{-- Calculate deposit/refund percentages and total revenue from paid bookings --}}
    @php
      // Read live settings (frontdesk can update these via SettingsController)
      $depositPercent = (float) \App\Models\Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
      $refundPercent = (float) \App\Models\Setting::get('refund_fee_percentage', 5);

      // Use a bookings collection passed from controller if available, otherwise fall back to $datas
      $bookingsCollection = isset($bookings) ? $bookings : (isset($datas) ? $datas : collect());

      $calculatedRevenue = 0.0; // sum of deposit amounts received for paid bookings
      $calculatedPaidCount = 0;
      foreach ($bookingsCollection as $b) {
        $amt = (float) ($b->total_amount ?? 0);
        if (isset($b->payment_status) && $b->payment_status === 'paid') {
          $depAmt = $amt * ($depositPercent / 100);
          $calculatedRevenue += $depAmt;
          $calculatedPaidCount++;
        }
      }
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
      <div class="bg-white dark:bg-black p-5 sm:p-6  shadow-lg flex justify-between items-center">
        <div>
          <div class="text-sm font-medium text-gray-500 dark:text-gray-300">Total Bookings</div>
          <div class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-1">
            {{ $totalBookings }}
          </div>
          <div class="text-xs text-green-500 mt-1">
            Rooms: {{ $totalRoomBookings }} | Boats: {{ $totalBoatBookings }}
          </div>
        </div>
        <div class="p-3  bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
              <path
                d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2m0 16H5V9h14zM5 7V5h14v2zm5.56 10.46l5.93-5.93-1.06-1.06-4.87 4.87-2.11-2.11-1.06 1.06z" />
            </g>
          </svg>
        </div>
      </div>
      <div class="bg-white dark:bg-black p-5 sm:p-6  shadow-lg flex justify-between items-center">
        <div>
          <div class="text-sm font-medium text-gray-500 dark:text-gray-300">Total Revenue</div>
          <div class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-1">
            ₱{{ number_format($calculatedRevenue, 2) }}
          </div>
          @if(isset($revenueGrowth))
            <div class="text-xs {{ $revenueGrowth >= 0 ? 'text-green-500' : 'text-red-500' }} mt-1">
              {{ $revenueGrowth >= 0 ? '+' : '' }}{{ number_format($revenueGrowth, 1) }}% vs last month
            </div>
          @endif
        </div>
        <div class="p-3  bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
              <path
                d="M5 2v4H3v2h2v2H3v2h2v10h2v-6h6c2.7 0 5.16-1.56 6.32-4H22v-2h-2.08c.11-.66.11-1.34 0-2H22V6h-2.68C18.16 3.56 15.7 2 13 2zM7 4h6c1.57 0 3.06.74 4 2H7zm6 10H7v-2h10c-.94 1.26-2.43 2-4 2zm5-5c0 .34-.04.67-.1 1H7V8h10.9c.06.33.1.66.1 1z" />
            </g>
          </svg>

        </div>
      </div>



      <div class="bg-white dark:bg-black p-5 sm:p-6  shadow-lg flex justify-between items-center">
        <div>
          <div class="text-sm font-medium text-gray-500 dark:text-gray-300">New Guest</div>
          <div class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-1">
            {{ $currentCustomers }}
          </div>
          <div class="text-xs mt-1 {{ $customerGrowth >= 0 ? 'text-green-500' : 'text-red-500' }}">
            {{ $customerGrowth >= 0 ? '+' : '' }}{{ number_format($customerGrowth, 1) }}% vs last month
          </div>
        </div>
        <div class="p-3  bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
          <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
      </div>

      <div class="bg-white dark:bg-black p-5 sm:p-6  shadow-lg flex justify-between items-center">
        <div>
          <div class="text-sm font-medium text-gray-500 dark:text-gray-300">Occupancy Rate</div>
          <div class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-1">
            {{ number_format($occupancyRate, 1) }}%
          </div>
          <div class="text-xs mt-1 {{ $occupancyGrowth >= 0 ? 'text-green-500' : 'text-red-500' }}">
            {{ $occupancyGrowth >= 0 ? '+' : '' }}{{ number_format($occupancyGrowth, 1) }}% vs last month
          </div>
        </div>
        <div class="p-3  bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <g fill="currentColor" fill-rule="evenodd" clip-rule="evenodd">
              <path
                d="M6 14h3.05l5-5q.225-.225.338-.513t.112-.562t-.125-.537t-.325-.488l-.9-.95q-.225-.225-.5-.337t-.575-.113q-.275 0-.562.113T11 5.95l-5 5zm7-6.075L12.075 7zM7.5 12.5v-.95l2.525-2.525l.5.45l.45.5L8.45 12.5zm3.025-3.025l.45.5l-.95-.95zm.65 4.525H18v-2h-4.825zM2 22V4q0-.825.588-1.412T4 2h16q.825 0 1.413.588T22 4v12q0 .825-.587 1.413T20 18H6zm3.15-6H20V4H4v13.125zM4 16V4z" />
            </g>
          </svg>

        </div>
      </div>
    </div>

    {{-- 2. New Action Bar: Export Controls (MOVED HERE) --}}
    <div
      class="bg-white dark:bg-black p-4  shadow-lg border dark:border-black flex items-center justify-end">
      <div class="hidden md:flex items-center gap-4">

        {{-- 1. Primary Button (Download All) --}}
        <a href="{{ route('admin.export.sales') }}"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white text-sm font-medium  transition duration-150 ease-in-out">
          Download All Sales (.xlsx)
        </a>

        {{-- 2. Filtered Exports Group --}}
        <div class="flex items-center gap-2 border-l border-gray-300 dark:border-black pl-4">
          {{-- Preset Buttons --}}
          <a href="{{ route('admin.export.sales', ['from' => $weekStart->toDateString(), 'to' => $weekEnd->toDateString()]) }}"
            class="px-3 py-2 bg-gray-100 dark:bg-black text-sm text-gray-700 dark:text-white rounded hover:bg-gray-200 dark:hover:bg-gray-900 transition">This
            Week</a>
          <a href="{{ route('admin.export.sales', ['from' => $monthStart->toDateString(), 'to' => $monthEnd->toDateString()]) }}"
            class="px-3 py-2 bg-gray-100 dark:bg-black text-sm text-gray-700 dark:text-white rounded hover:bg-gray-200 dark:hover:bg-gray-900 transition">This
            Month</a>
          <a href="{{ route('admin.export.sales', ['from' => $quarterStart->toDateString(), 'to' => $quarterEnd->toDateString()]) }}"
            class="px-3 py-2 bg-gray-100 dark:bg-black text-sm text-gray-700 dark:text-white rounded hover:bg-gray-200 dark:hover:bg-gray-900 transition">This
            Quarter</a>
          <a href="{{ route('admin.export.sales', ['from' => $yearStart->toDateString(), 'to' => $yearEnd->toDateString()]) }}"
            class="px-3 py-2 bg-gray-100 dark:bg-black text-sm text-gray-700 dark:text-white rounded hover:bg-gray-200 dark:hover:bg-gray-900 transition">This
            Year</a>

          {{-- Custom Form --}}
          <form method="GET" action="{{ route('admin.export.sales') }}" class="ml-2 inline-flex items-center gap-2">
            <input type="date" name="from"
              class="border border-gray-300 dark:border-black rounded px-2 py-1 text-sm max-w-[11rem] bg-white dark:bg-black text-gray-800 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
              required>
            <input type="date" name="to"
              class="border border-gray-300 dark:border-black rounded px-2 py-1 text-sm max-w-[11rem] bg-white dark:bg-black text-gray-800 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
              required>
            <button type="submit"
              class="px-3 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white rounded text-sm transition duration-150 ease-in-out">Export</button>
          </form>
        </div>
      </div>

      <details class="md:hidden relative group">
        <summary
          class="px-3 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded inline-flex items-center gap-2 cursor-pointer
                                    group-open:bg-indigo-700 dark:group-open:bg-indigo-600 hover:bg-indigo-700 dark:hover:bg-indigo-600 transition duration-150 ease-in-out">
          Export Sales
        </summary>
        <div
          class="absolute right-0 z-10 mt-2 p-3 bg-white dark:bg-black border border-gray-200 dark:border-black rounded shadow-lg w-64 text-gray-800 dark:text-white">
          <div class="flex flex-col gap-2">
            {{-- All Sales Button --}}
            <a href="{{ route('admin.export.sales') }}"
              class="px-3 py-2 bg-indigo-50 dark:bg-black rounded text-sm text-center font-medium text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-gray-900">
              Download ALL Sales (.xlsx)</a>

            <div class="h-px bg-gray-200 dark:bg-black my-0"></div>

            {{-- Preset Buttons --}}
            <a href="{{ route('admin.export.sales', ['from' => $weekStart->toDateString(), 'to' => $weekEnd->toDateString()]) }}"
              class="px-3 py-2 bg-gray-50 dark:bg-black rounded text-sm text-center hover:bg-gray-100 dark:hover:bg-gray-900">This
              Week</a>
            <a href="{{ route('admin.export.sales', ['from' => $monthStart->toDateString(), 'to' => $monthEnd->toDateString()]) }}"
              class="px-3 py-2 bg-gray-50 dark:bg-black rounded text-sm text-center hover:bg-gray-100 dark:hover:bg-gray-900">This
              Month</a>
            <a href="{{ route('admin.export.sales', ['from' => $quarterStart->toDateString(), 'to' => $quarterEnd->toDateString()]) }}"
              class="px-3 py-2 bg-gray-50 dark:bg-black rounded text-sm text-center hover:bg-gray-100 dark:hover:bg-gray-900">This
              Quarter</a>
            <a href="{{ route('admin.export.sales', ['from' => $yearStart->toDateString(), 'to' => $yearEnd->toDateString()]) }}"
              class="px-3 py-2 bg-gray-50 dark:bg-black rounded text-sm text-center hover:bg-gray-100 dark:hover:bg-gray-900">This
              Year</a>

            {{-- Custom Form --}}
            <form method="GET" action="{{ route('admin.export.sales') }}"
              class="mt-2 flex flex-col gap-2 border-t border-gray-200 dark:border-black pt-2">
              <p class="text-xs font-semibold text-gray-500 dark:text-gray-300">Custom Range:</p>
              <input type="date" name="from"
                class="border border-gray-300 dark:border-black rounded px-2 py-1 text-sm bg-white dark:bg-black text-gray-800 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                required>
              <input type="date" name="to"
                class="border border-gray-300 dark:border-black rounded px-2 py-1 text-sm bg-white dark:bg-black text-gray-800 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                required>
              <button type="submit"
                class="px-3 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white rounded text-sm transition duration-150 ease-in-out">Export
                Range</button>
            </form>
          </div>
        </div>
      </details>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
      {{-- Inline dataset bootstrap for admin dashboard JS (ensures data is available to static JS files) --}}
      <script>
        window.dashboardLabels = @json($labels ?? []);
        window.roomChartData = @json($roomChartData ?? []);
        window.boatChartData = @json($boatChartData ?? []);
        window.adminRecentBookingsEndpoint = @json(route('admin.api.recent_bookings'));
      </script>
      <div class="lg:col-span-2 bg-white dark:bg-black p-6  shadow-lg">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
          Bookings Overview (Last 30 Days)
        </h2>
        <div class="h-80">
          <canvas id="bookingsChart"></canvas>
        </div>
      </div>


      <div class="lg:col-span-1 bg-white dark:bg-black p-6  shadow-lg">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Recent Bookings</h2>
        <div class="space-y-5">
          @foreach ($datas as $data)
            <div id="recent-booking-{{ $data->id }}" class="flex items-center space-x-3">
              @php
                // Use the guest's uploaded image if it exists, else generate placeholder with their first initial
                $initial = strtoupper(substr($data->name, 0, 1));
                $imageUrl = !empty($data->profile_image)
                  ? asset('profile/' . $data->profile_image)
                  : "https://placehold.co/40x40/EC4899/FFFFFF?text={$initial}";
              @endphp

              <img class="h-10 w-10  object-cover" src="{{ $imageUrl }}"
                onerror="this.src='https://placehold.co/40x40/EC4899/FFFFFF?text={{ $initial }}'" alt="{{ $data->name }}">
              <div class="flex-1">
                <div class="font-medium text-sm text-gray-800 dark:text-white">{{ $data->name }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-300">{{ optional($data->room)->room_name }}</div>
              </div>
              @php
                $depositAmount = ($data->total_amount ?? 0) * ($depositPercent / 100);

              @endphp
              <div class="flex items-center space-x-2">
                <div
                  class="text-sm font-medium {{ $data->payment_status == 'paid' ? 'text-green-500' : 'text-gray-700 dark:text-gray-200' }}">
                  ₱{{ number_format($depositAmount, 2) }}</div>

                @if($data->payment_status == 'paid')
                  <span
                    class="text-xs inline-flex items-center px-2 py-0.5  bg-green-100 text-green-800">Paid</span>
                @elseif($data->payment_status == 'pending')
                  <span
                    class="text-xs inline-flex items-center px-2 py-0.5  bg-yellow-100 text-yellow-800">Pending</span>
                @elseif($data->payment_status == 'failed')
                  <span
                    class="text-xs inline-flex items-center px-2 py-0.5  bg-red-100 text-red-800">Failed</span>
                @endif
              </div>
            </div>
          @endforeach

        </div>
      </div>

    </div>

  </div>
  <div class="p-4 sm:p-6 space-y-6">
    <div class="bg-white dark:bg-black  shadow-lg overflow-hidden">
      <h2 class="text-lg font-semibold text-gray-800 dark:text-white p-6 border-b dark:border-black">
        Latest Reservations
      </h2>

      <div class="overflow-x-auto">
        <table class="w-full min-w-max divide-y divide-gray-200 dark:divide-black">
          <thead class="bg-gray-50 dark:bg-black">
            <tr>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Guest</th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Booking Dates</th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Room</th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Payment</th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status</th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions</th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Details</span>
              </th>
            </tr>
          </thead>

          @foreach ($datas as $data)
            <tbody id="booking-{{ $data->id }}" x-data="{ open: false }"
              class="bg-white dark:bg-black divide-y divide-gray-200 dark:divide-black">
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      @php
                        // Use the guest's uploaded image if it exists, else generate placeholder with their first initial
                        $initial = strtoupper(substr($data->name, 0, 1));
                        $imageUrl = !empty($data->profile_image)
                          ? asset('profile/' . $data->profile_image)
                          : "https://placehold.co/40x40/EC4899/FFFFFF?text={$initial}";
                      @endphp

                      <img class="h-10 w-10  object-cover" src="{{ $imageUrl }}"
                        onerror="this.src='https://placehold.co/40x40/EC4899/FFFFFF?text={{ $initial }}'"
                        alt="{{ $data->name }}">
                    </div>

                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $data->name }}</div>
                      <div class="text-sm text-gray-500 dark:text-gray-300">{{ $data->email }}</div>
                    </div>
                  </div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  <div><span class="font-medium">From:</span> {{ $data->start_date }}</div>
                  <div><span class="font-medium">To:</span> {{ $data->end_date }}</div>
                  <div class="text-xs text-gray-500">{{ $data->nights }} nights</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                  <div class="max-w-[180px] line-clamp-2 overflow-hidden text-ellipsis">
                    {{ optional($data->room)->room_name }}
                  </div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                  @php
                    $depositAmount = ($data->total_amount ?? 0) * ($depositPercent / 100);
                    $remaining = ($data->total_amount ?? 0) - $depositAmount;
                    $refundAmount = $depositAmount * ($refundPercent / 100);
                  @endphp

                  <div class="text-sm text-gray-900 dark:text-white">Total: ₱{{ number_format($data->total_amount, 2) }}
                  </div>
                  @if(!empty($data->promo_label))
                    <div class="mt-1"><span
                        class="px-2 inline-flex text-xs leading-5 font-semibold  bg-blue-100 text-blue-800">Promo:
                        {{ $data->promo_label }}</span></div>
                  @endif
                  @if($data->payment_status == 'paid')
                    <div class="text-sm text-green-700">Paid (Deposit {{ $depositPercent }}%):
                      ₱{{ number_format($depositAmount, 2) }}</div>
                    <div class="text-sm text-gray-600">Remaining: ₱{{ number_format($remaining, 2) }}</div>
                    <div class="text-xs text-gray-500">Potential Refund ({{ $refundPercent }}% of deposit):
                      ₱{{ number_format($refundAmount, 2) }}</div>
                    <span
                      class="px-2 inline-flex text-xs leading-5 font-semibold  bg-green-100 text-green-800">Paid</span>
                  @elseif($data->payment_status == 'pending')
                    <span
                      class="px-2 inline-flex text-xs leading-5 font-semibold  bg-yellow-100 text-yellow-800">Pending</span>
                  @elseif($data->payment_status == 'failed')
                    <span
                      class="px-2 inline-flex text-xs leading-5 font-semibold  bg-red-100 text-red-800">Failed</span>
                  @else
                    <div class="text-sm text-gray-900">Deposit Due: ₱{{ number_format($depositAmount, 2) }}</div>
                    <div class="text-xs text-gray-500">Potential Refund ({{ $refundPercent }}% of deposit):
                      ₱{{ number_format($refundAmount, 2) }}</div>
                    <span
                      class="px-2 inline-flex text-xs leading-5 font-semibold  bg-gray-100 text-gray-800">Unpaid</span>
                  @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  @if(in_array($data->status, ['approve', 'confirmed']))
                    <span
                      class="px-3 py-1 inline-flex text-xs leading-5 font-semibold  bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Confirmed</span>
                  @elseif($data->status == 'rejected')
                    <span
                      class="px-3 py-1 inline-flex text-xs leading-5 font-semibold  bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Rejected</span>
                  @else
                    <span
                      class="px-3 py-1 inline-flex text-xs leading-5 font-semibold  bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Waiting</span>
                  @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                  <button @click="open = !open"
                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200">
                    <span x-show="!open" class="material-symbols-outlined text-xl">add_circle</span>
                    <span x-show="open" x-cloak class="material-symbols-outlined text-xl">remove_circle</span>
                  </button>
                </td>
                <td class="relative px-6 py-4">
                  <span class="sr-only">Details</span>
                </td>
              </tr>

              <tr x-show="open" x-cloak class="bg-gray-50 dark:bg-black">
                <td colspan="7" class="px-6 py-4">
                  <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Booking Details</h4>
                  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                      <dl>
                        <dt class="font-medium text-gray-900 dark:text-white">Phone:</dt>
                        <dd class="text-gray-600 dark:text-gray-300">{{ $data->phone }}</dd>
                        <dt class="font-medium text-gray-900 dark:text-white mt-2">Adults:</dt>
                        <dd class="text-gray-600 dark:text-gray-300">{{ $data->adults }}</dd>
                        <dt class="font-medium text-gray-900 dark:text-white mt-2">Children:</dt>
                        <dd class="text-gray-600 dark:text-gray-300">{{ $data->children }}</dd>
                      </dl>
                    </div>

                    <div>
                      <dl>
                        <dt class="font-medium text-gray-900 dark:text-white">Room ID:</dt>
                        <dd class="text-gray-600 dark:text-gray-300">{{ $data->room_id }}</dd>
                        <dt class="font-medium text-gray-900 dark:text-white mt-2">Paid At:</dt>
                        <dd class="text-gray-600 dark:text-gray-300">
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
                        <dt class="font-medium text-gray-900 dark:text-white">Room Price:</dt>
                        <dd class="text-gray-600 dark:text-gray-300">₱{{ number_format(optional($data->room)->price, 2) }} /
                          night</dd>
                        <dt class="font-medium text-gray-900 dark:text-white mt-2">Accommodates:</dt>
                        <dd class="text-gray-600 dark:text-gray-300">{{ optional($data->room)->accommodates }}</dd>
                        <dt class="font-medium text-gray-900 dark:text-white mt-2">Room Image:</dt>
                        <dd>
                          @if($data->room && $data->room->image)
                            <img class="h-16 w-16 object-cover  mt-1"
                              src="{{ asset('room/' . $data->room->image) }}" alt="Room">
                          @else
                            <span class="text-gray-500">-</span>
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
  </div>


@endsection

@push('admin-scripts')
  <script src="/js/admin.js"></script>
  <script src="/js/admin-map.js"></script>
  <script src="/js/admin-dashboard.js"></script>
@endpush
