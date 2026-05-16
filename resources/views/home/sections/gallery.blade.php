<section class="bg-white text-black py-16 ">
    @php
        // Fetch only general gallery images (those not linked to a specific room)
        $galleryImages = \App\Models\Images::whereNull('room_id')->orderBy('created_at', 'desc')->get();
    @endphp

    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12" data-reveal>
            <p class="text-sm font-medium mb-4 section-label">Memories</p>
            <h2 class="text-4xl md:text-5xl font-medium leading-relaxed text-black">Gallery</h2>
            <p class="text-base text-black leading-relaxed mt-4 max-w-2xl mx-auto">
                Explore our beautiful resort through stunning images
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($galleryImages as $index => $image)
                <div class="relative overflow-hidden aspect-square" 
                     data-reveal data-reveal-delay="{{ $index % 4 }}">
                    <img src="{{ asset('images/' . $image->image) }}" 
                         alt="Gallery Image" 
                         class="w-full h-full object-cover">
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-base text-black">No images available yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

