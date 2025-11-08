<x-app-layout>
    @if (Auth::check() && !Auth::user()->hasVerifiedEmail())
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-orange-50 overflow-hidden shadow sm:rounded-lg border border-orange-200">
                    <div class="p-6 text-gray-800 flex items-center gap-3">
                        <svg class="w-5 h-5 text-[#FF9013]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-medium">{{ __('Please verify your account first!') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="fixed inset-0 flex items-center justify-center bg-gray-50 mt-[9vh]">
            <div
                class="w-full max-w-7xl h-[78vh] bg-white rounded-lg shadow-lg overflow-hidden flex border border-gray-200">
  
                <div class="w-80 bg-white flex flex-col border-r border-gray-200">
                
                    <div class="p-5 border-b border-gray-200 bg-[#FF9013]">
                        <h2 class="text-xl font-bold text-white mb-1">Messages</h2>
                        <p class="text-white/90 text-[14px]">{{ count($conversations) }} conversations</p>
                        <div class="relative mt-4">
                            <input type="text" id="search" placeholder="Search conversations..."
                                class="w-full bg-white border-0 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-white/50 placeholder-gray-400">
                            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
 
                    <div class="flex-1 overflow-y-auto" id="conversationListContainer">
                        <ul id="conversationList">
                            @foreach ($conversations as $conv)
                                @php
                                    $otherUser = $conv->userOne->id == auth()->id() ? $conv->userTwo : $conv->userOne;
                                    $profilePicture = $otherUser->userInfo->profile_picture ?? null;
                                    $lastMessage = $conv->messages->last();
                                    $unreadCount = $conv->messages
                                        ->where('receiver_id', auth()->id())
                                        ->where('is_read', false)
                                        ->count();
                                @endphp

                                <li class="cursor-pointer py-4 px-5 hover:bg-gray-50 transition-colors flex items-start gap-3 border-b border-gray-100"
                                    data-id="{{ $conv->id }}" data-name="{{ $otherUser->name }}">
                              
                                    <div class="relative flex-shrink-0">
                                        <div
                                            class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center bg-[#FF9013]">
                                            @if ($profilePicture)
                                                <img src="{{ asset('assets/' . $profilePicture) }}"
                                                    class="w-full h-full object-cover" alt="{{ $otherUser->name }}">
                                            @else
                                                <span class="text-base font-bold text-white">
                                                    {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                                                </span>
                                            @endif
                                        </div> 
                                    </div>
 
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <h3 class="text-sm font-semibold text-gray-900 truncate">
                                                {{ $otherUser->name }}</h3>
                                            @if ($lastMessage)
                                                <span
                                                    class="text-xs text-gray-500">{{ $lastMessage->created_at->diffForHumans(null, true, true) }}</span>
                                            @endif
                                        </div>
                                        @if ($lastMessage)
                                            <p class="text-xs text-gray-600 truncate">
                                                {{ $lastMessage->sender_id == auth()->id() ? 'You: ' : '' }}
                                                {{ Str::limit($lastMessage->message, 40) }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 italic">No messages yet</p>
                                        @endif
                                    </div>

                                    <!-- Unread Badge -->
                                    @if ($unreadCount > 0)
                                        <div class="flex-shrink-0">
                                            <span
                                                class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 bg-[#FF9013] text-white text-xs font-bold rounded-full">
                                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                            </span>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
 
                <div class="flex-1 flex flex-col bg-white"> 
                    <div id="chatHeader"
                        class="border-b border-gray-200 p-5 bg-white flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <div id="chatUserAvatar"
                                    class="w-11 h-11 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                </div> 
                            </div>
                            <div>
                                <h3 id="chatUserName" class="text-base font-semibold text-gray-900">Select a
                                    conversation</h3>
                                <p id="chatUserStatus" class="text-[14px] text-gray-500">Click on a conversation to
                                    start
                                    chatting</p>
                            </div>
                        </div>

                    </div>

                    <!-- Messages Section -->
                    <div id="messages" class="flex-1 overflow-y-auto p-6 bg-gray-50 flex flex-col gap-2"></div>

                    <!-- Typing Indicator -->
                    <div id="typingIndicator" class="px-6 py-2 hidden">
                        <div class="flex items-center gap-2 text-gray-500 text-sm">
                            <div class="flex gap-1">
                                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                    style="animation-delay: 0ms"></span>
                                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                    style="animation-delay: 150ms"></span>
                                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                    style="animation-delay: 300ms"></span>
                            </div>
                            <span class="text-xs">typing...</span>
                        </div>
                    </div>
 
                    <div class="border-t border-gray-200 bg-white p-5">
                        <div class="flex items-center gap-3">
 
                            <div class="flex-1 relative flex items-center">
                                <textarea id="messageInput" rows="1" placeholder="Type your message..."
                                    class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 pr-12 focus:outline-none focus:border-[#FF9013] focus:ring-1 focus:ring-[#FF9013] resize-none max-h-32 transition-all"
                                    disabled></textarea>

                            </div>
 
                            <button id="sendBtn"
                                class="bg-[#FF9013] hover:bg-[#e68010] text-white text-[14px] font-bold small p-3 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed shadow"
                                disabled title="Send">
                                Send
                            </button>
                        </div>

                    </div>
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
            const messageInput = document.getElementById('messageInput');
            const charCounter = document.getElementById('charCounter');
            const charCount = document.getElementById('charCount');
 
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
 
                if (charCounter) {
                    charCount.textContent = this.value.length;
                    if (this.value.length > 0) {
                        charCounter.classList.remove('hidden');
                    } else {
                        charCounter.classList.add('hidden');
                    }
                }
            });
 
            searchInput.addEventListener('input', async (e) => {
                const query = e.target.value.trim();

                if (query.length === 0) {
                    location.reload();
                    return;
                }

                if (query.length < 2) return;

                try {
                    const response = await fetch(`/search-users?query=${encodeURIComponent(query)}`);
                    const users = await response.json();

                    conversationList.innerHTML = '';

                    if (users.length === 0) {
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

                    users.forEach(user => {
                        const li = document.createElement('li');
                        li.className =
                            'cursor-pointer py-4 px-5 hover:bg-gray-50 transition-colors flex items-center gap-3 border-b border-gray-100';
                        li.dataset.id = user.id;
                        li.dataset.name = user.name;

                        const profile = user.profile_picture ?
                            `<img src="/assets/${user.profile_picture}" class="w-full h-full object-cover" alt="${user.name}">` :
                            `<span class="text-base font-bold text-white">${user.name.charAt(0).toUpperCase()}</span>`;

                        li.innerHTML = `
                            <div class="relative flex-shrink-0">
                                <div class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center bg-[#FF9013]">
                                    ${profile}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-gray-900">${user.name}</h3>
                                <p class="text-xs text-gray-500">Click to start a conversation</p>
                            </div>
                        `;

                        conversationList.appendChild(li);
                    });
                } catch (err) {
                    console.error('Search failed:', err);
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
            });
        </script>

        <style> 
            #messages .message-sent {
                display: flex;
                justify-content: flex-end;
                margin-bottom: 8px;
            }

            #messages .message-sent .bubble {
                background: #FF9013;
                color: white;
                padding: 12px 16px;
                border-radius: 18px;
                border-bottom-right-radius: 4px;
                max-width: 65%;
                word-wrap: break-word;
            }

            #messages .message-received {
                display: flex;
                justify-content: flex-start;
                margin-bottom: 8px;
            }

            #messages .message-received .bubble {
                background: white;
                color: #1f2937;
                padding: 12px 16px;
                border-radius: 18px;
                border-bottom-left-radius: 4px;
                max-width: 65%;
                word-wrap: break-word;
                border: 1px solid #e5e7eb;
            }

            #messages .message-time {
                font-size: 10px;
                color: #9ca3af;
                margin-top: 4px;
            }

            #messages .message-sent .message-time {
                text-align: right;
                color: rgba(255, 255, 255, 0.8);
            }
 
            .date-divider {
                display: flex;
                align-items: center;
                margin: 20px 0;
            }

            .date-divider::before,
            .date-divider::after {
                content: '';
                flex: 1;
                height: 1px;
                background: #e5e7eb;
            }

            .date-divider span {
                padding: 0 16px;
                font-size: 12px;
                color: #6b7280;
                font-weight: 500;
                background: #f9fafb;
                border-radius: 12px;
                padding: 4px 12px;
            }
 
            #conversationListContainer::-webkit-scrollbar,
            #messages::-webkit-scrollbar {
                width: 6px;
            }

            #conversationListContainer::-webkit-scrollbar-track,
            #messages::-webkit-scrollbar-track {
                background: #f3f4f6;
            }

            #conversationListContainer::-webkit-scrollbar-thumb,
            #messages::-webkit-scrollbar-thumb {
                background: #d1d5db;
                border-radius: 3px;
            }

            #conversationListContainer::-webkit-scrollbar-thumb:hover,
            #messages::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }
            
            #conversationList li.active {
                background: #fff7ed;
                border-left: 3px solid #FF9013;
            }
        </style>
    @endif
</x-app-layout>
