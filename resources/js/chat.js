// COMPLETE CHAT.JS - Replace your entire chat.js file with this

document.addEventListener("DOMContentLoaded", () => {
    let currentConversationId = null;
    let currentChatUserName = null;
    let currentChatUserAvatar = null;
    let originalConversations = null;
    const userId = window.Laravel.userId;

    const conversationList = document.getElementById("conversationList");
    const messageInput = document.getElementById("messageInput");
    const sendBtn = document.getElementById("sendBtn");
    const messagesDiv = document.getElementById("messages");
    const chatUserName = document.getElementById("chatUserName");
    const chatUserAvatar = document.getElementById("chatUserAvatar");
    const searchInput = document.getElementById("search");

    if (!conversationList) return;

    // Save original conversations when page loads
    originalConversations = conversationList.innerHTML;

    // Switch or start a conversation
    async function switchConversation(item) {
        if (!item) return;

        let newConversationId = item.dataset.id;
        const userIdToChat = item.dataset.userid;

        console.log("Switching to conversation:", newConversationId, "User ID:", userIdToChat);

        // Stop previous Echo channel
        if (window.currentEchoChannel) {
            window.currentEchoChannel.stopListening("MessageSent");
            Echo.leave(`private-conversation.${currentConversationId}`);
            window.currentEchoChannel = null;
        }

        // Create conversation if it doesn't exist
        if (!newConversationId && userIdToChat) {
            try {
                const res = await fetch("/chat/start", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ user_id: userIdToChat }),
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const conv = await res.json();
                newConversationId = conv.id;
                item.dataset.id = newConversationId;
                console.log("Created new conversation:", newConversationId);
            } catch (err) {
                console.error("Failed to create conversation:", err);
                return;
            }
        }

        currentConversationId = newConversationId;
        currentChatUserName = item.dataset.name;

        // Load avatar
        const avatarElement = item.querySelector(".w-12.h-12, .w-8.h-8, .w-11.h-11");
        currentChatUserAvatar = avatarElement ? avatarElement.outerHTML : null;

        // Update chat header
        chatUserName.textContent = currentChatUserName;
        if (currentChatUserAvatar) {
            chatUserAvatar.innerHTML = currentChatUserAvatar;
        }

        // Update status text
        const statusElement = document.getElementById("chatUserStatus");
        if (statusElement) {
            statusElement.textContent = "Active now";
        }

        // Enable input
        messageInput.disabled = false;
        sendBtn.disabled = false;
        messagesDiv.innerHTML = '<div class="text-center text-gray-400 py-8">Loading messages...</div>';

        // Mark as active in list
        conversationList.querySelectorAll("li").forEach(li => li.classList.remove("active"));
        item.classList.add("active");

        try {
            const res = await fetch(`/chat/${currentConversationId}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();
            renderMessages(data.messages || []);
        } catch (err) {
            messagesDiv.innerHTML = '<div class="text-center text-red-500 py-8">Failed to load messages</div>';
            console.error("Failed to load messages:", err);
            return;
        }

        // Setup real-time listener
        window.currentEchoChannel = Echo.private(`conversation.${currentConversationId}`)
            .listen("MessageSent", (e) => {
                if (e.message.sender_id === userId) return;
                appendMessage(e.message, false);
            });
    }

    // Handle conversation clicks
    conversationList.addEventListener("click", (e) => {
        const item = e.target.closest("li[data-id], li[data-userid]");
        if (!item) return;
        switchConversation(item);
    });

    // Search users - ONLY ONE IMPLEMENTATION
    let searchTimeout;
    searchInput?.addEventListener("input", async (e) => {
        const q = e.target.value.trim();

        clearTimeout(searchTimeout);

        // When search is cleared, restore original list
        if (!q) {
            conversationList.innerHTML = originalConversations;
            return;
        }

        // Wait 300ms before searching (debounce)
        searchTimeout = setTimeout(async () => {
            if (q.length < 2) return;

            try {
                const res = await fetch(`/search-users?q=${encodeURIComponent(q)}`);
                console.log(res);
                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const users = await res.json();
                console.log("Search results:", users);
                conversationList.innerHTML = "";

                if (!users.length) {
                    conversationList.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-12 px-4">
                            <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="text-sm text-gray-500 font-medium">No users found</p>
                            <p class="text-xs text-gray-400 mt-1">Try a different search term</p>
                        </div>
                    `;
                    return;
                }

                users.forEach((user) => {
                    const li = document.createElement("li");
                    li.className = "cursor-pointer py-4 px-5 hover:bg-gray-50 transition-colors flex items-center gap-3 border-b border-gray-100";
                    li.dataset.userid = user.id;
                    li.dataset.name = user.name;

                    // Create avatar HTML
                    const avatarHtml = user.profile_picture
                        ? `<img src="/assets/${user.profile_picture}" class="w-full h-full object-cover" alt="${user.name}">`
                        : `<span class="text-base font-bold text-white">${user.name.charAt(0).toUpperCase()}</span>`;

                    li.innerHTML = `
                        <div class="relative flex-shrink-0">
                            <div class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center bg-[#FF9013]">
                                ${avatarHtml}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900">${user.name}</h3>
                            <p class="text-xs text-gray-500">Click to start a conversation</p>
                        </div>
                    `;

                    // Add click handler
                    li.addEventListener("click", async () => {
                        try {
                            const res = await fetch("/chat/start", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify({ user_id: user.id }),
                            });

                            if (!res.ok) throw new Error(`HTTP ${res.status}`);

                            const conv = await res.json();
                            li.dataset.id = conv.id;

                            // Clear search and restore with new conversation
                            searchInput.value = "";
                            conversationList.innerHTML = originalConversations;

                            // Add new conversation to top of list
                            const newLi = li.cloneNode(true);
                            newLi.dataset.id = conv.id;
                            conversationList.insertBefore(newLi, conversationList.firstChild);

                            // Update saved state
                            originalConversations = conversationList.innerHTML;

                            // Switch to new conversation
                            switchConversation(newLi);
                        } catch (err) {
                            console.error("Failed to start conversation:", err);
                        }
                    });

                    conversationList.appendChild(li);
                });
            } catch (err) {
                console.error("Search failed:", err);
                conversationList.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-12 px-4">
                        <svg class="w-16 h-16 text-red-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-red-500 font-medium">Search failed</p>
                        <p class="text-xs text-gray-400 mt-1">Please try again later</p>
                    </div>
                `;
            }
        }, 300);
    });

    // Send message
    sendBtn.addEventListener("click", async () => {
        const body = messageInput.value.trim();
        if (!body || !currentConversationId) {
            console.error("Cannot send message:", { body, currentConversationId });
            return;
        }

        const messageCopy = body;
        messageInput.value = "";
        messageInput.style.height = 'auto';
        messageInput.disabled = true;
        sendBtn.disabled = true;

        try {
            const res = await fetch("/chat/message", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    conversation_id: currentConversationId,
                    body: messageCopy
                }),
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const msg = await res.json();
            appendMessage(msg, true);
        } catch (err) {
            messageInput.value = messageCopy;
            console.error("Failed to send message:", err);
        } finally {
            messageInput.disabled = false;
            sendBtn.disabled = false;
            messageInput.focus();
        }
    });

    // Send message on Enter
    messageInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendBtn.click();
        }
    });

    // Format message time
    function formatMessageTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString("en-US", { hour: "numeric", minute: "2-digit", hour12: true });
    }

    function createTimestamp(date) {
        const div = document.createElement("div");
        div.className = "text-center text-gray-400 text-xs my-2";
        div.textContent = date.toLocaleString([], {
            day: "2-digit", month: "2-digit", hour: "2-digit", minute: "2-digit", hour12: true
        });
        return div;
    }

    function createMessageElement(msg, isMine) {
        const wrapper = document.createElement("div");
        wrapper.className = isMine ? "message-sent" : "message-received";

        const bubble = document.createElement("div");
        bubble.className = "bubble";

        const messageText = document.createElement("div");
        messageText.textContent = msg.body;
        bubble.appendChild(messageText);

        const timeDiv = document.createElement("div");
        timeDiv.className = "message-time";
        timeDiv.textContent = formatMessageTime(msg.created_at);
        bubble.appendChild(timeDiv);

        wrapper.appendChild(bubble);
        return wrapper;
    }

    function renderMessages(messages) {
        messagesDiv.innerHTML = "";

        if (!messages.length) {
            messagesDiv.innerHTML = '<div class="text-center text-gray-400 py-8">No messages yet. Start the conversation!</div>';
            return;
        }

        const fragment = document.createDocumentFragment();
        let lastTimestamp = null;

        messages.forEach((m) => {
            const currentTime = new Date(m.created_at);
            if (!lastTimestamp || (currentTime - lastTimestamp) / 60000 > 10) {
                fragment.appendChild(createTimestamp(currentTime));
            }
            fragment.appendChild(createMessageElement(m, m.sender_id === userId));
            lastTimestamp = currentTime;
        });

        messagesDiv.appendChild(fragment);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    function appendMessage(msg, isMine) {
        const div = createMessageElement(msg, isMine);
        messagesDiv.appendChild(div);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;

        // Update conversation list recent message
        const convItem = conversationList.querySelector(`li[data-id="${msg.conversation_id}"]`);
        if (convItem) {
            const messageContainer = convItem.querySelector(".flex-1.min-w-0");
            if (messageContainer) {
            // Find the message preview (it's the last <p> element in the container)
            const paragraphs = messageContainer.querySelectorAll("p");
            let messagePreview = paragraphs[paragraphs.length - 1];

            // Update message text
            if (messagePreview) {
                messagePreview.textContent = isMine ? `You: ${msg.body}` : msg.body;
                messagePreview.className = "text-xs text-gray-600 truncate"; // Maintain styling
            }

            // Update timestamp - find the span with text-gray-500 in the first div
            const headerDiv = messageContainer.querySelector("div:first-child");
            if (headerDiv) {
                const timeSpan = headerDiv.querySelector("span.text-xs.text-gray-500");
                if (timeSpan) {
                timeSpan.textContent = "now";
                } else {
                // If no timestamp exists, create one
                const newTimeSpan = document.createElement("span");
                newTimeSpan.className = "text-xs text-gray-500";
                newTimeSpan.textContent = "now";
                headerDiv.appendChild(newTimeSpan);
                }
            }

            // Move conversation to top of list (common UX pattern)
            if (conversationList.firstChild !== convItem) {
                conversationList.insertBefore(convItem, conversationList.firstChild);
            }
            }
        }
        }
});
