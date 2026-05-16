<!-- Generic Booking Extension Modal (single instance) -->
<div id="extension-generic-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 font-['Raleway']">
  <div class="bg-white w-full max-w-md p-6">
    <h3 class="text-lg font-semibold mb-4">Request Extension</h3>

    <input type="hidden" id="extension-booking-id" value="">

    <div class="mb-3">
      <label class="block text-sm font-medium text-gray-700">Hours</label>
   <select id="extension-hours" 
    class="block w-full border border-gray-300 p-2 text-sm focus:ring-[#964B00] focus:border-[#964B00]">
    <option value="1">1 hour</option>
    <option value="2">2 hours</option>
    <option value="5">5 hours</option>
</select>
    </div>

    <div class="mb-3">
      <label class="block text-sm font-medium text-gray-700">Payment method</label>
      <select id="extension-payment"  class="block w-full border border-gray-300 p-2 text-sm focus:ring-[#964B00] focus:border-[#964B00]">
        <option value="online">Pay Online</option>
        <option value="frontdesk">Pay at Frontdesk</option>
      </select>
    </div>

    <div id="extension-feedback" class="text-sm text-red-600 mb-3 hidden"></div>

    <div class="flex justify-end space-x-2">
      <button type="button" id="extension-cancel" class="px-3 py-2 bg-black text-white text-sm">Cancel</button>
      <button type="button" id="extension-submit" class="px-3 py-2 bg-[#964B00] text-white text-sm">Pay</button>
    </div>
  </div>
</div>

