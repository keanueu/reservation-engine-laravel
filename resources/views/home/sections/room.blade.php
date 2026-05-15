@php
    use Carbon\Carbon;
    $placeholderImage = "https://placehold.co/400x300/f3f4f6/1f2937?text=Minimal+Suite";
    $roomCollection = $rooms ?? $room ?? [];
    $showPromos = $showPromos ?? true;
@endphp

<div class="bg-white py-8 font-[Inter]" x-data="{ roomFilter: 'all', roomLocation: 'all' }" x-cloak>

    @include('home.partials.xmas_collection')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="
                grid grid-cols-1 gap-6 mt-6
                sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4
                items-stretch
            ">

            @forelse(collect($roomCollection) as $room)
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

                    $imageSources = [];

                    if (!empty($room->image)) {
                        $decodedRoomImages = json_decode($room->image, true);
                        $addImage = function ($raw) use (&$imageSources, $placeholderImage) {
                            $raw = trim($raw);
                            if (empty($raw))
                                return;
                            if (preg_match('#^https?://#i', $raw)) {
                                $imageSources[] = $raw;
                                return;
                            }
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

                    if (method_exists($room, 'images')) {
                        foreach ($room->images as $imgModel) {
                            $raw = $imgModel->image ?? null;
                            if (!empty($raw)) {
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

                    // No discount images in slider

                    $imageSources = array_values(array_unique($imageSources));
                @endphp

                <div x-show="(roomFilter === 'all' || roomFilter === '{{ $type }}') && (roomLocation === 'all' || roomLocation === '{{ $location }}')"
                    x-cloak
                    data-room-id="{{ $room->id }}"
                    class="room-card bg-white border border-gray-100 shadow-lg transition duration-300 hover:shadow-xl overflow-hidden flex flex-col h-full">

                    <div x-data="{
                                images: {{ json_encode($imageSources) }},
                                currentIndex: 0,
                                init() {
                                    if (this.images.length > 1) {
                                        setInterval(() => {
                                            this.currentIndex = (this.currentIndex + 1) % this.images.length;
                                        }, 5000);
                                    }
                                }
                            }" class="relative room-image-container overflow-hidden h-56 w-full flex-shrink-0">

                        <div class="h-full w-full relative">
                            <img draggable="false" x-bind:src="images[currentIndex]"
                                :alt="`{{ addslashes($room->room_name) }} image ${currentIndex + 1}`"
                                style="-webkit-user-drag: none; -webkit-user-select: none; user-select: none;"
                                class="absolute inset-0 w-full h-full object-cover transition duration-500 transform group-hover:scale-105"
                                x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-500"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" />
                        </div>

                        <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/70 to-transparent z-5">
                        </div>

                        @if(count($imageSources) > 1)
                            <div class="absolute top-2 left-1/2 transform -translate-x-1/2 flex space-x-1 z-20">
                                @foreach ($imageSources as $key => $src)
                                    <button @click="currentIndex = {{ $key }}"
                                        class="w-2 h-2  transition-colors duration-300"
                                        :class="{ 'bg-white': currentIndex === {{ $key }}, 'bg-white/40': currentIndex !== {{ $key }} }"
                                        aria-label="Go to slide {{ $key + 1 }}"></button>
                                @endforeach
                            </div>
                        @endif


                        @if ($showPromos && $isActive && $discountValue > 0 && ($isPercentage || $isFixedAmount))
                            @php
                                $badgeText = $isPercentage ? '-' . rtrim(rtrim(number_format($discountValue, 2), '0'), '.') . '%' : 'SALE';
                            @endphp
                            <div class="absolute top-3 right-3 z-30">
                                <span
                                    class="inline-block bg-[#964B00] text-white text-[10px] font-bold py-1.5 px-3 shadow-xl tracking-widest uppercase">
                                    {{ $badgeText }} OFF
                                </span>
                            </div>
                        @endif

                        <div class="absolute bottom-4 left-4 z-20 text-white">
                            @if($isActive && $discountValue > 0 && ($isPercentage || $isFixedAmount))
                                <span class="text-sm text-gray-200 line-through block mb-1">
                                    PHP {{ number_format($room->price ?? 0, 2) }}
                                </span>
                                <p class="text-white text-lg font-extrabold">
                                    PHP {{ number_format($discountedPrice, 2) }}
                                </p>
                            @else
                                <p class="text-2xl font-extrabold">
                                    PHP {{ number_format($room->price ?? 0, 2) }}
                                </p>
                            @endif
                            <span class="text-xs text-white block mt-0.5">Per night</span>
                        </div>

                    </div>

                    <div class="p-4 flex flex-col flex-grow w-full">

                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            {{ $room->room_name }}
                        </h3>

                        <p class="text-sm text-gray-600 leading-relaxed mb-4 flex-grow">
                            {{ \Illuminate\Support\Str::limit($room->description ?? 'An exquisite room offering premium comfort and luxury for your stay.', 100) }}
                        </p>

                        @if($isActive && $expiryDate)
                            <div class="py-1 mb-2">
                                <p class="text-xs font-normal text-[#964B00]">
                                    Offer Ends: {{ Carbon::parse($expiryDate)->format('M d, Y') }}
                                </p>
                            </div>
                        @endif

                        <div class="flex flex-wrap gap-x-4 gap-y-3 text-gray-600 text-sm mb-4 pt-3 border-t border-gray-200">

                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">group</span>
                                <span class="text-sm text-gray-600">{{ $room->accommodates }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">bed</span>
                                <span class="text-sm">{{ $room->beds }}</span>
                            </div>
                        </div>

                        <div class="mt-auto flex justify-between items-center">
                            <a href="{{ url('room_details', $room->id) }}" class="text-[#964B00] font-bold text-xs uppercase tracking-widest hover:text-black transition-colors">
                                View details
                            </a>
                            <button type="button"
                                onclick="location.href='{{ route('booking.dates', ['room_id' => $room->id, 'type' => 'room']) }}'"
                                class="bg-[#964B00] px-8 py-3 text-[10px] font-bold uppercase tracking-[0.2em] text-white hover:bg-black transition flex items-center justify-center shadow-lg shadow-orange-900/10">
                                Book now
                            </button>
                        </div>

                    </div>

                </div>
            @empty
                <div class="w-full text-center p-10 col-span-full">
                    <p class="text-lg text-gray-600">No rooms available at this time.</p>
                </div>
            @endforelse

        </div>
    </div>
</div>
