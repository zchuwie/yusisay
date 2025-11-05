<x-app-layout>
    <div class="w-[calc(100vw-16rem)] h-[calc(100vh-7rem)] bg-gray-50 flex">

        <!-- Left Sidebar -->
        <div class="w-1/4 border-r bg-white flex flex-col">
            <div class="p-4 border-b">
                <h2 class="text-lg font-bold text-gray-700">Chats</h2>
                <input
                    type="text"
                    id="search"
                    placeholder="Search users..."
                    class="w-full mt-2 border rounded p-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400"
                >
            </div>

            <div class="flex-1 overflow-y-auto p-2" id="conversationListContainer">
                <ul id="conversationList">
                    @foreach($conversations as $conv)
                        @php
                            $otherUser = $conv->userOne->id == auth()->id() ? $conv->userTwo : $conv->userOne;
                        @endphp
                        <li
                            class="cursor-pointer py-2 px-3 hover:bg-green-50 rounded mb-1 border-b flex items-center justify-between"
                            data-id="{{ $conv->id }}"
                            data-name="{{ $otherUser->name }}"
                        >
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-green-200 flex items-center justify-center text-sm font-semibold text-green-800">
                                    {{ strtoupper(substr($otherUser->name, 0, 1)) }}
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
            <div id="chatHeader" class="border-b p-4 font-bold text-lg bg-gray-100 text-gray-700 flex items-center justify-between">
                <span id="chatUserName">Select a conversation</span>
            </div>

            <!-- Messages Section -->
            <div id="messages" class="flex-1 flex-col gap-2 overflow-y-auto p-4 bg-white"></div>

            <!-- Message Input -->
            <div class="border-t bg-gray-50 p-4 flex items-center">
                <input
                    id="messageInput"
                    type="text"
                    placeholder="Type your message..."
                    class="flex-1 border rounded p-2 focus:outline-none focus:ring-2 focus:ring-green-400"
                    disabled
                >
                <button
                    id="sendBtn"
                    class="ml-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded disabled:opacity-50 transition"
                    disabled
                >
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
    </script>

</x-app-layout>

