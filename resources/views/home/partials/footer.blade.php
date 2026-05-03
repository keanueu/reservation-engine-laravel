<footer style="background:#1a0a00;">
    {{-- Top wave --}}
    <div style="background:var(--off-white,#faf9f7); margin-bottom:-2px;">
        <svg viewBox="0 0 1440 48" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="display:block;width:100%;height:48px;">
            <path d="M0,24 C240,48 480,0 720,24 C960,48 1200,0 1440,24 L1440,48 L0,48 Z" fill="#1a0a00"/>
        </svg>
    </div>

    <div class="max-w-6xl mx-auto px-6 pt-16 pb-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-14">

            {{-- Brand --}}
            <div class="lg:col-span-1">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 overflow-hidden border-2 border-white/20">
                        <img src="{{ asset('LOGO-FINAL.png') }}" alt="Cabanas" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <p class="text-base tracking-widest text-white">CABANAS</p>
                        <p class="text-[10px] tracking-[.25em] text-white/40 mt-0.5">HOTEL & RESORT</p>
                    </div>
                </div>
                <p class="text-sm text-white/60 leading-relaxed mb-6">
                    A family-owned paradise in Tambobong, Dasol, Pangasinan — where beachfront comfort meets maritime adventure.
                </p>
                {{-- Trust badges --}}
                <div class="space-y-2 mb-6">
                    @php $trust = [
                        ['icon'=>'verified','label'=>'Verified & Trusted'],
                        ['icon'=>'lock','label'=>'Secure Payments']
                    ]; @endphp
                    @foreach($trust as $t)
                        <div class="flex items-center gap-2 text-white/50">
                            <span class="material-symbols-outlined text-base flex-shrink-0 text-[#964B00]">{{ $t['icon'] }}</span>
                            <span class="text-xs">{{ $t['label'] }}</span>
                        </div>
                    @endforeach
                </div>
                {{-- Socials --}}
                <div class="flex gap-3">
                    @php $socials = [
                        ['label'=>'Facebook','icon'=>'facebook'],
                        ['label'=>'Instagram','icon'=>'photo_camera']
                    ]; @endphp
                    @foreach($socials as $s)
                        <a href="#" aria-label="{{ $s['label'] }}"
                           class="w-9 h-9 flex items-center justify-center border border-white/15 text-white/50 hover:text-white hover:border-white/40 transition-all">
                            <span class="material-symbols-outlined text-base">{{ $s['icon'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-xs tracking-[.25em] uppercase text-white mb-6">Quick links</h3>
                <ul class="space-y-3">
                    @php $links = [['Home',url('/')],['Rooms',url('/home/rooms')],['Book Now',url('/home/roomcart')],['Amenities',url('/home/amenities')],['Contact',url('/home/contact')]]; @endphp
                    @foreach($links as [$label,$url])
                        <li>
                            <a href="{{ $url }}" class="flex items-center gap-2 text-sm text-white/55 hover:text-white transition-colors group">
                                <span class="w-0 h-px bg-[#964B00] transition-all duration-300 group-hover:w-4"></span>
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h3 class="text-xs tracking-[.25em] uppercase text-white mb-6">Contact</h3>
                <ul class="space-y-4">
                    @php $contacts = [
                        ['icon'=>'location_on','text'=>'Tambobong, Dasol, Pangasinan'],
                        ['icon'=>'call','text'=>'+63 912 345 6789'],
                        ['icon'=>'mail','text'=>'cabanasresort@gmail.com']
                    ]; @endphp
                    @foreach($contacts as $c)
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-base mt-0.5 flex-shrink-0 text-[#964B00]">{{ $c['icon'] }}</span>
                            <span class="text-sm text-white/55 leading-relaxed">{{ $c['text'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Newsletter --}}
            <div>
                <h3 class="text-xs tracking-[.25em] uppercase text-white mb-6">Stay updated</h3>
                <p class="text-sm text-white/55 mb-5 leading-relaxed">Get exclusive offers, seasonal promos, and resort news.</p>
                <form class="space-y-3" onsubmit="return false;">
                    <input type="email" placeholder="your@email.com"
                           class="w-full px-4 py-3 text-sm text-white placeholder-white/30 outline-none transition-all"
                           style="background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.12);"
                           onfocus="this.style.borderColor='#964B00'" onblur="this.style.borderColor='rgba(255,255,255,0.12)'">
                    <button type="submit"
                            class="w-full btn-primary py-3 text-xs tracking-widest uppercase">
                        Subscribe
                    </button>
                </form>
                {{-- Safe to sail badge --}}
                <div class="mt-5 flex items-center gap-2 px-4 py-3" style="background:rgba(150,75,0,0.15); border:1px solid rgba(150,75,0,0.3);">
                    <span class="w-2 h-2 bg-green-400 animate-pulse flex-shrink-0"></span>
                    <span class="text-xs text-white/70">Safe to Sail — Conditions Normal</span>
                </div>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="border-t pt-8 flex flex-col md:flex-row justify-between items-center gap-4" style="border-color:rgba(255,255,255,0.08);">
            <p class="text-xs text-white/35">
                © {{ date('Y') }} Cabanas Beach Resort. All Rights Reserved.
            </p>
            <div class="flex items-center gap-6 text-xs text-white/35">
                <a href="#" class="hover:text-white/70 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white/70 transition-colors">Terms of Service</a>
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base text-[#964B00]">location_on</span>
                    Dasol, Pangasinan
                </span>
            </div>
        </div>
    </div>
</footer>
