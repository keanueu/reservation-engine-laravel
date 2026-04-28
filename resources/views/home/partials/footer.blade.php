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
                        <p class="text-base font-bold tracking-widest text-white" style="font-family:'Playfair Display',serif;">CABANAS</p>
                        <p class="text-[10px] font-semibold tracking-[.25em] text-white/40 mt-0.5">HOTEL & RESORT</p>
                    </div>
                </div>
                <p class="text-sm text-white/60 leading-relaxed mb-6">
                    A family-owned paradise in Tambobong, Dasol, Pangasinan — where beachfront comfort meets maritime adventure.
                </p>
                {{-- Trust badges --}}
                <div class="space-y-2 mb-6">
                    @php $trust = [['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','label'=>'Verified & Trusted'],['icon'=>'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z','label'=>'Secure Payments']]; @endphp
                    @foreach($trust as $t)
                        <div class="flex items-center gap-2 text-white/50">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $t['icon'] }}"/></svg>
                            <span class="text-xs font-semibold">{{ $t['label'] }}</span>
                        </div>
                    @endforeach
                </div>
                {{-- Socials --}}
                <div class="flex gap-3">
                    @php $socials = [['label'=>'Facebook','d'=>'M22 12a10 10 0 1 0-11.6 9.9v-7h-2v-3h2v-2.3c0-2 1.2-3.1 3-3.1.9 0 1.8.1 1.8.1v2h-1c-1 0-1.3.6-1.3 1.2V12h2.3l-.4 3h-1.9v7A10 10 0 0 0 22 12z'],['label'=>'Instagram','d'=>'M7 2C4.2 2 2 4.2 2 7v10c0 2.8 2.2 5 5 5h10c2.8 0 5-2.2 5-5V7c0-2.8-2.2-5-5-5H7zm10 2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h10zm-5 3a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 2a3 3 0 1 1 0 6 3 3 0 0 1 0-6zm4.8-.8a1.2 1.2 0 1 0 0-2.4 1.2 1.2 0 0 0 0 2.4z']]; @endphp
                    @foreach($socials as $s)
                        <a href="#" aria-label="{{ $s['label'] }}"
                           class="w-9 h-9 flex items-center justify-center border border-white/15 text-white/50 hover:text-white hover:border-white/40 transition-all">
                            <svg fill="currentColor" class="w-4 h-4" viewBox="0 0 24 24"><path d="{{ $s['d'] }}"/></svg>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-xs font-bold tracking-[.25em] uppercase text-white mb-6">Quick Links</h3>
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
                <h3 class="text-xs font-bold tracking-[.25em] uppercase text-white mb-6">Contact</h3>
                <ul class="space-y-4">
                    @php $contacts = [['M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z','Tambobong, Dasol, Pangasinan'],['M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498A1 1 0 0121 15v2a2 2 0 01-2 2h-1C9.716 19 3 12.284 3 6V5z','+63 912 345 6789'],['M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z','cabanasresort@gmail.com']]; @endphp
                    @foreach($contacts as [$icon,$text])
                        <li class="flex items-start gap-3">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-[#964B00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                            <span class="text-sm text-white/55 leading-relaxed">{{ $text }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Newsletter --}}
            <div>
                <h3 class="text-xs font-bold tracking-[.25em] uppercase text-white mb-6">Stay Updated</h3>
                <p class="text-sm text-white/55 mb-5 leading-relaxed">Get exclusive offers, seasonal promos, and resort news.</p>
                <form class="space-y-3" onsubmit="return false;">
                    <input type="email" placeholder="your@email.com"
                           class="w-full px-4 py-3 text-sm font-medium text-white placeholder-white/30 outline-none transition-all"
                           style="background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.12);"
                           onfocus="this.style.borderColor='#964B00'" onblur="this.style.borderColor='rgba(255,255,255,0.12)'">
                    <button type="submit"
                            class="w-full btn-primary py-3 text-xs font-bold tracking-widest uppercase">
                        Subscribe
                    </button>
                </form>
                {{-- Safe to sail badge --}}
                <div class="mt-5 flex items-center gap-2 px-4 py-3" style="background:rgba(150,75,0,0.15); border:1px solid rgba(150,75,0,0.3);">
                    <span class="w-2 h-2 bg-green-400 animate-pulse flex-shrink-0"></span>
                    <span class="text-xs font-semibold text-white/70">Safe to Sail — Conditions Normal</span>
                </div>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="border-t pt-8 flex flex-col md:flex-row justify-between items-center gap-4" style="border-color:rgba(255,255,255,0.08);">
            <p class="text-xs font-medium text-white/35">
                © {{ date('Y') }} Cabanas Beach Resort. All Rights Reserved.
            </p>
            <div class="flex items-center gap-6 text-xs font-medium text-white/35">
                <a href="#" class="hover:text-white/70 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white/70 transition-colors">Terms of Service</a>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#964B00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.24 7.76l-2.12 6.36-6.36 2.12 2.12-6.36 6.36-2.12z"/></svg>
                    Dasol, Pangasinan
                </span>
            </div>
        </div>
    </div>
</footer>
