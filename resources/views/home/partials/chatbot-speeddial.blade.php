{{-- Modern Corporate Speed Dial Chatbot --}}
<div x-data="chatbotSpeedDial()" class="fixed bottom-6 right-6 z-50">
    {{-- Speed Dial Button --}}
    <button @click="toggle()" 
            class="group relative flex items-center justify-center w-14 h-14 bg-gradient-to-br from-[#63360D] to-[#8B4E14] hover:from-black hover:to-gray-800 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-110"
            :class="{ 'scale-110 shadow-2xl': isOpen }">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-300" :class="{ 'rotate-90': isOpen }">
            <path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"></path>
        </svg>
    </button>

    {{-- Chat Popover --}}
    <div x-show="isOpen" 
         x-cloak
         @click.outside="close()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         class="absolute bottom-16 right-0 w-96 h-[600px] bg-white border border-slate-200 shadow-2xl flex flex-col overflow-hidden">
        
        {{-- Header --}}
        <div class="px-4 py-3 bg-gradient-to-r from-[#63360D] to-[#8B4E14] border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <svg fill="white" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 512 512">
                            <path d="M168.64 23.253c4.608 1.814 8.768 4.8 12.544 8.747 6.293 6.528 11.605 15.872 15.659 26.944 4.074 11.136 6.72 23.467 7.722 35.84a107.824 107.824 0 0143.712-13.568l1.088-.085c18.56-1.494 36.907 1.856 52.907 10.112a103.091 103.091 0 016.336 3.626c1.067-12.138 3.669-24.192 7.68-35.072 4.053-11.093 9.365-20.416 15.637-26.965a35.628 35.628 0 0112.566-8.747c5.482-2.133 11.306-2.517 16.981-.896 8.555 2.432 15.893 7.851 21.675 15.723 5.29 7.19 9.258 16.405 11.968 27.456 4.906 19.925 5.76 46.144 2.453 77.76l1.131.853.554.406c16.15 12.288 27.392 29.802 33.344 50.133 9.28 31.723 4.608 67.307-11.392 87.211l-.384.448.043.064c8.896 16.256 14.293 33.429 15.445 51.2l.043.64c1.365 22.72-4.267 45.589-17.365 68.053l-.15.213.214.512c10.069 24.683 13.226 49.536 9.344 74.368l-.128.832a13.888 13.888 0 01-15.936 11.435 13.83 13.83 0 01-11.31-10.43 13.828 13.828 0 01-.21-5.399c3.562-22.038.213-44.139-10.24-66.624a13.713 13.713 0 01.853-13.163l.085-.128c12.886-19.712 18.219-39.04 17.067-58.027-.981-16.618-6.933-32.938-17.067-48.49a13.737 13.737 0 013.84-18.902l.192-.128c5.184-3.392 9.963-12.053 12.374-23.893a90.218 90.218 0 00-2.027-42.112c-4.373-14.933-12.373-27.392-23.573-35.904-12.694-9.685-29.504-14.357-50.774-13.013a13.93 13.93 0 01-13.482-7.915c-6.699-14.187-16.47-24.341-28.651-30.635a70.145 70.145 0 00-37.803-7.082c-26.56 2.112-49.984 17.088-56.96 35.968a13.91 13.91 0 01-13.013 9.066c-22.763.043-40.384 5.376-53.269 14.998-11.136 8.32-18.731 19.946-22.742 33.877a86.824 86.824 0 00-1.45 40.235c2.389 11.904 7.061 21.76 12.416 27.072l.17.149c4.523 4.416 5.483 11.307 2.326 16.747-7.68 13.269-13.419 33.045-14.358 52.053-1.066 21.717 3.968 40.576 15.339 54.101l.341.406a13.711 13.711 0 012.027 14.72c-12.288 26.368-16.064 48.042-11.989 65.109a13.91 13.91 0 01-27.072 6.357c-5.184-21.717-1.664-46.592 10.09-74.624l.299-.746-.17-.256a92.574 92.574 0 01-12.758-27.926l-.107-.405a122.965 122.965 0 01-3.776-38.08c.939-19.413 5.931-39.296 13.27-55.253l.256-.555-.043-.043c-6.25-8.917-10.88-20.33-13.44-32.96l-.107-.512a114.176 114.176 0 011.984-53.12c5.59-19.52 16.576-36.288 32.768-48.405 1.28-.96 2.624-1.92 3.968-2.816-3.392-31.851-2.538-58.24 2.39-78.293 2.709-11.051 6.698-20.267 11.989-27.456 5.76-7.851 13.099-13.27 21.653-15.723 5.675-1.621 11.52-1.259 17.003.896v.021z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-white">Booking Assistant</h3>
                        <p class="text-xs text-white/80">Online</p>
                    </div>
                </div>
                <button @click="close()" class="text-white/80 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Messages Container --}}
        <div id="chat-messages" class="flex-1 overflow-y-auto px-4 py-4 space-y-3 bg-slate-50">
            {{-- Initial Bot Message --}}
            <div class="flex gap-2 items-start">
                <div class="w-7 h-7 bg-slate-200 flex items-center justify-center shrink-0">
                    <svg stroke="none" fill="black" stroke-width="1.5" viewBox="0 0 24 24" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"></path>
                    </svg>
                </div>
                <div class="bg-white px-3 py-2 shadow-sm border border-slate-200 max-w-[80%]">
                    <p class="text-sm text-slate-800">Hi! How can I help you today?</p>
                </div>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="border-t border-slate-200 bg-white px-4 py-3">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input x-model="message" 
                       type="text" 
                       placeholder="Type your message..." 
                       class="flex-1 px-3 py-2 text-sm border border-slate-300 focus:border-[#A15D1A] focus:ring-1 focus:ring-[#A15D1A] outline-none transition-colors"
                       :disabled="isSending">
                <button type="submit" 
                        :disabled="!message.trim() || isSending"
                        class="px-4 py-2 bg-[#63360D]
                         text-white hover:bg-black transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </form>
            <p class="text-xs text-slate-500 mt-2 text-center">Powered by Meta-Qwen 2.5:3b</p>
        </div>
    </div>
