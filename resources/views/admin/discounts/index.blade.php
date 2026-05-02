@extends('admin.layouts.app')

@section('content')

  <div class="p-4 sm:p-6 space-y-6">

    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Promotions & Discounts</h1>
        <p class="text-sm text-gray-500 mt-1">Manage seasonal promotions, special discounts and promotional images.</p>
      </div>
      <div>
        <a href="{{ route('admin.discounts.create') }}"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium ">New
          Discount</a>
      </div>
    </div>

    @if(session('success'))
      <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <div class="text-sm text-green-700">{{ session('success') }}</div>
      </div>
    @endif

    <div class="bg-white dark:bg-black  shadow-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full min-w-max divide-y divide-gray-200 dark:divide-black">
          <thead class="bg-gray-50 dark:bg-black">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Images</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>

          <tbody class="bg-white dark:bg-black divide-y divide-gray-200 dark:divide-black">
            @foreach($discounts as $discount)
              <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $discount->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                  {{ ucfirst($discount->discount_type ?? $discount->type ?? '—') }}
                </td>
                <td class="px-6 py-4">@include('discounts._badge', ['discount' => $discount])</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $discount->start_date ?? '—' }} -
                  {{ $discount->end_date ?? '—' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $discount->images->count() ?? 0 }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  @foreach($discount->rooms as $room)
                    <div class="text-xs text-gray-500">{{ $room->room_name }}</div>
                  @endforeach
                </td>
                <td class="px-6 py-4">
                  @if($discount->active)
                    <span
                      class="px-2 inline-flex text-xs leading-5 font-semibold  bg-green-100 text-green-800">Yes</span>
                  @else
                    <span
                      class="px-2 inline-flex text-xs leading-5 font-semibold  bg-gray-100 text-gray-800">No</span>
                  @endif
                </td>
                <td class="px-6 py-4 text-sm">

                  <a href="{{ route('admin.discounts.edit', $discount->id) }}"
                    class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                      stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                  </a>

                  <form action="{{ route('admin.discounts.delete', $discount->id) }}" method="POST"
                    class="inline-block ml-1" onsubmit="return confirm('Delete this discount?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                      class="inline-flex items-center text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 p-2 hover:bg-slate-100 dark:hover:bg-slate-700  transition-colors">
                      <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4 7h16m-10 4v6m4-6v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3" />
                      </svg>
                    </button>
                  </form>

                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>

@endsection
