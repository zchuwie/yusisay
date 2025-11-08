document.addEventListener("DOMContentLoaded", () => {
    let currentConversationId = null;
    let currentChatUserName = null;
    let currentChatUserAvatar = null;
    const userId = window.Laravel.userId;

    const conversationList = document.getElementById("conversationList");
    const messageInput = document.getElementById("messageInput");
    const sendBtn = document.getElementById("sendBtn");
    const messagesDiv = document.getElementById("messages");
    const chatUserName = document.getElementById("chatUserName");
    const chatUserAvatar = document.getElementById("chatUserAvatar");
    const searchInput = document.getElementById("search");

    if (!conversationList) return;
 
    conversationList.addEventListener("click", async (e) => {
        const item = e.target.closest("li[data-id]");
        if (!item) return;

        const newConversationId = item.dataset.id;
 
        if (window.currentEchoChannel) {
            window.currentEchoChannel.stopListening("MessageSent");
            Echo.leave(`private-conversation.${currentConversationId}`);
            window.currentEchoChannel = null;
        }

        currentConversationId = newConversationId;
        currentChatUserName = item.dataset.name;

        // pang-get sa profile -neo
        const avatarElement = item.querySelector(".w-12.h-12");
        if (avatarElement) {
            currentChatUserAvatar = avatarElement.innerHTML;
        }
 
        chatUserName.textContent = currentChatUserName;
        if (currentChatUserAvatar) {
            chatUserAvatar.innerHTML = currentChatUserAvatar;
        }
        messageInput.disabled = false;
        sendBtn.disabled = false;
        messagesDiv.innerHTML =
            '<div class="text-center text-gray-400 py-8">Loading messages...</div>';

        const fetchPromise = fetch(`/chat/${currentConversationId}`);

        fetch(`/chat/${currentConversationId}/mark-read`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        }).catch((err) => console.error("Mark read failed:", err));
 
        try {
            const res = await fetchPromise;
            const data = await res.json();
            renderMessages(data.messages);
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
    });

    // Search users
    searchInput?.addEventListener("input", async () => {
        const q = searchInput.value.trim();
        const list = document.getElementById("conversationList");

        if (q.length === 0) {
            window.location.reload();
            return;
        }

        const res = await fetch(`/chat/search?q=${encodeURIComponent(q)}`);
        const users = await res.json();

        list.innerHTML = "";
        if (users.length === 0) {
            list.innerHTML =
                '<li class="text-gray-500 text-sm p-2">No users found</li>';
            return;
        }

        users.forEach((user) => {
            const li = document.createElement("li");
            li.className =
                "cursor-pointer py-2 px-3 hover:bg-green-50 rounded mb-1 border-b flex items-center justify-between";
            li.dataset.userid = user.id;
            li.dataset.name = user.name;
            li.innerHTML = `
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-green-200 flex items-center justify-center text-sm font-semibold text-green-800">
                        ${user.name.charAt(0).toUpperCase()}
                    </div>
                    <span class="text-gray-700 font-medium">${user.name}</span>
                </div>
            `;
 
            li.addEventListener("click", async () => {
                if (!confirm(`Start chat with ${user.name}?`)) return;

                const res = await fetch("/chat/start", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                    },
                    body: JSON.stringify({ user_id: user.id }),
                });

                const conv = await res.json();
                list.innerHTML = "";
                const newLi = document.createElement("li");
                newLi.className = li.className;
                newLi.dataset.id = conv.id;
                newLi.dataset.name = user.name;
                newLi.innerHTML = li.innerHTML;
                list.appendChild(newLi);
                newLi.click();
            });

            list.appendChild(li);
        });
    });
 
    sendBtn.addEventListener("click", async () => {
        const body = messageInput.value.trim();
        if (!body || !currentConversationId) return;

        const messageCopy = body;
        messageInput.value = "";
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

    // dinagdag q para sa time each bubble -neo
    function formatMessageTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString("en-US", {
            hour: "numeric",
            minute: "2-digit",
            hour12: true,
        });
    }
 
    function renderMessages(messages) {
        messagesDiv.innerHTML = "";
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

    // Oras
    function createTimestamp(date) {
        const timeDiv = document.createElement("div");
        timeDiv.className = "text-center text-gray-400 text-xs my-2";
        timeDiv.textContent = date.toLocaleString([], {
            day: "2-digit",
            month: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            hour12: true,
        });
        return timeDiv;
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

    // Para realtime yong UI
    function appendMessage(msg, isMine) {
        const div = createMessageElement(msg, isMine);
        messagesDiv.appendChild(div);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    messageInput.addEventListener("keydown", async (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendBtn.click();
        }
    });
});
