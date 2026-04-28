@props(['discount'])

@php
  $label = $discount->amount_type === 'percent'
    ? rtrim(rtrim(number_format($discount->amount,2),'0'),'.') . "%"
    : '₱' . number_format($discount->amount, 2);
@endphp

<div class="inline-flex items-center space-x-2">
  <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $label }} off</span>
  @if(method_exists($discount, 'isActive') && $discount->isActive())
    <span class="px-2 inline-flex text-xs leading-5 font-semibold  bg-green-100 text-green-800">Active</span>
  @elseif($discount->active)
    <span class="px-2 inline-flex text-xs leading-5 font-semibold  bg-yellow-100 text-yellow-800">Scheduled</span>
  @else
    <span class="px-2 inline-flex text-xs leading-5 font-semibold  bg-gray-100 text-gray-800">Inactive</span>
  @endif
</div>
