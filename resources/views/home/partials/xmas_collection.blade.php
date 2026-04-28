@php
  use App\Models\Discount;

  // Load all active discounts (including their images) so the carousel shows every active promo
  $promos = Discount::with('images')->where('active', 1)->get();
@endphp

@if($promos->count())
  <section data-animate class="opacity-0 will-change-transform will-change-opacity font-[Manrope]">
    <div class="bg-white">
      {{-- Match room section width and padding --}}
      <div class="max-w-6xl mx-auto px-6">

        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-xl  tracking-tight text-black">
              Current Promotions
            </h2>
            <p class="mt-1 text-gray-500 text-xs ">
              Seasonal and special offers — don't miss out on these deals!
            </p>
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
          {{-- Make same responsive layout behavior as room grid --}}
          <div class="mt-4">
            <div x-data="{
                        slides: {{ json_encode($slides) }},
                        idx: 0,
                        startX: 0,
                        isDragging: false,
                        autoplay: true,
                        interval: null,
                        init() { if (this.autoplay) this.startAutoplay(); },
                        startAutoplay() { if (this.interval) clearInterval(this.interval); this.interval = setInterval(() => this.next(), 5000); },
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
              }" class="relative w-full overflow-hidden shadow-lg" style="touch-action: pan-y;"
              @keydown.window.arrow-left.prevent="prev()" @keydown.window.arrow-right.prevent="next()">

              <div class="h-64 sm:h-80 lg:h-[28rem] bg-gray-100 dark:bg-gray-700" @touchstart="onPointerDown($event)"
                @touchend="onPointerUp($event)" @pointerdown="onPointerDown($event)" @pointerup="onPointerUp($event)">
                <template x-for="(s, i) in slides" :key="i">
                  <img x-show="idx === i" x-transition:enter="transition-opacity duration-500 ease-in"
                    x-transition:leave="transition-opacity duration-500 ease-out" x-bind:src="s.src" x-bind:alt="s.alt"
                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 ease-in-out"
                    loading="lazy" />
                </template>


                <div class="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-black/50 to-transparent">
                  <span x-text="slides[idx].label"
                    class="inline-block px-3 py-1 text-xs font-extrabold text-white uppercase bg-[#964B00]  shadow-lg"></span>
                </div>
              </div>

              <button @click="prev()"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white p-3  shadow-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-white"
                aria-label="Previous slide">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </button>

              <button @click="next()"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white p-3  shadow-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-white"
                aria-label="Next slide">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </button>

              <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex items-center space-x-2">
                <template x-for="(s, i) in slides" :key="i">
                  <button @click="idx = i" :class="idx === i ? 'bg-white w-3 h-3' : 'bg-white/50 w-2 h-2 hover:bg-white/75'"
                    class=" transition-all duration-300 shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    :aria-label="'Go to slide ' + (i + 1)" title=""></button>
                </template>
              </div>
            </div>
          </div>
        @endif

      </div>
    </div>
  </section>
@endif