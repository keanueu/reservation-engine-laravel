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

<div class="bg-white py-2 font-[Manrope]" x-data="{ roomFilter: 'all', roomLocation: 'all' }" x-cloak>
    
    @include('home.partials.xmas_collection')
    <div class="max-w-6xl mx-auto px-6">



        {{-- Room Card Grid --}}
        <div class="
                flex overflow-x-auto snap-x snap-mandatory space-x-6 pb-4
                sm:overflow-visible sm:snap-none sm:space-x-0
                sm:grid sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 {{-- *** CHANGED THIS LINE *** --}}
                sm:gap-6 items-stretch
                mt-6 overscroll-auto" style="-webkit-overflow-scrolling: touch; touch-action: pan-x pan-y;">

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

                <div x-show="(roomFilter === 'all' || roomFilter === '{{ $type }}') && (roomLocation === 'all' || roomLocation === '{{ $location }}')"
                    x-cloak
                    data-room-id="{{ $room->id }}"
                    class="room-card snap-start min-w-full sm:min-w-0 sm:w-full bg-white border border-gray-100 shadow-lg transition duration-300 hover:shadow-xl overflow-hidden flex flex-col md:flex-row h-full">

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
                            }" class="relative room-image-container overflow-hidden h-56 md:h-full md:w-2/5 flex-shrink-0">

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

                        {{-- DISCOUNT BADGE (Top-Right) --}}
                        @if ($showPromos && $isActive && $discountValue > 0 && ($isPercentage || $isFixedAmount))
                            @php
                                $badgeText = $isPercentage ? '-' . rtrim(rtrim(number_format($discountValue, 2), '0'), '.') . '%' : 'SALE';
                            @endphp
                            <div class="absolute top-3 right-3 z-10">
                                <span
                                    class="inline-block bg-[#964B00] text-white text-sm font-bold py-1 px-3 tracking-wider uppercase  shadow-lg">
                                    {{ $badgeText }}
                                </span>
                            </div>
                        @endif

                        {{-- PRICE OVERLAY (Bottom-Left) --}}
                        <div class="absolute bottom-4 left-4 z-10 text-white">
                            @if($isActive && $discountValue > 0 && ($isPercentage || $isFixedAmount))
                                <span class="text-sm  text-gray-200 line-through tracking-wide block mb-1">
                                    PHP {{ number_format($room->price ?? 0, 2) }}
                                </span>
                                <p class="text-lg font-extrabold">
                                    PHP {{ number_format($discountedPrice, 2) }}
                                </p>
                            @else
                                <p class="text-2xl font-extrabold">
                                    PHP {{ number_format($room->price ?? 0, 2) }}
                                </p>
                            @endif
                            <span class="text-xs  text-gray-300 tracking-wide block mt-0.5">PER NIGHT</span>
                        </div>

                    </div>

                    {{-- Details column --}}
                    <div class="p-4 flex flex-col flex-grow md:w-3/5">

                        <h3 class="text-lg  text-black tracking-tight mb-2">
                            {{ $room->room_name }}
                        </h3>

                        <p class="text-xs text-black  mb-4">
                            {{ \Illuminate\Support\Str::limit($room->description ?? 'An exquisite room offering premium comfort and luxury for your stay.', 100) }}
                        </p>

                        {{-- Offer ends info --}}
                        @if($isActive && $expiryDate)
                            <div class="py-1 mb-2">
                                <p class="text-xs font-normal text-[#964B00] tracking-wider">
                                    Offer Ends: {{ Carbon::parse($expiryDate)->format('M d, Y') }}
                                </p>
                            </div>
                        @endif

                        <div class="flex flex-wrap gap-x-4 gap-y-3 text-gray-600 text-xs  mb-4 pt-3 border-t border-gray-200">

                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-600" style="font-size: 16px;">group</span>
                                <span class="font-medium text-xs text-gray-600">{{ $room->accommodates }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-600" style="font-size: 16px;">bed</span>
                                <span class="text-xs">{{ $room->beds }}</span>
                            </div>
                        </div>

                        <div class="mt-auto flex justify-between items-center">
                            <a href="{{ url('room_details', $room->id) }}" class="text-[#964B00] underline text-xs hover:text-black">
                                View Details
                            </a>
                            <a href="{{ url('/home/roomcart') }}"
                                class="bg-[#964B00] px-6 py-2.5 text-xs text-white hover:bg-black transition  flex items-center justify-center tracking-wide">
                                <span class="btn-text">Check Availability</span>
                            </a> 
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