@php
    // $booking expected
    $bid = $booking->id ?? 'template';
@endphp

<div id="refund-modal-{{ $bid }}" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white shadow p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-3">Request Refund for Booking #{{ $bid }}</h3>
        <form method="POST" action="{{ route('bookings.requestRefund', $booking->id ?? 0) }}">
            @csrf
            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-700">Amount (optional)</label>
                <input type="number" step="0.01" name="amount" class="mt-1 block w-full border-gray-300"
                    placeholder="Enter amount to refund">
            </div>
            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-700">Reason (optional)</label>
                <textarea name="reason" rows="3" class="mt-1 block w-full border-gray-300"
                    placeholder="Reason for refund (e.g. cancellation)"></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" data-close-refund="{{ $bid }}" class="px-3 py-2 text-xs bg-black">Cancel</button>
                <button type="submit" class="px-3 py-2 text-xs bg-[#964B00] text-white">Submit Request</button>
            </div>
        </form>
    </div>
</div>

