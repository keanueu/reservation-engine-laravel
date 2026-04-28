
document.addEventListener('DOMContentLoaded', async () => {
    // 1. Get DOM Elements 
    const chatContainer = document.getElementById('ai-chat-container');
    const chatBox = document.getElementById('chat-box');
    const input = document.getElementById('chat-input');
    const sendBtn = document.getElementById('send-btn');
    const chatFab = document.getElementById('chat-fab');
    const closeChatBtn = document.getElementById('close-chat-btn');

    // If required elements are missing, bail out gracefully.
    if (!chatContainer || !chatBox || !input || !sendBtn || !chatFab) {
        console.warn('Chatbot: required DOM elements missing, chatbot disabled on this page.');
        return;
    }

    let initialThinkingElement = null;
    const TYPING_DELAY = 15; // Speed of the typewriter effect (in milliseconds per character)
    const TRANSITION_DURATION = 300; // Match this to the CSS transition time (0.3s)

    // --- Toggle Logic with Animation ---
    function openChat() {
        // 1. Remove 'hidden-chat' to make it display: block immediately
        chatContainer.classList.remove('hidden-chat');
        chatFab.style.display = 'none';

        // 2. Schedule removal of 'ai-chat-closed' for the next tick, allowing the transition to run
        setTimeout(() => {
            chatContainer.classList.remove('ai-chat-closed');
        }, 0);

        input.focus();
    }

    function closeChat() {
        // 1. Add 'ai-chat-closed' to start the fade-out/slide-down transition
        chatContainer.classList.add('ai-chat-closed');

        // 2. After the transition completes, add 'hidden-chat' to remove it completely from flow
        setTimeout(() => {
            chatContainer.classList.add('hidden-chat');
            chatFab.style.display = 'flex';
        }, TRANSITION_DURATION); // Wait for 300ms (0.3s)
    }

    // Initialize chat to be closed
    chatContainer.classList.add('ai-chat-closed');
    closeChat(); // Ensures the FAB is visible and the container is fully hidden initially

    chatFab.onclick = openChat;
    closeChatBtn.onclick = closeChat;
    // --- End Toggle Logic ---

    // Helper function to append a user message
    function appendUser(text) {
        chatBox.innerHTML += `
                    <div class="flex justify-end my-4 text-gray-600 text-sm"> 
                        <p class="leading-relaxed bg-orange-600 font-light text-white rounded-lg p-2 max-w-[80%]">
                            <span class="block text-white"></span> ${text}
                        </p>
                    </div>`;
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // Helper function to append an AI message chunk
    function appendAI(text) {
        const container = document.createElement('div');
        container.className = 'flex gap-3 my-4 text-black text-sm font-light';

        // AI Avatar
        const avatarHtml = `<span class="relative flex shrink-0 overflow-hidden rounded-full w-8 h-8">
                    <div class="rounded-full bg-gray-100 border p-1"><svg stroke="none" fill="black" stroke-width="1.5"
                        viewBox="0 0 24 24" aria-hidden="true" height="20" width="20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z">
                        </path>
                    </svg></div>
                </span>`;

        // Message Bubble
        const pElement = document.createElement('p');
        pElement.className = 'leading-relaxed bg-gray-100 rounded-lg p-2 max-w-[80%]';

        const spanElement = document.createElement('span');
        spanElement.className = 'block font-bold text-gray-700';
        spanElement.textContent = '';

        const textNode = document.createTextNode(text);

        pElement.appendChild(spanElement);
        pElement.appendChild(textNode);

        container.innerHTML = avatarHtml;
        container.appendChild(pElement);
        chatBox.appendChild(container);
        chatBox.scrollTop = chatBox.scrollHeight;

        return textNode;
    }

    // Helper function to append a meta message (like 'Thinking...')
    function appendMeta(text, isError = false) {
        const metaDiv = document.createElement('div');
        metaDiv.className = `text-center my-1 text-xs meta-message ${isError ? 'text-red-500 font-bold' : 'text-gray-500'}`;
        metaDiv.textContent = text;
        chatBox.appendChild(metaDiv);
        chatBox.scrollTop = chatBox.scrollHeight;
        return metaDiv;
    }

    // Helper function to show the bouncing dots indicator
    function appendTypingIndicator() {
        const indicatorContainer = document.createElement('div');
        indicatorContainer.className = 'flex gap-3 my-4 text-gray-600 text-sm';
        indicatorContainer.id = 'typing-indicator-wrapper'; // ID for easy removal

        const avatarHtml = `<span class="relative flex shrink-0 overflow-hidden rounded-full w-8 h-8">
                    <div class="rounded-full bg-gray-100 border p-1"><svg stroke="none" fill="black" stroke-width="1.5"
                        viewBox="0 0 24 24" aria-hidden="true" height="20" width="20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z">
                        </path>
                    </svg></div>
                </span>`;

        const indicatorContent = `<div class="typing-indicator">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>`;

        indicatorContainer.innerHTML = avatarHtml + indicatorContent;
        chatBox.appendChild(indicatorContainer);
        chatBox.scrollTop = chatBox.scrollHeight;
        return indicatorContainer;
    }

    // New function to remove the indicator
    function removeTypingIndicator() {
        const indicator = document.getElementById('typing-indicator-wrapper');
        if (indicator) {
            indicator.remove();
        }
    }

    // Function to handle the streaming response from the server with typing effect
    async function processStream(response) {
        removeTypingIndicator(); // Remove indicator once the first chunk is received

        if (!response.body) {
            appendMeta('Error: Failed to establish connection to server stream.', true);
            return;
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let buffer = '';
        let aiTextNode = null;
        let streamComplete = false;

        // Helper to add characters one by one with a delay
        const typeCharacter = (char) => {
            return new Promise(resolve => {
                setTimeout(() => {
                    if (aiTextNode) {
                        aiTextNode.textContent += char;
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }
                    resolve();
                }, TYPING_DELAY);
            });
        };

        try {
            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, { stream: true });

                const lines = buffer.split('\n');
                buffer = lines.pop();

                for (const line of lines) {
                    if (line.trim() === '') continue;

                    try {
                        const data = JSON.parse(line);

                        if (data.reply_chunk) {
                            if (!aiTextNode) {
                                // Create the initial message bubble
                                aiTextNode = appendAI(data.reply_chunk);
                            } else {
                                // Apply the typewriter effect character by character
                                for (const char of data.reply_chunk) {
                                    await typeCharacter(char);
                                }
                            }
                        }

                        // Handle non-reply chunks (functions, done, error)
                        if (data.function_running || data.function_called || data.done || data.error) {

                            // Handle internal events
                            if (data.function_running) {
                                aiTextNode = null;
                                appendMeta('Running internal logic...');
                            }

                            if (data.function_called) {
                                aiTextNode = null;
                                appendMeta(`(Function called: ${data.function_called})`);
                                appendMeta('Result: ' + JSON.stringify(data.function_result).substring(0, 100) + '...');
                                aiTextNode = appendAI('');
                            }

                            if (data.done) {
                                streamComplete = true;
                                reader.cancel();
                                return;
                            }

                            if (data.error) {
                                appendMeta(`Server Error: ${data.error}`, true);
                                reader.cancel();
                                return;
                            }
                        }

                    } catch (e) {
                        continue;
                    }
                }
            }
        } catch (error) {
            console.error('Stream processing error:', error);
            appendMeta('Error reading stream. Connection may have been interrupted.', true);
        } finally {
            if (!streamComplete) {
                if (!aiTextNode || aiTextNode.textContent.trim() === 'AI') {
                    appendMeta('AI response failed or timed out. Check server logs.', true);
                }
            }
        }
    }

    // --- Send Logic ---
    const handleSend = async (e) => {
        e.preventDefault();

        const message = input.value.trim();
        if (!message) return;

        appendUser(message);
        const userMessageToSend = message;
        input.value = '';

        // Disable input during the process
        input.disabled = true;
        sendBtn.disabled = true;

        // Show the typing indicator immediately
        initialThinkingElement = appendTypingIndicator();

        try {
            // NOTE: This assumes a backend running on your server at /ai/chat
            const res = await fetch('/ai/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : 'fake-token'
                },
                body: JSON.stringify({ message: userMessageToSend })
            });

            if (!res.ok) {
                removeTypingIndicator();
                let errorMessage = `Server responded with status ${res.status}.`;
                try {
                    const errorBody = await res.json();
                    errorMessage = errorBody.error || errorMessage;
                } catch (e) {
                }

                appendMeta(`Request failed: ${errorMessage}`, true);
                return;
            }

            await processStream(res);

        } catch (error) {
            console.error('Fetch error:', error);
            removeTypingIndicator();
            appendMeta('Network error: Could not connect to the server.', true);
        } finally {
            // Re-enable input
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();
            removeTypingIndicator(); // Ensure indicator is gone on process completion
        }
    };

    // Attach event listeners
    sendBtn.addEventListener('click', handleSend);
    const form = input.closest('form');
    if (form) form.addEventListener('submit', handleSend);

    // Enter key listener
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSend(e);
        }
    });
});