</div>

<script>
function chatbotSpeedDial() {
    return {
        isOpen: false,
        message: '',
        isSending: false,
        
        toggle() {
            this.isOpen = !this.isOpen;
        },
        
        close() {
            this.isOpen = false;
        },
        
        async sendMessage() {
            if (!this.message.trim() || this.isSending) return;
            
            const userMessage = this.message.trim();
            this.appendUserMessage(userMessage);
            this.message = '';
            this.isSending = true;
            
            this.appendTypingIndicator();
            
            try {
                const response = await fetch('/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ message: userMessage })
                });
                
                if (!response.ok) throw new Error('Network response was not ok');
                
                await this.processStream(response);
            } catch (error) {
                console.error('Chat error:', error);
                this.removeTypingIndicator();
                this.appendErrorMessage('Failed to connect. Please try again.');
            } finally {
                this.isSending = false;
            }
        },
        
        appendUserMessage(text) {
            const container = document.getElementById('chat-messages');
            const div = document.createElement('div');
            div.className = 'flex gap-2 items-start justify-end';
            div.innerHTML = `
                <div class="bg-[#63360D] text-white px-3 py-2 max-w-[80%]">
                    <p class="text-sm">${this.escapeHtml(text)}</p>
                </div>`;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        },
        
        appendBotMessage(text = '') {
            const container = document.getElementById('chat-messages');
            const div = document.createElement('div');
            div.className = 'flex gap-2 items-start bot-message';
            div.innerHTML = `
                <div class="w-7 h-7 bg-slate-200 flex items-center justify-center shrink-0">
                    <svg stroke="none" fill="black" stroke-width="1.5" viewBox="0 0 24 24" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"></path>
                    </svg>
                </div>
                <div class="bg-white px-3 py-2 shadow-sm border border-slate-200 max-w-[80%]">
                    <p class="text-sm text-slate-800 bot-text">${this.escapeHtml(text)}</p>
                </div>`;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
            return div.querySelector('.bot-text');
        },
        
        appendTypingIndicator() {
            const container = document.getElementById('chat-messages');
            const div = document.createElement('div');
            div.className = 'flex gap-2 items-start typing-indicator';
            div.innerHTML = `
                <div class="w-7 h-7 bg-slate-200 flex items-center justify-center shrink-0">
                    <svg stroke="none" fill="black" stroke-width="1.5" viewBox="0 0 24 24" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"></path>
                    </svg>
                </div>
                <div class="bg-white px-3 py-2 shadow-sm border border-slate-200">
                    <div class="flex gap-1">
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                </div>`;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        },
        
        removeTypingIndicator() {
            const indicator = document.querySelector('.typing-indicator');
            if (indicator) indicator.remove();
        },
        
        appendErrorMessage(text) {
            const container = document.getElementById('chat-messages');
            const div = document.createElement('div');
            div.className = 'text-center';
            div.innerHTML = `<p class="text-xs text-red-500">${this.escapeHtml(text)}</p>`;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        },
        
        async processStream(response) {
            this.removeTypingIndicator();
            
            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';
            let botTextNode = null;
            
            while (true) {
                const { done, value } = await reader.read();
                if (done) break;
                
                buffer += decoder.decode(value, { stream: true });
                const lines = buffer.split('\n');
                buffer = lines.pop();
                
                for (const line of lines) {
                    if (!line.trim()) continue;
                    
                    try {
                        const data = JSON.parse(line);
                        
                        if (data.reply_chunk) {
                            if (!botTextNode) {
                                botTextNode = this.appendBotMessage(data.reply_chunk);
                            } else {
                                botTextNode.textContent += data.reply_chunk;
                            }
                            document.getElementById('chat-messages').scrollTop = document.getElementById('chat-messages').scrollHeight;
                        }
                        
                        if (data.done) {
                            reader.cancel();
                            return;
                        }
                        
                        if (data.error) {
                            this.appendErrorMessage(data.error);
                            reader.cancel();
                            return;
                        }
                    } catch (e) {
                        continue;
                    }
                }
            }
        },
        
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
