@props(['style' => session('flash.bannerStyle', 'success'), 'message' => session('flash.banner')])

<div x-data="{{ json_encode(['show' => true, 'style' => $style, 'message' => $message]) }}"
     class="fixed top-24 md:top-28 right-4 md:right-8 z-[100] max-w-sm w-full"
     style="display: none;"
     x-show="show && message"
     x-init="setTimeout(() => show = false, 8000)"
     x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="opacity-0 translate-x-12"
     x-transition:enter-end="opacity-100 translate-x-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-90"
     x-on:banner-message.window="
         style = event.detail.style;
         message = event.detail.message;
         show = true;
         setTimeout(() => show = false, 8000);
     ">
    
    <div class="relative overflow-hidden rounded-2xl shadow-xl border border-white/10"
         :class="{ 
            'bg-[#63360D] text-white': style == 'success', 
            'bg-red-700 text-white': style == 'danger', 
            'bg-amber-600 text-white': style == 'warning', 
            'bg-[#1a1a1a] text-white': style != 'success' && style != 'danger' && style != 'warning'
         }">
        
        {{-- Progress Bar (Autoclose timer) --}}
        <div class="absolute bottom-0 left-0 h-1 bg-white/30 w-full animate-[progress_8s_linear_forwards]"></div>

        <div class="p-4 pr-12 flex items-start gap-4">
            {{-- Icon --}}
            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-xl" 
                      x-text="style == 'success' ? 'check_circle' : (style == 'danger' ? 'dangerous' : 'info')"></span>
            </div>

            {{-- Text --}}
            <div class="flex-1 min-w-0 pt-1">
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mb-0.5" x-text="style"></p>
                <p class="text-sm font-semibold leading-relaxed" x-text="message"></p>
            </div>

            {{-- Close Button --}}
            <button @click="show = false" class="absolute top-4 right-4 text-white/50 hover:text-white transition-colors">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes progress {
        from { width: 100%; }
        to { width: 0%; }
    }
</style>
