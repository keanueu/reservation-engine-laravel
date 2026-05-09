 <div class="text-center mb-12">
        <p class="text-[#964B00] text-sm sm:  mb-2">Have Questions?</p>
        <h1 class="text-3xl sm:text-4xl md:text-5xl  text-black">
          Contact Our Team
        </h1>
        <p class="text-black text-sm sm: mt-3  max-w-2xl mx-auto">
          We're here to help! Send us a message, give us a call, or visit our location on the map below.
        </p>
      </div>

      <div class="bg-white shadow-xl overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2">

          <div class="p-8 lg:p-12 text-white"
            style="background-image: linear-gradient(to bottom right, #f97316, #ea580c); /* orange-500 to orange-700 */">

            <h2 class="text-3xl lg:text-4xl font-normal mb-4">Contact Information</h2>
            <p class="mb-8 text-sm ">
              Explore new destinations, indulge in local cuisines, and immerse yourself in diverse cultures.
            </p>
            <div class="space-y-8">
              <div class="flex items-center">
                <span class="material-symbols-outlined text-2xl mr-4 text-center flex-shrink-0">call</span>
                <span class="text-sm">+1-316-555-1258</span>
              </div>

              <div class="flex items-center">
                <span class="material-symbols-outlined text-2xl mr-4 text-center flex-shrink-0">mail</span>
                <span class="text-sm">hadams@gmail.com</span>
              </div>

              <div class="flex items-start">
                <span class="material-symbols-outlined text-2xl mt-1 mr-4 text-center flex-shrink-0">location_on</span>
                <span class="text-sm">802 Pension Rd, Maine 96812, USA</span>
              </div>
            </div>
          </div>

          <div class="p-8 lg:p-12">

            @if(session()->has('message'))
              <div id="successAlert"
                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 relative mb-6 flex justify-between items-center">
                <span>{{ session()->get('message') }}</span>
                <button onclick="document.getElementById('successAlert').style.display='none'"
                  class="ml-4 text-green-700 hover:text-green-900 font-bold text-sm leading-none">
                  ×
                </button>
              </div>
            @endif

            <form action="{{ url('contact') }}" method="POST" class="space-y-2">
              @csrf

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="name" class="font-normal text-sm text-black block mb-1">Your Name</label>
                  <input type="text" id="name" name="name" placeholder="Enter your name here..." required
                    class="w-full border border-gray-300 focus:border-[#964B00] outline-none py-3 px-4 transition-colors font-normal  text-sm" />
                </div>
                <div>
                  <label for="email" class="font-normal text-sm text-black block mb-1">Your Email</label>
                  <input type="email" id="email" name="email" placeholder="Enter your email here..." required
                    class="w-full border border-gray-300 focus:border-[#964B00] outline-none py-3 px-4 transition-colors font-normal  text-sm" />
                </div>
              </div>

              <div>
                <label for="phone" class="font-normal text-sm text-black block mb-1">Your Phone</label>
                <input type="text" id="phone" name="phone" placeholder="Enter your phone here..." required
                  class="w-full border border-gray-300 focus:border-[#964B00] outline-none py-3 px-4 transition-colors font-normal text-sm" />
              </div>

              <div>
                <label for="message" class="font-normal text-sm text-black block mb-1">Message</label>
                <textarea id="message" name="message" placeholder="Type here..." required rows="3"
                  class="w-full border border-gray-300 focus:border-[#964B00] outline-none py-3 px-4 transition-colors font-normal text-sm"></textarea>
              </div>

              <button type="submit"
                class="w-full bg-[#964B00] hover:bg-[#7a3c00] text-white py-3 font-normal text-sm transition-colors flex items-center justify-center shadow-lg hover:shadow-xl font-normal text-sm">
                Send Message <span class="material-symbols-outlined text-base ml-2">send</span>
              </button>
            </form>
          </div>
        </div>
      </div>
