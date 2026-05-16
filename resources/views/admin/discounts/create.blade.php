@extends('admin.layouts.app')

@section('content')

  <div class="p-4 sm:p-6 space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Create Discount</h1>
      <a href="{{ route('admin.discounts.index') }}" class="text-sm text-gray-600">Back to list</a>
    </div>

    @if($errors->any())
      <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
        <ul class="text-sm text-red-700">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="bg-white dark:bg-black  shadow-lg p-6">
      <form action="{{ route('admin.discounts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full  border-gray-200 p-2 bg-white dark:bg-black" required>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Slug (optional)</label>
            <input type="text" name="slug" value="{{ old('slug') }}" class="w-full  border-gray-200 p-2 bg-white dark:bg-black">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Type</label>
            <select name="type" class="w-full  border-gray-200 p-2 bg-white dark:bg-black">
              <option value="seasonal">Seasonal</option>
              <option value="special">Special</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Amount</label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount', 0) }}" class="w-full  border-gray-200 p-2 bg-white dark:bg-black" required>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Amount Type</label>
            <select name="amount_type" class="w-full  border-gray-200 p-2 bg-white dark:bg-black">
              <option value="percent">Percent</option>
              <option value="fixed">Fixed</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Start Date</label>
            <input type="text" name="start_date" id="discount_start_date" placeholder="Select Date" value="{{ old('start_date') }}" class="w-full  border-gray-200 p-2 bg-white dark:bg-black">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">End Date</label>
            <input type="text" name="end_date" id="discount_end_date" placeholder="Select Date" value="{{ old('end_date') }}" class="w-full  border-gray-200 p-2 bg-white dark:bg-black">
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Assign to Rooms</label>
            <select name="rooms[]" multiple class="w-full  border-gray-200 p-2 bg-white dark:bg-black" size="6">
              @foreach($rooms as $r)
                <option value="{{ $r->id }}">{{ $r->room_name }}</option>
              @endforeach
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Promo Images (multiple)</label>
            <input type="file" name="images[]" multiple accept="image/*" class="w-full">
            <p class="text-xs text-gray-500 mt-1">Upload images to show in carousel on the site. Files are stored in <code>public/images/promotions/</code>.</p>
          </div>

          <div>
            <label class="inline-flex items-center">
              {{-- hidden fallback so unchecked boxes submit 0 and satisfy boolean validation --}}
              <input type="hidden" name="combinable" value="0">
              <input type="checkbox" name="combinable" value="1" {{ old('combinable') ? 'checked' : '' }} class="mr-2"> Combinable with other discounts
            </label>
          </div>

          <div>
            <label class="inline-flex items-center">
              {{-- hidden fallback so unchecked boxes submit 0 and satisfy boolean validation --}}
              <input type="hidden" name="active" value="0">
              <input type="checkbox" name="active" value="1" {{ old('active', 1) ? 'checked' : '' }} class="mr-2"> Active
            </label>
          </div>

        </div>

        <div class="pt-4">
          <button class="px-4 py-2 bg-indigo-600 text-white ">Create Discount</button>
          <a href="{{ route('admin.discounts.index') }}" class="ml-2 text-gray-600">Cancel</a>
        </div>
      </form>
    </div>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#discount_start_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "M j, Y"
        });
        flatpickr("#discount_end_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "M j, Y"
        });
    });
  </script>
@endsection
