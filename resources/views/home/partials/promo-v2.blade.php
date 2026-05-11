@php
    use Carbon\Carbon;
    // Placeholder image definition.
    $placeholderImage = "https://placehold.co/400x300/f3f4f6/1f2937?text=Minimal+Suite";

    // Set the rooms variable using the Null Coalescing Operator: 
    // Try to use $rooms (plural), if undefined, try to use $room (singular).
    // If both are undefined, use an empty collection ([]).
    $roomCollection = $rooms ?? $room ?? [];
    // Control whether this template should render promo badges/carousels.
    $showPromos = $showPromos ?? true;
@endphp
<div class="max-w-7xl mx-auto px-6">

   {{-- Room Card Grid --}}
    <div class="
    flex overflow-x-auto snap-x snap-mandatory space-x-6 pb-4
    sm:overflow-visible sm:snap-none sm:space-x-0
    sm:grid sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-1 xl:grid-cols-1
    sm:gap-6 items-stretch
    mt-6 overscroll-auto font-[Inter]" style="-webkit-overflow-scrolling: touch; touch-action: pan-x pan-y;">


        {{-- Only show rooms that currently have an active promotion (admin can remove promos) --}}
        @php
            // Filter rooms that have at least one active discount whose end_date is not passed
            $promoRooms = collect($roomCollection)->filter(function ($r) {
                try {
                    $discounts = $r->discounts ?? [];
                    foreach ($discounts as $d) {
                        $active = $d->active ?? false;
                        $end = $d->end_date ?? null;
                        // treat null end_date as ongoing
                        if ($active) {
                            if (empty($end))
                                return true;
                            // compare dates (accept Y-m-d or datetime strings)
                            if (strtotime($end) >= strtotime(now()))
                                return true;
                        }
                    }
                } catch (\Throwable $e) {
                    return false;
                }
                return false;
            })->values();
        @endphp

        {{-- Iterate only promo rooms --}}
        @forelse ($promoRooms as $room)
            @php
                // Determine a simple promo type tag for client-side filtering
                $promoType = 'none';
                try {
                    foreach ($room->discounts ?? [] as $d) {
                        $name = strtolower($d->name ?? '');
                        if (strpos($name, 'christ') !== false || strpos($name, 'xmas') !== false) {
                            $promoType = 'christmas';
                            break;
                        }
                        if (strpos($name, 'rain') !== false) {
                            $promoType = 'rainy';
                            break;
                        }
                        if (strpos($name, 'senior') !== false || strpos($name, 'pwd') !== false) {
                            $promoType = 'seniorpwd';
                            break;
                        }
                    }
                } catch (\Throwable $e) {
                    $promoType = 'none';
                }
            @endphp
            @php
                $type = strtolower($room->room_type ?? '');
                $beachFrontNames = [
                    'Beach Front - Beach Front Kubo - Terrace Cabana',
                    'Beach Front Kubo Units - Room B or C',
                    'Beach Front Kubo - Twin Cabana',
                    'Beach Front Kubo - Family Cabana',
                    'Vacation House',
                    'Britannia Room w/ Sand Kubo',
                    'Beach Front-Penthouse Suite w/ Sand Kubo',
                    'Beach Front - VIP STUDIO w/ Open Cabana',
                ];
                $nonBeachFrontNames = [
                    'Luxury Unit - King Suite w/ Sand Kubo',
                    'Luxury Unit - Queen Suite w/ Sand Kubo',
                    'Luxury Unit - Mini Queen w/ Sand Kubo',
                    'Lovers Room w/ Sand Kubo',
                    'Sunset Rooftop w/ Sand Kubo',
                ];
                $location = in_array($room->room_name, $beachFrontNames) ? 'beach' : (in_array($room->room_name, $nonBeachFrontNames) ? 'nonbeach' : 'other');

                // --- Promotion Logic ---
                $discount = $room->discounts->first();
                $discountValue = optional($discount)->amount;
                $isPercentage = optional($discount)->amount_type === 'percent' || optional($discount)->amount_type === 'percentage';
                $isFixedAmount = optional($discount)->amount_type === 'fixed';
                $isActive = optional($discount)->active;
                $expiryDate = optional($discount)->end_date;

                $discountedPrice = $room->price;
                if ($isActive && $discountValue > 0) {
                    if ($isPercentage) {
                        $discountedPrice = $room->price * (1 - ($discountValue / 100));
                    } elseif ($isFixedAmount) {
                        $discountedPrice = max(0, $room->price - $discountValue);
                    }
                }

                // --- Image Carousel Logic: Collect all images (Room image + Promo images) ---
                $imageSources = [];

                // 1. Add the main room image(s) - support JSON array, comma-separated, or single filename
                if (!empty($room->image)) {
                    // Normalize and add images safely
                    $decodedRoomImages = json_decode($room->image, true);
                    $addImage = function ($raw) use (&$imageSources, $placeholderImage) {
                        $raw = trim($raw);
                        if (empty($raw))
                            return;
                        // If it's already an absolute URL, use as-is
                        if (preg_match('#^https?://#i', $raw)) {
                            $imageSources[] = $raw;
                            return;
                        }
                        // Strip any leading slashes or a leading 'room/' to avoid double paths
                        $raw = preg_replace('#^/+#', '', $raw);
                        $raw = preg_replace('#^room/#i', '', $raw);
                        $imageSources[] = asset('room/' . $raw);
                    };

                    if (is_array($decodedRoomImages)) {
                        foreach ($decodedRoomImages as $ri) {
                            $addImage($ri);
                        }
                    } else {
                        if (strpos($room->image, ',') !== false) {
                            $parts = array_map('trim', explode(',', $room->image));
                            foreach ($parts as $p) {
                                $addImage($p);
                            }
                        } else {
                            $addImage($room->image);
                        }
                    }
                } else {
                    $imageSources[] = $placeholderImage;
                }

                // 2. Add any additional images stored in the Images table for this room (uploaded via frontdesk)
                if (method_exists($room, 'images')) {
                    foreach ($room->images as $imgModel) {
                        $raw = $imgModel->image ?? null;
                        if (!empty($raw)) {
                            // Normalize like above
                            if (preg_match('#^https?://#i', $raw)) {
                                $imageSources[] = $raw;
                            } else {
                                $raw = preg_replace('#^/+#', '', $raw);
                                $raw = preg_replace('#^room/#i', '', $raw);
                                $imageSources[] = asset('room/' . $raw);
                            }
                        }
                    }
                }

                // 3. Add active promo images (if any)
                if ($showPromos) {
                    $activeDiscounts = $room->discounts->filter(function ($d) {
                        return $d->active ?? false;
                    })->values();
                    foreach ($activeDiscounts as $d) {
                        foreach ($d->images as $image) {
                            $imageSources[] = asset('discount/' . $image->path);
                        }
                    }
                }

                // Remove duplicates, reindex, and drop the last image (if exists)
                $imageSources = array_values(array_unique($imageSources));
                if (count($imageSources) > 1) {
                    array_pop($imageSources);
                }

            @endphp

            <div x-show="(roomFilter === 'all' || roomFilter === '{{ $type }}') && (roomLocation === 'all' || roomLocation === '{{ $location }}') && (roomPromo === 'all' || roomPromo === '{{ $promoType }}')"
                x-cloak data-room-id="{{ $room->id }}"
                class="room-card snap-start min-w-full sm:min-w-0 sm:w-full bg-white border border-gray-200 shadow-sm overflow-hidden flex flex-col md:flex-row h-full">

                {{-- Start: Image (left) + Details (right) layout to match roomcart UI --}}
                {{-- Image / Carousel column --}}
                @if(config('app.debug'))
                @endif
                <div x-data="{ 
                                                images: {{ json_encode($imageSources) }}, 
                                                currentIndex: 0,
                                                // Optional: Auto-slide every 5 seconds
                                                init() {
                                                    if (this.images.length > 1) {
                                                        setInterval(() => {
                                                            this.currentIndex = (this.currentIndex + 1) % this.images.length;
                                                        }, 5000);
                                                    }
                                                }
                                            }"
                    class="relative room-image-container overflow-hidden h-56 md:h-full md:w-2/5 flex-shrink-0">

                    {{-- Image Slides (single bound image to avoid hidden lazy-load issues) --}}
                    <div class="h-full w-full relative">
                        <img draggable="false" x-bind:src="images[currentIndex]"
                            :alt="`{{ addslashes($room->room_name) }} image ${currentIndex + 1}`"
                            style="-webkit-user-drag: none; -webkit-user-select: none; user-select: none;"
                            class="absolute inset-0 w-full h-full object-cover transition duration-500 transform group-hover:scale-105"
                            x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-500"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" />
                    </div>

                    {{-- Gradient Overlay (for text visibility) --}}
                    <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/70 to-transparent">
                    </div>

                    {{-- Carousel Indicators (optional, only shows if multiple images exist) --}}
                    @if(count($imageSources) > 1)
                        <div class="absolute top-2 left-1/2 transform -translate-x-1/2 flex space-x-1 z-10">
                            @foreach ($imageSources as $key => $src)
                                <button @click="currentIndex = {{ $key }}"
                                    class="w-2 h-2  transition-colors duration-300"
                                    :class="{ 'bg-white': currentIndex === {{ $key }}, 'bg-white/40': currentIndex !== {{ $key }} }"
                                    aria-label="Go to slide {{ $key + 1 }}"></button>
                            @endforeach
                        </div>
                    @endif

                    {{-- 🎁 DISCOUNT BADGE (Top-Right) --}}
                    @if ($showPromos && $isActive && $discountValue > 0 && ($isPercentage || $isFixedAmount))
                        @php
                            $badgeText = $isPercentage ? '-' . rtrim(rtrim(number_format($discountValue, 2), '0'), '.') . '%' : 'SALE';
                        @endphp
                        <div class="absolute top-3 right-3 z-10">
                            <span
                                class="inline-block bg-[#964B00] text-white text-sm font-bold py-1 px-3  shadow-lg">
                                {{ $badgeText }}
                            </span>
                        </div>
                    @endif

                    {{-- PRICE OVERLAY (Bottom-Left) --}}
                    <div class="absolute bottom-4 left-4 z-10">
                        @if($isActive && $discountValue > 0 && ($isPercentage || $isFixedAmount))
                            <span class="text-sm line-through block mb-1" style="color: #ffffff;">
                                PHP {{ number_format($room->price ?? 0, 2) }}
                            </span>
                            <p class="text-lg font-extrabold" style="color: #ffffff;">
                                PHP {{ number_format($discountedPrice, 2) }}
                            </p>
                        @else
                            <p class="text-2xl font-extrabold" style="color: #ffffff;">
                                PHP {{ number_format($room->price ?? 0, 2) }}
                            </p>
                        @endif
                        <span class="text-xs block mt-0.5" style="color: #ffffff;">Per night</span>
                    </div>

                </div>

                {{-- Details column --}}
                <div class="p-4 flex flex-col flex-grow md:w-3/5">

                    <h3 class="text-lg  text-black mb-2">
                        {{ $room->room_name }}
                    </h3>

                    <div
                        class="flex flex-wrap gap-x-4 gap-y-3 text-gray-600 text-xs  mb-4 pt-3 border-t border-gray-200">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.6" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 20v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m8-10a4 4 0 100-8 4 4 0 000 8z" />
                            </svg>
                            <span>{{ $room->accommodates }} Guests</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.6" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 18H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2zM9 10a1 1 0 100 2 1 1 0 000-2z" />
                            </svg>
                            <span>{{ $room->beds }}</span>
                        </div>
                    </div>

                    @include('home.cart.room-tab.amenity-svgsv2')

                    {{-- Offer ends info --}}
                    @if($isActive && $expiryDate)
                        <div class="py-4">
                            <p class="text-xs font-normal text-[#964B00]">
                                Offer Ends: {{ Carbon::parse($expiryDate)->format('M d, Y') }}
                            </p>
                        </div>
                    @endif

                    <div class="mt-auto flex justify-between items-center">
                        <a href="{{ url('room_detailsv2', $room->id) }}"
                            class="text-[#964B00] underline text-xs hover:text-black">
                            View Details
                        </a>
                        <button data-room-id="{{ $room->id }}" data-max-guests="{{ $room->accommodates }}"
                            class="book-now-btn bg-[#964B00] px-6 py-2.5 text-xs text-white hover:bg-black transition  flex items-center justify-center">
                            <span class="btn-text">BOOK NOW</span>
                            <svg class="btn-loader hidden animate-spin ml-2 h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </button>
                    </div>

                </div>

            </div>
        @empty
            <div class="w-full text-center p-10 col-span-full">
                <p class="text-lg text-gray-600">No rooms available at this time. Please check your controller to ensure
                    data is
                    passed.</p>
            </div>
        @endforelse

    </div>
</div>
</div>
