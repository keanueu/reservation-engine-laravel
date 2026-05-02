<!-- Gallery Section -->
<div class="bg-white py-2 mb-14 font-[Manrope]">
  <div class="max-w-6xl mx-auto px-6 mb-12">
  <div class="text-center" data-reveal>
  <p class="text-[#964B00] text-sm sm:  mb-2">
    Explore Our Resort
  </p>
  <h1 class="text-3xl sm:text-4xl md:text-5xl ">
    Photo Gallery
  </h1>
  <p class="text-black text-sm sm: mt-3  max-w-2xl mx-auto">
    See the beauty of Cabanas Beach Resort & Hotel, from our serene beach and 
    cozy rooms to our happy guests.
  </p>
</div>
  </div>

  <div class="max-w-6xl mx-auto px-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-10">
      @foreach ($image->chunk(3) as $index => $column)
        <div class="grid gap-4" data-reveal data-reveal-delay="{{ $index + 1 }}">
          @foreach ($column as $img)
            <div class="overflow-hidden">
              <img
                class="w-full h-64 object-cover shadow-md hover:scale-105 transition-transform duration-300"
                src="{{ asset('images/' . $img->image) }}"
                alt="Gallery Image">
            </div>
          @endforeach
        </div>
      @endforeach
    </div>
  </div>
</div>
