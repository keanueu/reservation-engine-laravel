document.addEventListener('DOMContentLoaded', () => {
    const chatbotWidget = document.getElementById('chatbot-widget');
    const chatbotWindow = document.getElementById('chatbot-window');
    const toggleBtn = document.getElementById('chatbot-toggle-btn');
    const closeBtn = document.getElementById('chatbot-close-btn');
    const toggleIcon = document.getElementById('chatbot-toggle-icon');
    const messagesContainer = document.getElementById('chatbot-messages');
    const chatForm = document.getElementById('chatbot-form');
    const chatInput = document.getElementById('chatbot-input');
    const quickReplyBtns = document.querySelectorAll('.chatbot-quick-btn');

    let isOpen = false;
    let pollInterval = null;
    let lastMessageCount = 0;

    // Show widget with a small delay so it doesn't pop instantly on load
    setTimeout(() => {
        chatbotWidget.style.display = 'block';
    }, 1000);

    function toggleChat() {
        isOpen = !isOpen;
        if (isOpen) {
            chatbotWindow.classList.add('chatbot-enter');
            toggleIcon.textContent = 'close';
            chatInput.focus();
            fetchMessages(); // Fetch on open to get latest
            startPolling();
        } else {
            chatbotWindow.classList.remove('chatbot-enter');
            toggleIcon.textContent = 'chat';
            stopPolling();
        }
    }

    toggleBtn.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', toggleChat);

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function renderMessage(msg) {
        const isUser = msg.sender === 'user';
        
        const wrapper = document.createElement('div');
        wrapper.className = `flex ${isUser ? 'justify-end' : 'justify-start'}`;
        
        const bubble = document.createElement('div');
        if (isUser) {
            bubble.className = 'bg-[#964B00] text-white text-xs py-2 px-3 rounded-2xl rounded-tr-sm shadow-sm max-w-[85%] leading-relaxed';
        } else {
            bubble.className = 'bg-white border border-gray-100 text-gray-800 text-xs py-2 px-3 rounded-2xl rounded-tl-sm shadow-sm max-w-[85%] leading-relaxed whitespace-pre-wrap';
        }
        
        bubble.textContent = msg.message;
        wrapper.appendChild(bubble);
        return wrapper;
    }

    function appendMessages(messages, isHistory = false) {
        if (isHistory) {
            // Keep the initial welcome message, append the rest
            const welcomeMsg = messagesContainer.firstElementChild;
            messagesContainer.innerHTML = '';
            if (welcomeMsg) messagesContainer.appendChild(welcomeMsg);
            
            messages.forEach(msg => {
                messagesContainer.appendChild(renderMessage(msg));
            });
        } else {
            messages.forEach(msg => {
                messagesContainer.appendChild(renderMessage(msg));
            });
        }
        scrollToBottom();
    }

    async function fetchMessages() {
        try {
            const res = await fetch('/api/chat/fetch');
            const data = await res.json();
            if (data.messages) {
                if (data.messages.length > lastMessageCount) {
                    appendMessages(data.messages, true);
                    lastMessageCount = data.messages.length;
                }
            }
        } catch (error) {
            console.error('Failed to fetch messages:', error);
        }
    }

    async function sendMessage(text) {
        if (!text.trim()) return;

        // Optimistically render user message
        const tempMsg = { sender: 'user', message: text };
        appendMessages([tempMsg]);
        chatInput.value = '';
        lastMessageCount++; // Increment optimistically

        // Show typing indicator (optional but good UX)
        const typingIndicator = document.createElement('div');
        typingIndicator.id = 'typing-indicator';
        typingIndicator.className = 'flex justify-start';
        typingIndicator.innerHTML = `
            <div class="bg-white border border-gray-100 text-gray-500 text-[10px] py-1.5 px-3 rounded-full shadow-sm">
                typing...
            </div>
        `;
        messagesContainer.appendChild(typingIndicator);
        scrollToBottom();

        try {
            const res = await fetch('/api/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ message: text })
            });
            const data = await res.json();
            
            // Remove typing indicator
            const indicator = document.getElementById('typing-indicator');
            if (indicator) indicator.remove();

            if (data.success && data.messages) {
                // The API returns [userMessage, aiMessage]. We already rendered the user message optimistically.
                // We just need to render the AI message (index 1).
                if (data.messages.length > 1) {
                    appendMessages([data.messages[1]]);
                    lastMessageCount++; 
                }
            }
        } catch (error) {
            const indicator = document.getElementById('typing-indicator');
            if (indicator) indicator.remove();
            console.error('Send failed:', error);
        }
    }

    async function sendQuickReply(option) {
        const tempMsg = { sender: 'user', message: option };
        appendMessages([tempMsg]);
        lastMessageCount++;

        const typingIndicator = document.createElement('div');
        typingIndicator.id = 'typing-indicator';
        typingIndicator.className = 'flex justify-start';
        typingIndicator.innerHTML = `
            <div class="bg-white border border-gray-100 text-gray-500 text-[10px] py-1.5 px-3 rounded-full shadow-sm">
                typing...
            </div>
        `;
        messagesContainer.appendChild(typingIndicator);
        scrollToBottom();

        try {
            const res = await fetch('/api/chat/quick-reply', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ option: option })
            });
            const data = await res.json();
            
            const indicator = document.getElementById('typing-indicator');
            if (indicator) indicator.remove();

            if (data.success && data.messages && data.messages.length > 1) {
                appendMessages([data.messages[1]]);
                lastMessageCount++;
            }
        } catch (error) {
            const indicator = document.getElementById('typing-indicator');
            if (indicator) indicator.remove();
            console.error('Quick reply failed:', error);
        }
    }

    chatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        sendMessage(chatInput.value);
    });

    quickReplyBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            sendQuickReply(btn.textContent);
        });
    });

    // Simple short-polling mechanism to check for admin replies when chat is open
    function startPolling() {
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => {
            if (isOpen) {
                fetchMessages();
            }
        }, 5000); // Check every 5 seconds
    }

    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }
});
