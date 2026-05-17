@php
  use App\Models\Discount;

  // Load all active discounts (including their images) so the carousel shows every active promo
  $promos = Discount::with('images')->where('active', 1)->get();
@endphp

@if($promos->count())
  <section data-animate class="opacity-0 will-change-transform will-change-opacity py-12 bg-white overflow-hidden">
    {{-- Match room section width and padding --}}
    <div class="max-w-7xl mx-auto px-6">

      {{-- Modern Header --}}
      <div class="flex flex-col md:flex-row items-end justify-between mb-10 gap-6">
        <div class="max-w-2xl">
          <div class="flex items-center gap-3 mb-4">
            <span class="w-12 h-[2px] bg-[#63360D]"></span>
            <span class="text-md font-bold text-[#A15D1A]">Special Offers</span>
          </div>
          <h2 class="text-4xl lg:text-6xl font-extrabold text-black leading-tight">
            Current <span class="text-[#63360D]">Promotions</span>
          </h2>
          <p class="mt-4 text-black font-medium text-lg leading-relaxed">
            Experience the peak of luxury with our seasonal and special offers. Hand-picked deals designed to make your stay extraordinary.
          </p>
        </div>
        <div class="hidden md:flex gap-2">
            <div id="promo-prev-trigger" class="cursor-pointer w-14 h-14 rounded-full text-white flex items-center justify-center bg-[#63360D] transition-all duration-300">
                <span class="material-symbols-outlined text-2xl">west</span>
            </div>
            <div id="promo-next-trigger" class="cursor-pointer w-14 h-14 rounded-full text-white flex items-center justify-center bg-[#63360D] transition-all duration-300">
                <span class="material-symbols-outlined text-2xl">east</span>
            </div>
        </div>
      </div>

      @php
        $combined = [];
        $basePath = 'images/promotions/';

        foreach ($promos as $promo) {
          $promoName = $promo->name ?? 'Promo';
          foreach ($promo->images ?? [] as $img) {
            $filename = $img->filename ?? $img->path ?? null;
            if (!$filename)
              continue;
            $src = asset($basePath . ltrim($filename, '/'));
            if (!in_array($src, array_column($combined, 'src'))) {
              $combined[] = [
                'src' => $src,
                'label' => $promoName,
                'alt' => $img->alt ?? $promoName
              ];
            }
          }
        }
        $slides = array_values($combined);
      @endphp

      @if(count($slides))
        <div class="mt-4 relative group">
          <div x-data="{
                      slides: {{ json_encode($slides) }},
                      idx: 0,
                      startX: 0,
                      isDragging: false,
                      autoplay: true,
                      interval: null,
                      init() { 
                        if (this.autoplay) this.startAutoplay();
                        // Connect custom triggers
                        document.getElementById('promo-prev-trigger')?.addEventListener('click', () => this.prev());
                        document.getElementById('promo-next-trigger')?.addEventListener('click', () => this.next());
                      },
                      startAutoplay() { if (this.interval) clearInterval(this.interval); this.interval = setInterval(() => this.next(), 6000); },
                      stopAutoplay() { if (this.interval) { clearInterval(this.interval); this.interval = null; } },
                      next() { this.idx = (this.idx + 1) % this.slides.length; },
                      prev() { this.idx = (this.idx - 1 + this.slides.length) % this.slides.length; },
                      onPointerDown(e) { this.stopAutoplay(); this.isDragging = true; this.startX = (e.touches ? e.touches[0].clientX : e.clientX); },
                      onPointerUp(e) {
                        if (!this.isDragging) return; this.isDragging = false;
                        let endX = (e.changedTouches ? e.changedTouches[0].clientX : e.clientX);
                        let diff = this.startX - endX;
                        if (diff > 50) this.next();
                        else if (diff < -50) this.prev();
                        if (this.autoplay) this.startAutoplay();
                      }
            }" class="relative w-full overflow-hidden rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.1)]" style="touch-action: pan-y;"
            @keydown.window.arrow-left.prevent="prev()" @keydown.window.arrow-right.prevent="next()">

            <div class="h-[300px] sm:h-[400px] lg:h-[32rem] bg-gray-50" @touchstart="onPointerDown($event)"
              @touchend="onPointerUp($event)" @pointerdown="onPointerDown($event)" @pointerup="onPointerUp($event)">
              <template x-for="(s, i) in slides" :key="i">
                <div x-show="idx === i" 
                     x-transition:enter="transition-all duration-1000 ease-out"
                     x-transition:enter-start="opacity-0 scale-110"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition-all duration-700 ease-in"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute inset-0 w-full h-full">
                    <img x-bind:src="s.src" x-bind:alt="s.alt"
                         class="w-full h-full object-cover transform-gpu"
                         loading="lazy" decoding="async" />
                    
                    {{-- Luxury Overlay Gradient --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    
                    {{-- Slide Content --}}
                    <div class="absolute inset-0 flex flex-col justify-end p-8 sm:p-12">
                        <div class="flex items-center gap-3 mb-4 animate-in fade-in slide-in-from-left-4 duration-1000">
                            <span class="px-4 py-1.5 bg-[#63360D] text-white text-md font-bold shadow-xl">Limited Offer</span>
                        </div>
                        <h3 x-text="s.label" class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-2 animate-in fade-in slide-in-from-bottom-4 duration-1000"></h3>
                    </div>
                </div>
              </template>
            </div>

            {{-- Mobile Controls --}}
            <div class="md:hidden flex gap-2 absolute top-4 right-4">
                <button @click="prev()" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 transition-all">
                    <span class="material-symbols-outlined text-lg">west</span>
                </button>
                <button @click="next()" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 transition-all">
                    <span class="material-symbols-outlined text-lg">east</span>
                </button>
            </div>

            {{-- Progress Dots --}}
            <div class="absolute bottom-8 right-8 flex items-center space-x-3">
              <template x-for="(s, i) in slides" :key="i">
                <button @click="idx = i; stopAutoplay(); startAutoplay();" 
                        class="h-1.5 transition-all duration-500 rounded-full shadow-lg"
                        :class="idx === i ? 'bg-white w-12' : 'bg-white/30 w-3 hover:bg-white/60'"
                        :aria-label="'Go to slide ' + (i + 1)"></button>
              </template>
            </div>
          </div>
        </div>
      @endif

    </div>
  </section>
@endif
