<section class="bg-white text-black py-16 font-[Inter]">
    @php
        $galleryImages = \App\Models\Images::orderBy('created_at', 'desc')->take(12)->get();
    @endphp

    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12" data-reveal>
            <p class="text-sm font-semibold mb-4 section-label">Memories</p>
            <h2 class="text-4xl md:text-5xl font-bold leading-[1.2] text-gray-900">Gallery</h2>
            <p class="text-base text-gray-600 leading-relaxed mt-4 max-w-2xl mx-auto">
                Explore our beautiful resort through stunning images
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($galleryImages as $index => $image)
                <div class="group relative overflow-hidden aspect-square cursor-pointer" 
                     onclick="openGalleryModal({{ $index }})">
                    <img src="{{ asset('images/' . $image->image) }}" 
                         alt="Gallery Image" 
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors duration-300 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-4xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            zoom_in
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-base text-gray-500">No images available yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Gallery Modal -->
<div id="galleryModal" class="fixed inset-0 bg-black/95 z-50 hidden items-center justify-center p-4">
    <!-- Image Counter - Top Left -->
    <div class="absolute top-6 left-6 text-white text-lg font-medium">
        <span id="currentImageIndex">1</span> / <span id="totalImages">0</span>
    </div>

    <!-- Close Button - Top Right -->
    <button class="absolute top-6 right-6 text-white hover:text-gray-300 transition-colors" onclick="closeGalleryModal()">
        <span class="material-symbols-outlined text-4xl">close</span>
    </button>

    <!-- Previous Button - Left -->
    <button id="prevBtn" class="absolute left-6 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 transition-colors p-2" onclick="navigateGallery(-1)">
        <span class="material-symbols-outlined text-5xl">chevron_left</span>
    </button>

    <!-- Image Container -->
    <div class="relative max-w-7xl max-h-[80vh] flex items-center justify-center" onclick="event.stopPropagation()">
        <img id="galleryModalImage" src="" alt="Gallery Image" class="max-w-full max-h-[80vh] object-contain">
    </div>

    <!-- Next Button - Right -->
    <button id="nextBtn" class="absolute right-6 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 transition-colors p-2" onclick="navigateGallery(1)">
        <span class="material-symbols-outlined text-5xl">chevron_right</span>
    </button>

    <!-- Press Esc to Exit - Bottom Center -->
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/70 text-sm">
        Press <kbd class="px-2 py-1 bg-white/10 rounded">Esc</kbd> to exit
    </div>
</div>

<script>
const galleryImages = [
    @foreach($galleryImages as $img)
        '{{ asset('images/' . $img->image) }}',
    @endforeach
];
let currentIndex = 0;

function openGalleryModal(index) {
    currentIndex = index;
    updateGalleryModal();
    const modal = document.getElementById('galleryModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeGalleryModal() {
    const modal = document.getElementById('galleryModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

function navigateGallery(direction) {
    currentIndex += direction;
    if (currentIndex < 0) currentIndex = galleryImages.length - 1;
    if (currentIndex >= galleryImages.length) currentIndex = 0;
    updateGalleryModal();
}

function updateGalleryModal() {
    const modalImage = document.getElementById('galleryModalImage');
    const currentImageIndex = document.getElementById('currentImageIndex');
    const totalImages = document.getElementById('totalImages');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    modalImage.src = galleryImages[currentIndex];
    currentImageIndex.textContent = currentIndex + 1;
    totalImages.textContent = galleryImages.length;

    // Hide navigation buttons if only one image
    if (galleryImages.length <= 1) {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
    } else {
        prevBtn.style.display = 'block';
        nextBtn.style.display = 'block';
    }
}

document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('galleryModal');
    if (!modal.classList.contains('hidden')) {
        if (e.key === 'Escape') {
            closeGalleryModal();
        } else if (e.key === 'ArrowLeft') {
            navigateGallery(-1);
        } else if (e.key === 'ArrowRight') {
            navigateGallery(1);
        }
    }
});

// Close modal when clicking outside the image
document.getElementById('galleryModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeGalleryModal();
    }
});
</script>
