<section class="relative bg-white font-[Manrope] py-20 overflow-hidden">

    {{-- Logo watermark — centered, large, very faint --}}
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none">
        <img src="{{ asset('LOGO-FINAL.png') }}"
             class="w-[900px] h-[900px] object-contain opacity-[0.07]"
             aria-hidden="true" />
    </div>

    {{-- Content --}}
    <div class="relative max-w-6xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- Left: Text --}}
            <div data-reveal>
                <p class="text-sm font-semibold tracking-[.25em] uppercase mb-3" style="color:#964B00;">About Us</p>

                <h2 class="text-4xl md:text-5xl leading-tight mb-5 text-gray-900" style="font-family:'Playfair Display',serif;">
                    Welcome to Cabanas<br>
                    Beach Resort &amp; Hotel
                </h2>

                <p class="text-sm text-gray-600 leading-relaxed mb-6 max-w-md">
                    The Cabanas Family Resort is a family-owned getaway built on land that has
                    been passed down through generations. Nestled in Tambobong, Dasol,
                    Pangasinan, we take pride in sharing our hidden gem with you.
                </p>

                {{-- Stats row --}}
                <div class="grid grid-cols-3 gap-4 mb-8">
                    @php $stats = [['15+','Room Types'],['5★','Guest Rating'],['10+','Years Open']]; @endphp
                    @foreach($stats as [$val,$lbl])
                        <div class="border border-gray-100 px-4 py-3 text-center shadow-sm">
                            <p class="text-xl font-bold" style="color:#964B00;">{{ $val }}</p>
                            <p class="text-[11px] font-semibold text-gray-400 tracking-wide mt-0.5">{{ $lbl }}</p>
                        </div>
                    @endforeach
                </div>

                <a href="{{ url('/home/amenities') }}"
                   class="inline-flex items-center gap-2 text-sm font-semibold text-gray-800 border-b-2 pb-0.5 transition-colors hover:text-[#964B00] hover:border-[#964B00]"
                   style="border-color:#964B00;">
                    Learn More
                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                </a>
            </div>

            {{-- Right: Images --}}
            <div class="grid grid-cols-2 gap-4" data-reveal data-reveal-delay="2">
                {{-- Tall left image --}}
                <div class="col-span-2 h-56 overflow-hidden shadow-md">
                    <img src="{{ asset('images/1758952332.jpg') }}"
                         class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                         alt="Cabanas Resort" />
                </div>
                {{-- Two smaller images --}}
                <div class="h-44 overflow-hidden shadow-md">
                    <img src="{{ asset('images/1758952350.jpg') }}"
                         class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                         alt="Cabanas Beach" />
                </div>
                <div class="h-44 overflow-hidden shadow-md relative">
                    <img src="{{ asset('images/1758952017.jpg') }}"
                         class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                         alt="Cabanas Sign" />
                    {{-- Location badge --}}
                    <div class="absolute bottom-0 left-0 right-0 px-3 py-2" style="background:rgba(150,75,0,0.85);">
                        <p class="text-white text-xs font-semibold tracking-wide">Tambobong, Dasol</p>
                        <p class="text-white/70 text-[11px]">Pangasinan, Philippines</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
