<x-app-layout>
    @if (Auth::check() && !Auth::user()->hasVerifiedEmail())
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        {{ __('Please verify your account first!') }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="w-[calc(100vw-16rem)] h-[calc(100vh-7rem)] bg-gray-50 flex">
            <!-- Left Chat List -->
            <div class="w-1/4 border-r bg-white flex flex-col">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-bold text-gray-700">Chats</h2>
                    <input type="text" id="search" placeholder="Search users..."
                        class="w-full mt-2 border rounded p-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                </div>

                <!-- Conversation List -->
                <div class="flex-1 overflow-y-auto p-2" id="conversationListContainer">
                    <ul id="conversationList">
                        @foreach ($conversations as $conv)
                            @php
                                $otherUser = $conv->userOne->id == auth()->id() ? $conv->userTwo : $conv->userOne;
                                $profilePicture = $otherUser->userInfo->profile_picture ?? null;
                            @endphp

                            <li class="cursor-pointer py-2 px-3 hover:bg-green-50 rounded mb-1 border-b flex items-center justify-between"
                                data-id="{{ $conv->id }}" data-name="{{ $otherUser->name }}">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center bg-gray-200">
                                        @if ($profilePicture)
                                            <img src="{{ asset('assets/' . $profilePicture) }}"
                                                class="w-full h-full object-cover" alt="{{ $otherUser->name }}">
                                        @else
                                            <span class="text-sm font-semibold text-gray-700">
                                                {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-gray-700 font-medium">{{ $otherUser->name }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Right Chat Area -->
            <div class="flex-1 flex flex-col">
                <!-- Chat Header -->
                <div id="chatHeader"
                    class="border-b p-4 font-bold text-lg bg-gray-100 text-gray-700 flex items-center justify-between">
                    <span id="chatUserName">Select a conversation</span>
                </div>

                <!-- Messages Section -->
                <div id="messages" class="flex-1 flex-col gap-2 overflow-y-auto p-4 bg-white"></div>

                <!-- Message Input -->
                <div class="border-t bg-gray-50 p-4 flex items-center">
                    <input id="messageInput" type="text" placeholder="Type your message..."
                        class="flex-1 border rounded p-2 focus:outline-none focus:ring-2 focus:ring-green-400" disabled>
                    <button id="sendBtn"
                        class="ml-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded disabled:opacity-50 transition"
                        disabled>
                        Send
                    </button>
                </div>
            </div>
        </div>

        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/chat.js'])

        <script>
            window.Laravel = {
                userId: {{ auth()->id() }}
            };


            const searchInput = document.getElementById('search');
            const conversationList = document.getElementById('conversationList');

            searchInput.addEventListener('input', async (e) => {
                const query = e.target.value.trim();

                if (query.length === 0) {
                    location.reload(); searchInput
                }


                if (query.length < 2) return;  

                try {
                    const response = await fetch(`/search-users?query=${encodeURIComponent(query)}`);
                    const users = await response.json();

                    conversationList.innerHTML = ''; 

                    if (users.length === 0) {
                        conversationList.innerHTML = '<p class="text-sm text-gray-500 px-2">No users found.</p>';
                        return;
                    }

                    users.forEach(user => {
                        const li = document.createElement('li');
                        li.className =
                            'cursor-pointer py-2 px-3 hover:bg-green-50 rounded mb-1 border-b flex items-center justify-between';
                        li.dataset.id = user.id;
                        li.dataset.name = user.name;

                        const profile = user.profile_picture ?
                            `<img src="/assets/${user.profile_picture}" class="w-full h-full object-cover" alt="${user.name}">` :
                            `<span class="text-sm font-semibold text-gray-700">${user.name.charAt(0).toUpperCase()}</span>`;

                        li.innerHTML = `
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center bg-gray-200">
                                    ${profile}
                                </div>
                                <span class="text-gray-700 font-medium">${user.name}</span>
                            </div>
                        `;

                        conversationList.appendChild(li);
                    });
                } catch (err) {
                    console.error('Search failed:', err);
                }
            });
        </script>
    @endif
</x-app-layout>
