<!-- Chatbot Floating Widget -->
<div id="chatbot-widget" class="fixed bottom-6 right-6 z-[100] font-[Inter]" style="display: none;">
    
    <!-- Chat Window (Hidden by default) -->
    <div id="chatbot-window" class="hidden flex-col bg-white border border-gray-200 shadow-2xl w-80 sm:w-96 h-[500px] max-h-[80vh] overflow-hidden transition-all duration-300 transform translate-y-4 opacity-0 mb-4 rounded-lg">
        
        <!-- Header -->
        <div class="bg-[#964B00] text-white px-4 py-3 flex justify-between items-center shadow-md z-10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-sm">support_agent</span>
                </div>
                <div>
                    <h3 class="font-medium text-sm ">Guest assistant</h3>
                    <p class="text-[10px] text-white/80">Online</p>
                </div>
            </div>
            <button id="chatbot-close-btn" class="text-white/80 hover:text-white transition-colors focus:outline-none">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <!-- Messages Area -->
        <div id="chatbot-messages" class="flex-1 overflow-y-auto p-4 bg-gray-50 flex flex-col gap-3 scroll-smooth">
            <!-- Initial Welcome Message -->
            <div class="flex justify-start">
                <div class="bg-white border border-gray-100 text-black text-sm py-2 px-3 rounded-2xl rounded-tl-sm shadow-sm max-w-[85%] leading-relaxed">
                    Hello! I'm your digital guest assistant. How can I help you make your stay extraordinary today?
                </div>
            </div>
            <!-- Dynamic messages will be appended here -->
        </div>

        <!-- Quick Actions -->
        <div class="px-4 py-2 bg-gray-50 border-t border-gray-100 flex gap-2 overflow-x-auto scrollbar-hide shrink-0 pb-3" id="chatbot-quick-replies">
            <button class="chatbot-quick-btn whitespace-nowrap bg-white border border-[#964B00]/30 text-[#964B00] hover:bg-[#964B00] hover:text-white transition-colors text-[10px] px-3 py-1.5 rounded-full shadow-sm font-medium">Book a room</button>
            <button class="chatbot-quick-btn whitespace-nowrap bg-white border border-[#964B00]/30 text-[#964B00] hover:bg-[#964B00] hover:text-white transition-colors text-[10px] px-3 py-1.5 rounded-full shadow-sm font-medium">Amenities</button>
            <button class="chatbot-quick-btn whitespace-nowrap bg-white border border-[#964B00]/30 text-[#964B00] hover:bg-[#964B00] hover:text-white transition-colors text-[10px] px-3 py-1.5 rounded-full shadow-sm font-medium">Contact front desk</button>
        </div>

        <!-- Input Area -->
        <div class="bg-white px-4 py-3 border-t border-gray-200">
            <form id="chatbot-form" class="flex items-center gap-2">
                <input type="text" id="chatbot-input" autocomplete="off" placeholder="Type a message..." class="flex-1 text-sm border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:border-[#964B00] focus:ring-1 focus:ring-[#964B00] transition-colors">
                <button type="submit" class="bg-[#964B00] text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-[#7A3C00] transition-colors focus:outline-none shrink-0 shadow-md">
                    <span class="material-symbols-outlined text-[15px] -ml-0.5 mt-0.5">send</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Toggle Button -->
    <button id="chatbot-toggle-btn" class="w-14 h-14 bg-[#964B00] text-white rounded-full flex items-center justify-center shadow-[0_8px_30px_rgb(0,0,0,0.12)] hover:bg-black hover:scale-105 transition-all duration-300 focus:outline-none group float-right">
        <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform duration-300" id="chatbot-toggle-icon">chat</span>
    </button>
</div>

<style>
    /* Hide scrollbar for quick replies but allow scroll */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    /* Animation classes */
    .chatbot-enter {
        opacity: 1 !important;
        transform: translateY(0) !important;
        display: flex !important;
    }
</style>
