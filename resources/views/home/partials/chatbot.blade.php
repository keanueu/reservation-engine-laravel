{{-- Modern Speed Dial Chatbot - Luxury Resort Style --}}
<div x-data="chatbot()" 
     id="chatbot-widget" 
     class="fixed bottom-6 right-6 z-[100]" 
     x-init="initWidget()"
     @chatbot-quick-replies.window="quickReplies = $event.detail"
     x-cloak>
    
    <!-- Speed Dial Options -->
    <div class="flex flex-col-reverse items-center gap-3 mb-4" 
         x-show="dialOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10 scale-90"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-10 scale-90">
        
        {{-- AI Chat Option --}}
        <button @click="openChat()" 
                class="group relative flex items-center justify-center w-12 h-12 bg-white text-[#63360D] rounded-full shadow-lg hover:bg-[#63360D] hover:text-white transition-all duration-300">
            <span class="material-symbols-outlined">smart_toy</span>
            <span class="absolute right-16 bg-white text-[#63360D] text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none uppercase tracking-widest border border-gray-100">AI Assistant</span>
        </button>

        {{-- Book Now Option --}}
        <a href="{{ route('booking.dates') }}" 
           class="group relative flex items-center justify-center w-12 h-12 bg-white text-[#63360D] rounded-full shadow-lg hover:bg-[#63360D] hover:text-white transition-all duration-300">
            <span class="material-symbols-outlined">calendar_month</span>
            <span class="absolute right-16 bg-white text-[#63360D] text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none uppercase tracking-widest border border-gray-100">Book Now</span>
        </a>

        {{-- Call Us Option --}}
        <a href="tel:+639123456789" 
           class="group relative flex items-center justify-center w-12 h-12 bg-white text-[#63360D] rounded-full shadow-lg hover:bg-[#63360D] hover:text-white transition-all duration-300">
            <span class="material-symbols-outlined">call</span>
            <span class="absolute right-16 bg-white text-[#63360D] text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none uppercase tracking-widest border border-gray-100">Call Resort</span>
        </a>

        {{-- Weather Option --}}
        <button @click="window.scrollTo({top: 0, behavior: 'smooth'})" 
                class="group relative flex items-center justify-center w-12 h-12 bg-white text-[#63360D] rounded-full shadow-lg hover:bg-[#63360D] hover:text-white transition-all duration-300">
            <span class="material-symbols-outlined">wb_sunny</span>
            <span class="absolute right-16 bg-white text-[#63360D] text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none uppercase tracking-widest border border-gray-100">Weather</span>
        </button>
    </div>

    <!-- Main FAB Trigger -->
    <button @click="dialOpen = !dialOpen" 
            class="relative w-16 h-16 rounded-full flex items-center justify-center shadow-2xl transition-all duration-500 overflow-hidden group"
            :class="dialOpen ? 'bg-black rotate-45' : 'bg-[#63360D] hover:scale-110'">
        <div class="absolute inset-0 bg-gradient-to-tr from-black/20 to-transparent"></div>
        <span class="material-symbols-outlined text-3xl text-white transition-all duration-500" 
              x-text="dialOpen ? 'add' : 'chat'"></span>
        
        <!-- Pulse effect when closed -->
        <span x-show="!dialOpen" class="absolute inset-0 rounded-full bg-white/20 animate-ping"></span>
    </button>

    <!-- Chat Window (Modern Modal Style) -->
    <div x-show="chatOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         class="fixed md:absolute bottom-20 md:bottom-24 right-0 md:-right-2 w-[calc(100vw-2rem)] md:w-[400px] h-[600px] max-h-[80vh] bg-white border border-gray-100 shadow-2xl overflow-hidden flex flex-col z-50 rounded-2xl">
        
        <!-- Header -->
        <div class="bg-black text-white px-6 py-5 flex justify-between items-center relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-[#63360D]/20 to-transparent"></div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="relative">
                    <div class="w-10 h-10 bg-[#63360D] rounded-xl flex items-center justify-center shadow-lg">
                        <span class="material-symbols-outlined text-white text-xl">smart_toy</span>
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 border-2 border-black rounded-full"></div>
                </div>
                <div>
                    <h3 class="font-bold text-sm tracking-tight">CABANAS ASSISTANT</h3>
                    <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Always Online</p>
                </div>
            </div>
            <button @click="chatOpen = false" class="text-gray-400 hover:text-white transition-colors relative z-10">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <!-- Messages Area -->
        <div id="chatbot-messages" 
             class="flex-1 overflow-y-auto p-6 bg-gray-50/50 flex flex-col gap-6 scroll-smooth">
            <div class="flex justify-start">
                <div class="bg-white border border-gray-100 text-black text-sm py-4 px-5 rounded-2xl rounded-tl-none shadow-sm max-w-[85%] leading-relaxed">
                    Welcome to <span class="font-bold">Cabanas Resort</span>! I'm your digital concierge. How may I assist with your hidden paradise experience today?
                </div>
            </div>
            <!-- Dynamic messages -->
        </div>

        <!-- Footer / Input -->
        <div class="p-4 bg-white border-t border-gray-100">
            {{-- Quick Replies --}}
            <div class="flex gap-2 overflow-x-auto pb-4 scrollbar-hide mb-2">
                <template x-for="reply in quickReplies" :key="reply">
                    <button @click="sendQuick(reply)" 
                            class="whitespace-nowrap bg-gray-50 hover:bg-[#63360D] hover:text-white text-gray-600 transition-all duration-300 text-[10px] font-bold uppercase tracking-widest px-4 py-2 border border-gray-100 rounded-lg shadow-sm">
                        <span x-text="reply"></span>
                    </button>
                </template>
            </div>

            {{-- Form --}}
            <form @submit.prevent="sendMessage()" class="flex items-center gap-3 bg-gray-50 rounded-xl p-2 border border-gray-100 focus-within:border-[#63360D] transition-colors">
                <input type="text" 
                       x-model="userInput" 
                       placeholder="Ask about rooms, amenities..." 
                       class="flex-1 bg-transparent border-none focus:ring-0 text-sm px-3 py-2 text-black">
                <button type="submit" 
                        class="w-10 h-10 bg-black text-white rounded-lg flex items-center justify-center hover:bg-[#63360D] transition-all shadow-md group">
                    <span class="material-symbols-outlined text-lg group-hover:translate-x-0.5 transition-transform">send</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function chatbot() {
    return {
        dialOpen: false,
        chatOpen: false,
        userInput: '',
        quickReplies: ['Book Room', 'Amenities', 'Check Availability', 'Contact Info'],
        
        initWidget() {
            // Existing logic can be hooked here or left in chatbot.js
            // If chatbot.js is using IDs, we should keep them or update it.
            setTimeout(() => {
                document.getElementById('chatbot-widget').style.display = 'block';
            }, 1000);
        },

        openChat() {
            this.dialOpen = false;
            this.chatOpen = true;
            // Focus input after transition
            setTimeout(() => {
                const input = document.querySelector('#chatbot-widget input');
                if(input) input.focus();
            }, 400);
        },

        sendMessage() {
            if (!this.userInput.trim()) return;
            // Trigger existing sendMessage logic from chatbot.js if possible
            if (window.sendChatbotMessage) {
                window.sendChatbotMessage(this.userInput);
                this.userInput = '';
            } else {
                // Fallback: manually trigger the form submit event
                const form = document.getElementById('chatbot-form');
                const input = document.getElementById('chatbot-input');
                if (input) {
                    input.value = this.userInput;
                    this.userInput = '';
                    form.dispatchEvent(new Event('submit'));
                }
            }
        },

        sendQuick(reply) {
            this.userInput = reply;
            this.sendMessage();
        }
    }
}
</script>

<style>
    [x-cloak] { display: none !important; }
    
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

    #chatbot-messages::-webkit-scrollbar {
        width: 4px;
    }
    #chatbot-messages::-webkit-scrollbar-track {
        background: transparent;
    }
    #chatbot-messages::-webkit-scrollbar-thumb {
        background: #e5e7eb;
        border-radius: 10px;
    }
</style>
