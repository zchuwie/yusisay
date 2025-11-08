document.addEventListener("DOMContentLoaded", () => {
    let currentConversationId = null;
    let currentChatUserName = null;
    let currentChatUserAvatar = null;
    let currentChatUserId = null; // Track the user we're chatting with
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

    // Listen to user's personal channel for conversation list updates
    Echo.private(`user.${userId}`).listen("MessageSent", (e) => {
        console.log("Received message on user channel:", e);
        updateConversationList(e.message);
    });

    // Update or create conversation in the list
    async function updateConversationList(message) {
        console.log("Updating conversation list with:", message);

        let convItem = conversationList.querySelector(
            `li[data-id="${message.conversation_id}"]`
        );

        if (convItem) {
            const messageContainer = convItem.querySelector(".flex-1.min-w-0");
            if (messageContainer) {
                const paragraphs = messageContainer.querySelectorAll("p");
                let messagePreview = paragraphs[paragraphs.length - 1];

                if (messagePreview) {
                    messagePreview.textContent = message.body;
                    messagePreview.className =
                        "text-xs text-gray-600 truncate recent-message";
                }

                // Update timestamp
                const headerDiv =
                    messageContainer.querySelector("div:first-child");
                if (headerDiv) {
                    const timeSpan = headerDiv.querySelector(
                        "span.text-xs.text-gray-500"
                    );
                    if (timeSpan) {
                        timeSpan.textContent = "now";
                    } else {
                        const newTimeSpan = document.createElement("span");
                        newTimeSpan.className =
                            "text-xs text-gray-500 recent-time";
                        newTimeSpan.textContent = "now";
                        headerDiv.appendChild(newTimeSpan);
                    }
                }

                if (conversationList.firstChild !== convItem) {
                    conversationList.insertBefore(
                        convItem,
                        conversationList.firstChild
                    );
                }

                originalConversations = conversationList.innerHTML;
            }
        } else {
            // Create new conversation item
            try {
                const response = await fetch(`/api/user/${message.sender_id}`);
                if (!response.ok) throw new Error("Failed to fetch user info");

                const sender = await response.json();

                const li = document.createElement("li");
                li.className =
                    "cursor-pointer py-4 px-5 hover:bg-gray-50 transition-colors flex items-start gap-3 border-b border-gray-100";
                li.dataset.id = message.conversation_id;
                li.dataset.name = sender.name;
                li.dataset.userid = sender.id;

                const avatarHtml = sender.profile_picture
                    ? `<img src="/assets/${sender.profile_picture}" class="w-full h-full object-cover" alt="${sender.name}">`
                    : `<span class="text-base font-bold text-white">${sender.name
                          .charAt(0)
                          .toUpperCase()}</span>`;

                li.innerHTML = `
                    <div class="relative flex-shrink-0">
                        <div class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center bg-[#FF9013]">
                            ${avatarHtml}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-sm font-semibold text-gray-900 truncate">${sender.name}</h3>
                            <span class="text-xs text-gray-500 recent-time">now</span>
                        </div>
                        <p class="text-xs text-gray-600 truncate recent-message">${message.body}</p>
                    </div>
                `;

                conversationList.insertBefore(li, conversationList.firstChild);
                originalConversations = conversationList.innerHTML;
            } catch (error) {
                console.error("Failed to create conversation item:", error);
            }
        }
    }

    // Switch or start a conversation
    async function switchConversation(item) {
        if (!item) return;

        let newConversationId = item.dataset.id;
        const userIdToChat = item.dataset.userid;

        console.log(
            "Switching to conversation:",
            newConversationId,
            "User ID:",
            userIdToChat
        );

        if (window.currentEchoChannel) {
            window.currentEchoChannel.stopListening("MessageSent");
            Echo.leave(`private-conversation.${currentConversationId}`);
            window.currentEchoChannel = null;
        }
        if (newConversationId) {
            currentConversationId = newConversationId;
            currentChatUserName = item.dataset.name;
            currentChatUserId = userIdToChat;

            const avatarElement = item.querySelector(
                ".w-12.h-12, .w-8.h-8, .w-11.h-11"
            );
            currentChatUserAvatar = avatarElement
                ? avatarElement.outerHTML
                : null;

            chatUserName.textContent = currentChatUserName;
            if (currentChatUserAvatar) {
                chatUserAvatar.innerHTML = currentChatUserAvatar;
            }

            const statusElement = document.getElementById("chatUserStatus");
            if (statusElement) {
                statusElement.textContent = "Active now";
            }

            messageInput.disabled = false;
            sendBtn.disabled = false;
            messagesDiv.innerHTML =
                '<div class="text-center text-gray-400 py-8">Loading messages...</div>';

            conversationList
                .querySelectorAll("li")
                .forEach((li) => li.classList.remove("active"));
            item.classList.add("active");

            try {
                const res = await fetch(`/chat/${currentConversationId}`);
                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const data = await res.json();
                renderMessages(data.messages || []);
            } catch (err) {
                messagesDiv.innerHTML =
                    '<div class="text-center text-red-500 py-8">Failed to load messages</div>';
                console.error("Failed to load messages:", err);
                return;
            }

            window.currentEchoChannel = Echo.private(
                `conversation.${currentConversationId}`
            ).listen("MessageSent", (e) => {
                if (e.message.sender_id === userId) return;
                appendMessage(e.message, false);
            });
        } else if (userIdToChat) {
            currentConversationId = null;
            currentChatUserName = item.dataset.name;
            currentChatUserId = userIdToChat;

            const avatarElement = item.querySelector(
                ".w-12.h-12, .w-8.h-8, .w-11.h-11"
            );
            currentChatUserAvatar = avatarElement
                ? avatarElement.outerHTML
                : null;

            chatUserName.textContent = currentChatUserName;
            if (currentChatUserAvatar) {
                chatUserAvatar.innerHTML = currentChatUserAvatar;
            }

            const statusElement = document.getElementById("chatUserStatus");
            if (statusElement) {
                statusElement.textContent = "Active now";
            }

            messageInput.disabled = false;
            sendBtn.disabled = false;

            messagesDiv.innerHTML =
                '<div class="text-center text-gray-400 py-8">No messages yet. Start the conversation!</div>';
            conversationList
                .querySelectorAll("li")
                .forEach((li) => li.classList.remove("active"));
            item.classList.add("active");

            console.log("Prepared new chat with user:", currentChatUserId);
        }
    }

    // Handle conversation clicks
    conversationList.addEventListener("click", (e) => {
        const item = e.target.closest("li[data-id], li[data-userid]");
        if (!item) return;
        switchConversation(item);
    });

    // Search users
    let searchTimeout;
    searchInput?.addEventListener("input", async (e) => {
        const q = e.target.value.trim();

        clearTimeout(searchTimeout);

        if (!q) {
            conversationList.innerHTML = originalConversations;
            return;
        }

        searchTimeout = setTimeout(async () => {
            if (q.length < 2) return;

            try {
                const res = await fetch(
                    `/search-users?q=${encodeURIComponent(q)}`
                );
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
                    li.className =
                        "cursor-pointer py-4 px-5 hover:bg-gray-50 transition-colors flex items-center gap-3 border-b border-gray-100";
                    li.dataset.userid = user.id;
                    li.dataset.name = user.name;

                    const existingConversation = findExistingConversation(
                        user.id
                    );
                    if (existingConversation) {
                        li.dataset.id = existingConversation.id;
                    }

                    const avatarHtml = user.profile_picture
                        ? `<img src="/assets/${user.profile_picture}" class="w-full h-full object-cover" alt="${user.name}">`
                        : `<span class="text-base font-bold text-white">${user.name
                              .charAt(0)
                              .toUpperCase()}</span>`;

                    const messagePreview = existingConversation
                        ? existingConversation.last_message
                            ? existingConversation.last_message
                            : "No messages yet"
                        : "Click to start a conversation";

                    li.innerHTML = `
                    <div class="relative flex-shrink-0">
                        <div class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center bg-[#FF9013]">
                            ${avatarHtml}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-900">${user.name}</h3>
                        <p class="text-xs text-gray-500 truncate">${messagePreview}</p>
                    </div>
                `;

                    li.addEventListener("click", () => {
                        // Clear search and restore original list
                        searchInput.value = "";
                        conversationList.innerHTML = originalConversations;

                        // Find the conversation item in the restored list by user ID
                        let targetItem = conversationList.querySelector(
                            `li[data-userid="${user.id}"]`
                        );

                        // Only create new item if user doesn't exist at all in the list
                        if (!targetItem) {
                            // Create new conversation item for completely new chat
                            targetItem = document.createElement("li");
                            targetItem.className =
                                "cursor-pointer py-4 px-5 hover:bg-gray-50 transition-colors flex items-start gap-3 border-b border-gray-100";
                            targetItem.dataset.userid = user.id;
                            targetItem.dataset.name = user.name;

                            targetItem.innerHTML = `
                            <div class="relative flex-shrink-0">
                                <div class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center bg-[#FF9013]">
                                    ${avatarHtml}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate">${user.name}</h3>
                                    <span class="text-xs text-gray-500 recent-time">now</span>
                                </div>
                                <p class="text-xs text-gray-500 truncate recent-message">Click to start a conversation</p>
                            </div>
                        `;

                            // Add to top of list
                            conversationList.insertBefore(
                                targetItem,
                                conversationList.firstChild
                            );
                            // Update saved state
                            originalConversations = conversationList.innerHTML;
                        }

                        // Switch to the conversation (existing or newly created)
                        if (targetItem) {
                            switchConversation(targetItem);
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

    function findExistingConversation(userId) {
        const originalListContainer = document.createElement("div");
        originalListContainer.innerHTML = originalConversations;
        const conversationItems =
            originalListContainer.querySelectorAll("li[data-userid]");

        for (let item of conversationItems) {
            if (item.dataset.userid === userId && item.dataset.id) {
                const messageElement = item.querySelector(
                    ".flex-1.min-w-0 p:last-child"
                );
                const lastMessage = messageElement
                    ? messageElement.textContent
                    : "No messages yet";

                return {
                    id: item.dataset.id,
                    last_message: lastMessage,
                };
            }
        }
        return null;
    }

    // Create conversation and send first message
    async function createConversationAndSendMessage(body) {
        if (!currentChatUserId) {
            console.error("No user selected to chat with");
            return null;
        }

        try {
            // Create conversation
            const convRes = await fetch("/chat/start", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: JSON.stringify({ user_id: currentChatUserId }),
            });

            if (!convRes.ok)
                throw new Error(
                    `HTTP ${convRes.status} - Failed to create conversation`
                );

            const conv = await convRes.json();
            currentConversationId = conv.id;
            console.log("Created new conversation:", currentConversationId);

            // Send first message
            const msgRes = await fetch("/chat/message", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: JSON.stringify({
                    conversation_id: currentConversationId,
                    body: body,
                }),
            });

            if (!msgRes.ok)
                throw new Error(
                    `HTTP ${msgRes.status} - Failed to send message`
                );

            const msg = await msgRes.json();

            // Update the conversation list item
            const activeItem = conversationList.querySelector("li.active");
            if (activeItem) {
                activeItem.dataset.id = currentConversationId;

                // Add conversation to top of list if it's a new one from search
                if (!activeItem.dataset.id) {
                    const newItem = activeItem.cloneNode(true);
                    newItem.dataset.id = currentConversationId;
                    conversationList.insertBefore(
                        newItem,
                        conversationList.firstChild
                    );
                    // Update saved state
                    originalConversations = conversationList.innerHTML;
                }
            }

            // Setup real-time listener for the new conversation
            if (window.currentEchoChannel) {
                window.currentEchoChannel.stopListening("MessageSent");
                Echo.leave(`private-conversation.${currentConversationId}`);
            }

            window.currentEchoChannel = Echo.private(
                `conversation.${currentConversationId}`
            ).listen("MessageSent", (e) => {
                if (e.message.sender_id === userId) return;
                appendMessage(e.message, false);
            });

            return msg;
        } catch (err) {
            console.error(
                "Failed to create conversation and send message:",
                err
            );
            throw err;
        }
    }

    // Send message
    sendBtn.addEventListener("click", async () => {
        const body = messageInput.value.trim();
        if (!body) {
            console.error("Cannot send empty message");
            return;
        }

        // If no conversation exists yet, create one and send the first message
        if (!currentConversationId && currentChatUserId) {
            const messageCopy = body;
            messageInput.value = "";
            messageInput.style.height = "auto";
            messageInput.disabled = true;
            sendBtn.disabled = true;

            try {
                const msg = await createConversationAndSendMessage(messageCopy);
                appendMessage(msg, true);
            } catch (err) {
                messageInput.value = messageCopy;
                console.error("Failed to send first message:", err);
            } finally {
                messageInput.disabled = false;
                sendBtn.disabled = false;
                messageInput.focus();
            }
            return;
        }

        // Existing conversation - normal message flow
        if (!currentConversationId) {
            console.error("No conversation selected");
            return;
        }

        const messageCopy = body;
        messageInput.value = "";
        messageInput.style.height = "auto";
        messageInput.disabled = true;
        sendBtn.disabled = true;

        try {
            const res = await fetch("/chat/message", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: JSON.stringify({
                    conversation_id: currentConversationId,
                    body: messageCopy,
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
        return date.toLocaleTimeString("en-US", {
            hour: "numeric",
            minute: "2-digit",
            hour12: true,
        });
    }

    function createTimestamp(date) {
        const div = document.createElement("div");
        div.className = "text-center text-gray-400 text-xs my-2";
        div.textContent = date.toLocaleString([], {
            day: "2-digit",
            month: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            hour12: true,
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
            messagesDiv.innerHTML =
                '<div class="text-center text-gray-400 py-8">No messages yet. Start the conversation!</div>';
            return;
        }

        const fragment = document.createDocumentFragment();
        let lastTimestamp = null;

        messages.forEach((m) => {
            const currentTime = new Date(m.created_at);
            if (!lastTimestamp || (currentTime - lastTimestamp) / 60000 > 10) {
                fragment.appendChild(createTimestamp(currentTime));
            }
            fragment.appendChild(
                createMessageElement(m, m.sender_id === userId)
            );
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
        const convItem = conversationList.querySelector(
            `li[data-id="${msg.conversation_id}"]`
        );
        if (convItem) {
            const messageContainer = convItem.querySelector(".flex-1.min-w-0");
            if (messageContainer) {
                const paragraphs = messageContainer.querySelectorAll("p");
                let messagePreview = paragraphs[paragraphs.length - 1];

                if (messagePreview) {
                    messagePreview.textContent = isMine
                        ? `You: ${msg.body}`
                        : msg.body;
                    messagePreview.className = "text-xs text-gray-600 truncate";
                }

                const headerDiv =
                    messageContainer.querySelector("div:first-child");
                if (headerDiv) {
                    const timeSpan = headerDiv.querySelector(
                        "span.text-xs.text-gray-500"
                    );
                    if (timeSpan) {
                        timeSpan.textContent = "now";
                    } else {
                        const newTimeSpan = document.createElement("span");
                        newTimeSpan.className = "text-xs text-gray-500";
                        newTimeSpan.textContent = "now";
                        headerDiv.appendChild(newTimeSpan);
                    }
                }

                if (conversationList.firstChild !== convItem) {
                    conversationList.insertBefore(
                        convItem,
                        conversationList.firstChild
                    );
                }
            }
        }
    }
});
