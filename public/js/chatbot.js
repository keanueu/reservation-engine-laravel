/**
 * Modern Chatbot Logic
 * Optimized for the new Speed Dial UI with natural word streaming UX
 */

document.addEventListener('DOMContentLoaded', () => {
    const messagesContainer = document.getElementById('chatbot-messages');
    
    // Check if we are in the guest home view where the widget exists
    if (!messagesContainer) return;

    let lastMessageCount = 0;

    // Helper: Scroll to bottom
    const scrollToBottom = () => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    // Helper: Render a message bubble with dynamic progressive typing effect
    const renderMessage = (msg, animate = false) => {
        const isUser = msg.sender === 'user';
        const wrapper = document.createElement('div');
        wrapper.className = `flex ${isUser ? 'justify-end' : 'justify-start'} animate-in fade-in slide-in-from-bottom-2 duration-300`;
        
        const bubble = document.createElement('div');
        if (isUser) {
            bubble.className = 'bg-black text-white text-sm py-3 px-4 rounded-2xl rounded-tr-none shadow-md max-w-[85%] leading-relaxed';
            bubble.textContent = msg.message;
        } else {
            bubble.className = 'bg-white border border-gray-100 text-black text-sm py-4 px-5 rounded-2xl rounded-tl-none shadow-sm max-w-[85%] leading-relaxed whitespace-pre-wrap';
            
            if (animate) {
                // Progressive streaming word-by-word rendering
                const words = msg.message.split(' ');
                let currentWordIndex = 0;
                bubble.textContent = '';
                
                const timer = setInterval(() => {
                    if (currentWordIndex < words.length) {
                        bubble.textContent += (currentWordIndex === 0 ? '' : ' ') + words[currentWordIndex];
                        currentWordIndex++;
                        scrollToBottom();
                    } else {
                        clearInterval(timer);
                    }
                }, 30); // 30ms per word is the perfect natural reading speed
            } else {
                bubble.textContent = msg.message;
            }
        }
        
        wrapper.appendChild(bubble);
        return wrapper;
    };

    // Helper: Append messages
    const appendMessages = (messages, isHistory = false, animate = false) => {
        if (isHistory) {
            const welcomeMsg = messagesContainer.firstElementChild;
            messagesContainer.innerHTML = '';
            if (welcomeMsg) messagesContainer.appendChild(welcomeMsg);
        }
        
        messages.forEach(msg => {
            messagesContainer.appendChild(renderMessage(msg, animate));
        });
        scrollToBottom();
    };

    // Core: Fetch messages from API
    window.fetchChatbotMessages = async () => {
        try {
            const res = await fetch('/chat/fetch');
            const data = await res.json();
            if (data.messages && data.messages.length > lastMessageCount) {
                const newMessages = data.messages.slice(lastMessageCount);
                const shouldAnimate = lastMessageCount > 0;
                
                appendMessages(newMessages, false, shouldAnimate);
                lastMessageCount = data.messages.length;

                // Sync suggested quick replies of the latest AI message
                const lastAiMsg = [...newMessages].reverse().find(msg => msg.sender === 'bot' || msg.sender === 'ai' || msg.sender === 'admin');
                if (lastAiMsg && lastAiMsg.meta && lastAiMsg.meta.quick_replies && lastAiMsg.meta.quick_replies.length > 0) {
                    const event = new CustomEvent('chatbot-quick-replies', { detail: lastAiMsg.meta.quick_replies });
                    window.dispatchEvent(event);
                }
            }
        } catch (error) {
            console.error('Chatbot: Fetch failed', error);
        }
    };

    // Core: Send message to API
    window.sendChatbotMessage = async (text) => {
        if (!text.trim()) return;

        // Optimistic render
        appendMessages([{ sender: 'user', message: text }]);
        lastMessageCount++;

        // Typing indicator
        const typing = document.createElement('div');
        typing.id = 'chatbot-typing';
        typing.className = 'flex justify-start animate-pulse';
        typing.innerHTML = `<div class="bg-gray-100 text-[#63360D] text-[10px] font-bold uppercase px-3 py-1.5 rounded-full tracking-wider">Assistant is thinking...</div>`;
        messagesContainer.appendChild(typing);
        scrollToBottom();

        try {
            const res = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ message: text })
            });
            const data = await res.json();
            
            document.getElementById('chatbot-typing')?.remove();

            if (data.success && data.messages && data.messages.length > 0) {
                const aiMsg = data.messages[data.messages.length - 1];
                if (aiMsg.sender === 'bot' || aiMsg.sender === 'ai' || aiMsg.sender === 'admin') {
                    appendMessages([aiMsg], false, true);
                    lastMessageCount++;

                    // Sync suggested quick replies of the latest AI message
                    if (aiMsg.meta && aiMsg.meta.quick_replies && aiMsg.meta.quick_replies.length > 0) {
                        const event = new CustomEvent('chatbot-quick-replies', { detail: aiMsg.meta.quick_replies });
                        window.dispatchEvent(event);
                    }
                }
            }
        } catch (error) {
            document.getElementById('chatbot-typing')?.remove();
            console.error('Chatbot: Send failed', error);
        }
    };

    // Start polling when window is visible and open
    setInterval(() => {
        const messagesArea = document.getElementById('chatbot-messages');
        if (messagesArea && messagesArea.offsetParent !== null && !document.hidden) {
            window.fetchChatbotMessages();
        }
    }, 15000);

    // Initial fetch
    window.fetchChatbotMessages();
});
