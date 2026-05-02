<div class="bg-white dark:bg-black p-4 sm:p-6  shadow-xl mb-4 max-w-4xl mx-auto mt-4">
  <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b pb-2">Create Alert</h3>
  <form method="POST" action="{{ url('/admin/alerts') }}">
    @csrf

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
      <input 
        type="text" 
        name="title" 
        placeholder="Title (optional)" 
        class="border border-gray-300 dark:border-black p-3  focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-black text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-300" 
      />
      
      <select 
        name="severity" 
        required 
        class="border border-gray-300 dark:border-black p-3  focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-black text-gray-900 dark:text-white"
      >
        <option value="clear">Clear</option>
        <option value="advisory">Advisory</option>
        <option value="watch">Watch</option>
        <option value="warning">Warning</option>
      </select>
      
      <input 
        type="text" 
        name="location" 
        placeholder="Location (optional)" 
        class="border border-gray-300 dark:border-black p-3  focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-black text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-300" 
      />
      
      <div class="flex items-center sm:col-start-2">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2 cursor-pointer">
          <input 
            type="checkbox" 
            name="send_email" 
            value="1" 
            class="h-4 w-4 text-[#964B00] border-gray-300 rounded focus:ring-orange-500" 
          /> 
          Send Email Notification
        </label>
      </div>
    </div>
    
    <div class="mb-4">
      <textarea 
        name="message" 
        rows="4" 
        placeholder="Message" 
        required 
        class="w-full border border-gray-300 dark:border-black p-3  focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-black text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-300 resize-y"
      ></textarea>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
      
      <div class="flex flex-1 gap-3 flex-col xs:flex-row">
        <input 
          type="datetime-local" 
          name="starts_at" 
          aria-label="Alert Start Time"
          class="flex-1 border border-gray-300 dark:border-black p-3  bg-white dark:bg-black text-gray-900 dark:text-white focus:ring-orange-500 focus:border-orange-500" 
        />
        <input 
          type="datetime-local" 
          name="ends_at" 
          aria-label="Alert End Time"
          class="flex-1 border border-gray-300 dark:border-black p-3  bg-white dark:bg-black text-gray-900 dark:text-white focus:ring-orange-500 focus:border-orange-500" 
        />
      </div>

      <button 
        type="submit" 
        class="w-full sm:w-auto px-6 py-3 bg-[#964B00] hover:bg-[#7a3c00] text-white font-semibold  transition duration-150 ease-in-out shadow-md"
      >
        Create Alert
      </button>
    </div>
  </form>
</div>
