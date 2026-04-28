        @extends('admin.layouts.app')
        @section('content')

          <div class="p-4 sm:p-6 space-y-6">
            <div class="flex items-center justify-between">
              <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Edit Discount</h1>
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

            <div class="bg-white dark:bg-gray-800  shadow-lg p-6">
              <form action="{{ route('admin.discounts.update', $discount->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Name</label>
                    <input type="text" name="name" value="{{ old('name', $discount->name) }}" class="w-full  border-gray-200 p-2 bg-white dark:bg-gray-700" required>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Slug (optional)</label>
                    <input type="text" name="slug" value="{{ old('slug', $discount->slug) }}" class="w-full  border-gray-200 p-2 bg-white dark:bg-gray-700">
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Type</label>
                    <select name="type" class="w-full  border-gray-200 p-2 bg-white dark:bg-gray-700">
                      <option value="seasonal" {{ ($discount->type ?? '') === 'seasonal' ? 'selected' : '' }}>Seasonal</option>
                      <option value="special" {{ ($discount->type ?? '') === 'special' ? 'selected' : '' }}>Special</option>
                      <option value="other" {{ ($discount->type ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Amount</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount', $discount->amount) }}" class="w-full  border-gray-200 p-2 bg-white dark:bg-gray-700" required>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Amount Type</label>
                    <select name="amount_type" class="w-full  border-gray-200 p-2 bg-white dark:bg-gray-700">
                      <option value="percent" {{ $discount->amount_type === 'percent' ? 'selected' : '' }}>Percent</option>
                      <option value="fixed" {{ $discount->amount_type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $discount->start_date) }}" class="w-full  border-gray-200 p-2 bg-white dark:bg-gray-700">
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $discount->end_date) }}" class="w-full  border-gray-200 p-2 bg-white dark:bg-gray-700">
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Assign to Rooms</label>
                    <select name="rooms[]" multiple class="w-full  border-gray-200 p-2 bg-white dark:bg-gray-700" size="6">
                      @foreach($rooms as $r)
                        <option value="{{ $r->id }}" {{ $discount->rooms->contains('id', $r->id) ? 'selected' : '' }}>{{ $r->room_name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Existing Images</label>
                    <div class="flex space-x-2 mt-2">
                      @foreach($discount->images as $img)
                        <div class="w-24 h-16 overflow-hidden rounded border relative">
                          <img src="{{ asset('images/promotions/' . $img->filename) }}" class="w-full h-full object-cover" alt="">
                          {{-- per-image delete not implemented yet; avoid nested form which breaks the outer form submission --}}
                        </div>
                      @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-1">To add new images, upload below. Existing images aren't deleted automatically.</p>
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Add Promo Images (multiple)</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full">
                  </div>

                  <div>
                    {{-- ensure a value is always present and boolean-valid: use hidden + checkbox value="1" --}}
                    <input type="hidden" name="combinable" value="0">
                    <label class="inline-flex items-center">
                      <input type="checkbox" name="combinable" value="1" {{ $discount->combinable ? 'checked' : '' }} class="mr-2"> Combinable with other discounts
                    </label>
                  </div>

                  <div>
                    {{-- ensure a value is always present and boolean-valid: use hidden + checkbox value="1" --}}
                    <input type="hidden" name="active" value="0">
                    <label class="inline-flex items-center">
                      <input type="checkbox" name="active" value="1" {{ $discount->active ? 'checked' : '' }} class="mr-2"> Active
                    </label>
                  </div>

                </div>

                <div class="pt-4">
                  <button type="submit" class="px-4 py-2 bg-indigo-600 text-white ">Update Discount</button>
                  <a href="{{ route('admin.discounts.index') }}" class="ml-2 text-gray-600">Cancel</a>
                </div>
              </form>
            </div>

          </div>
@endsection     