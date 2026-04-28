

let selectedSession = null;
const sessionsListEl = document.getElementById('sessionsList');
const chatWindowEl = document.getElementById('chatWindow');
const adminMsgEl = document.getElementById('adminMsg');
const sendBtnEl = document.getElementById('sendAdmin');
const chatHeaderEl = document.getElementById('chatHeader');

if (!sessionsListEl || !chatWindowEl || !adminMsgEl || !sendBtnEl || !chatHeaderEl) {
    console.warn('admin-chat.js loaded without chat UI present; skipping initialization.');
} else {

/**
 * 🎨 Renders the list of active chat sessions.
 */
async function loadSessions() {
    try {
        const res = await fetch('/admin/api/sessions');
        if (!res.ok) throw new Error('Failed to fetch sessions');
        const sessions = await res.json();

        sessionsListEl.innerHTML = ''; // Clear existing list

        if (sessions.length === 0) {
            sessionsListEl.innerHTML = `<li class="p-4 text-center text-gray-500 dark:text-gray-400">No active sessions.</li>`;
            return;
        }

        sessions.forEach(s => {
            const user = s.user || {};

            // Create user avatar placeholder
            const initial = (user.name ? user.name.charAt(0) : (s.user_id ? String(s.user_id).charAt(0) : 'G')).toUpperCase();
            const avatarUrl = `https://placehold.co/40x40/E0E7FF/4F46E5?text=${initial}`; // Indigo-100 bg, Indigo-600 text

            const nameDisplay = user.name ? user.name : `Guest #${s.user_id}`;
            const emailDisplay = user.email || 'Session: ' + s.session_id.substring(0, 8) + '...';

            // Create the rich list item
            const li = document.createElement('li');
            li.innerHTML = `
                        <button class="w-full text-left p-4 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none transition duration-150 ease-in-out" 
                                onclick="selectSession('${s.session_id}', '${nameDisplay}')" 
                                data-session-id="${s.session_id}">
                            <div class="flex items-center space-x-3">
                                <img class="h-10 w-10 rounded-full object-cover flex-shrink-0" src="${avatarUrl}" alt="${nameDisplay}">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-sm text-gray-800 dark:text-white truncate">${nameDisplay}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">${emailDisplay}</div>
                                </div>
                            </div>
                        </button>`;
            sessionsListEl.appendChild(li);
        });

        // Re-apply active state visual if a session is already selected
        if (selectedSession) {
            const activeBtn = sessionsListEl.querySelector(`button[data-session-id="${selectedSession}"]`);
            if (activeBtn) {
                activeBtn.classList.add('bg-indigo-50', 'dark:bg-gray-700', 'font-semibold');
                activeBtn.classList.remove('hover:bg-gray-100', 'dark:hover:bg-gray-800');
            }
        }
    } catch (error) {
        console.error("Error loading sessions:", error);
        sessionsListEl.innerHTML = `<li class="p-4 text-center text-red-500">Failed to load sessions.</li>`;
    }
}

/**
 * 💬 Selects a session, highlights it, and loads its messages.
 */
async function selectSession(sessionId, userName) {
    selectedSession = sessionId;

    // Update chat header
    chatHeaderEl.textContent = `Conversation with ${userName}`;

    // Update active state in list
    document.querySelectorAll('#sessionsList button').forEach(btn => {
        btn.classList.remove('bg-indigo-50', 'dark:bg-gray-700', 'font-semibold');
        btn.classList.add('hover:bg-gray-100', 'dark:hover:bg-gray-800');
    });
    const activeBtn = document.querySelector(`button[data-session-id="${sessionId}"]`);
    if (activeBtn) {
        activeBtn.classList.add('bg-indigo-50', 'dark:bg-gray-700', 'font-semibold');
        activeBtn.classList.remove('hover:bg-gray-100', 'dark:hover:bg-gray-800');
    }

    // Fetch and display messages
    try {
        const res = await fetch(`/admin/api/session/${sessionId}`);
        if (!res.ok) throw new Error('Failed to fetch messages');
        const msgs = await res.json();

        chatWindowEl.innerHTML = ''; // Clear chat window

        if (msgs.length === 0) {
            chatWindowEl.innerHTML = `<div class="flex justify-center items-center h-full"><p class="text-gray-500 dark:text-gray-400">No messages in this conversation yet.</p></div>`;
            return;
        }

        msgs.forEach(m => {
            appendMessage(m);
        });

        scrollToBottom();

    } catch (error) {
        console.error("Error loading messages:", error);
        chatWindowEl.innerHTML = `<div class="flex justify-center items-center h-full"><p class="text-red-500">Failed to load messages.</p></div>`;
    }
}

/**
 * ✨ Appends a single message bubble to the chat window.
 */
function appendMessage(m) {
    const outerWrap = document.createElement('div');
    const bubble = document.createElement('div');

    // Create a text node to safely insert the message
    bubble.appendChild(document.createTextNode(m.message));

    // Check if message is from Admin (outgoing) or User (incoming), case-insensitive
    if (String(m.sender).toLowerCase() === 'admin') {
        outerWrap.className = 'flex justify-end';
        bubble.className = 'bg-indigo-600 text-white py-2 px-4 rounded-xl rounded-br-lg max-w-xs lg:max-w-lg break-words';
        outerWrap.appendChild(bubble);
    } else {
        outerWrap.className = 'flex justify-start';
        // Container for label + bubble
        const innerWrap = document.createElement('div');

        const senderLabel = document.createElement('div');
        senderLabel.className = 'text-xs text-gray-500 dark:text-gray-400 mb-1';
        senderLabel.textContent = m.sender; // e.g., "Guest #12345"
        innerWrap.appendChild(senderLabel);

        bubble.className = 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white py-2 px-4 rounded-xl rounded-bl-lg shadow-sm border dark:border-gray-600 max-w-xs lg:max-w-lg break-words';
        innerWrap.appendChild(bubble);

        outerWrap.appendChild(innerWrap);
    }
    chatWindowEl.appendChild(outerWrap);
}

/**
 * 📨 Sends the admin's reply.
 */
async function sendReply() {
    if (!selectedSession) {
        // Using a custom message box style instead of alert()
        const originalContent = chatWindowEl.innerHTML;
        chatWindowEl.innerHTML = `<div class="flex justify-center items-center h-full text-red-500">
                                                <p class="p-3 bg-red-100 dark:bg-red-900 rounded-lg shadow-md">
                                                    Please select a conversation to reply to.
                                                </p>
                                            </div>`;
        setTimeout(() => {
            chatWindowEl.innerHTML = originalContent;
        }, 3000);
        return;
    }

    const text = adminMsgEl.value.trim();
    if (!text) return;

    // Optimistically append the admin's message
    const optimisticMessage = {
        sender: 'Admin',
        message: text
    };
    appendMessage(optimisticMessage);
    scrollToBottom();

    adminMsgEl.value = ''; // Clear input

    try {
        // Ensure CSRF token meta tag exists in the main layout file for this to work
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : null;

        if (!csrfToken) {
            console.error("CSRF token not found. Cannot send reply.");
            throw new Error("CSRF token missing.");
        }

        await fetch(`/admin/api/session/${selectedSession}/reply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ message: text })
        });

    } catch (error) {
        console.error("Error sending reply:", error);
        // Custom UI error message instead of alert()
        const errorMessage = document.createElement('div');
        errorMessage.className = 'text-center text-red-500 p-2 bg-red-100 dark:bg-red-900 rounded-lg mt-2';
        errorMessage.textContent = 'Failed to send reply. Please try again.';
        chatWindowEl.appendChild(errorMessage);
        scrollToBottom();
    }
}

/**
 * 👇 Scrolls the chat window to the most recent message.
 */
function scrollToBottom() {
    chatWindowEl.scrollTop = chatWindowEl.scrollHeight;
}

// --- Event Listeners ---
sendBtnEl.addEventListener('click', sendReply);
adminMsgEl.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault(); // Prevent newline
        sendReply();
    }
});

// --- Initial Load and Polling ---
loadSessions();
setInterval(() => {
    if (!document.hidden) {
        loadSessions();
    }
}, 10000); // Refresh sessions list every 10 seconds when visible

}

