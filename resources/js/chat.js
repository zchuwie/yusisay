document.addEventListener('DOMContentLoaded', () => {
    let currentConversationId = null;
    let currentChatUserName = null;
    const userId = window.Laravel.userId;

    const conversationList = document.getElementById('conversationList');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const messagesDiv = document.getElementById('messages');
    const chatUserName = document.getElementById('chatUserName');
    const searchInput = document.getElementById('search');

    if (!conversationList) return;

    // Load conversation when clicked
    conversationList.addEventListener('click', async (e) => {
        const item = e.target.closest('li[data-id]');
        if (!item) return;

        const newConversationId = item.dataset.id;

        // Leave the old channel if switching
        if (window.currentEchoChannel) {
            window.currentEchoChannel.stopListening('MessageSent');
            Echo.leave(`private-conversation.${currentConversationId}`);
            window.currentEchoChannel = null;
        }

        currentConversationId = newConversationId;
        currentChatUserName = item.dataset.name;
        chatUserName.textContent = currentChatUserName;
        messageInput.disabled = false;
        sendBtn.disabled = false;

        const res = await fetch(`/chat/${currentConversationId}`);
        const data = await res.json();
        renderMessages(data.messages);

        // Subscribe to real-time updates for this conversation
        window.currentEchoChannel = Echo.private(`conversation.${currentConversationId}`)
            .listen('MessageSent', (e) => {
                if (e.message.sender_id === userId) return;
                appendMessage(e.message, false);
            });
    });

    // Search users
    searchInput?.addEventListener('input', async () => {
        const q = searchInput.value.trim();
        const list = document.getElementById('conversationList');

        if (q.length === 0) {
            window.location.reload();
            return;
        }

        const res = await fetch(`/chat/search?q=${encodeURIComponent(q)}`);
        const users = await res.json();

        list.innerHTML = '';
        if (users.length === 0) {
            list.innerHTML = '<li class="text-gray-500 text-sm p-2">No users found</li>';
            return;
        }

        users.forEach(user => {
            const li = document.createElement('li');
            li.className = "cursor-pointer py-2 px-3 hover:bg-green-50 rounded mb-1 border-b flex items-center justify-between";
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

            li.addEventListener('click', async () => {
                const res = await fetch('/chat/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ user_id: user.id })
                });

                const conv = await res.json();
                list.innerHTML = '';
                const newLi = document.createElement('li');
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

    // Send message
    sendBtn.addEventListener('click', async () => {
        const body = messageInput.value.trim();
        if (!body || !currentConversationId) return;

        const res = await fetch('/chat/message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ conversation_id: currentConversationId, body })
        });

        const msg = await res.json();
        appendMessage(msg, true);
        messageInput.value = '';
    });

    // Render conversation messages
    function renderMessages(messages) {
        messagesDiv.innerHTML = '';
        messages.forEach(m => appendMessage(m, m.sender_id === userId));
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    // Append a single message to the UI
    function appendMessage(msg, isMine) {
        const div = document.createElement('div');
        div.className = `my-2 p-2 rounded-lg max-w-xs ${
            isMine ? 'bg-green-200 ml-auto text-right' : 'bg-gray-200'
        }`;
        div.textContent = msg.body;
        messagesDiv.appendChild(div);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
});
