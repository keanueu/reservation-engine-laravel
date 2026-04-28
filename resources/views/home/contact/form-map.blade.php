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
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4 text-center flex-shrink-0" viewBox="0 0 20 20"
                  fill="currentColor" aria-hidden="true">
                  <path
                    d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                </svg>
                <span class="text-sm">+1-316-555-1258</span>
              </div>

              <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4 text-center flex-shrink-0" viewBox="0 0 20 20"
                  fill="currentColor" aria-hidden="true">
                  <path d="M2.003 5.884L10 11.884l7.997-6.001A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                  <path d="M18 8.118l-8 6-8-6V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                </svg>
                <span class="text-sm">hadams@gmail.com</span>
              </div>

              <div class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mt-1 mr-4 text-center flex-shrink-0"
                  viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd"
                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                    clip-rule="evenodd" />
                </svg>
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
                Send Message <i class="fas fa-paper-plane ml-2" aria-hidden="true"></i>
              </button>
            </form>
          </div>
        </div>
      </div>