@props(['post', 'username', 'time', 'content', 'commentsCount', 'postId', 'isOwner' => false])


<div class="relative group">
    <div
        class="bg-[#fafafa] border border-[#dddddd] p-[30px] px-[37px] w-[600px] rounded-2xl flex flex-col items-center justify-center mb-5 font-sans">
        <div class="flex flex-col justify-center gap-6 w-full">

            <div class="flex justify-between items-center w-full">

                <div class="flex items-center gap-3 w-full">
                    <!-- ✅ Profile Picture -->
                    <x-user-avatar :user="$post->user" :isAnonymous="$post->is_anonymous" />
                    @if ($isOwner)
                        <div class="text-[16px] font-bold text-[#454545]">
                            {{ $post->is_anonymous ? 'Anonymous' : $post->user->name }}<p
                                class="inline ml-1 text-[12px] text-[#8d8d8d]">(You)</p>
                        </div>
                    @elseif (!$post->is_anonymous)
                        <div class="text-[16px] font-bold text-[#454545]">
                            {{ $post->user->name }}
                        </div>
                    @else
                        <div class="text-[16px] font-bold text-[#454545]">
                            Anonymous
                        </div>
                    @endif
                    <div class="mt-1 text-[12px] text-[#8d8d8d]">
                        {{ $post->created_at->diffForHumans() }}
                    </div>
                </div>

                <div class="flex justify-end items-center gap-1.5 w-full self-center">
                    <!-- Comment icon with link -->
                    <a href="{{ route('posts.show', $postId) }}" class="flex justify-center items-center z-20">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22"
                            fill="none">
                            <path
                                d="M1.99169 15.3417C2.13873 15.7126 2.17147 16.119 2.08569 16.5087L1.02069 19.7987C0.986375 19.9655 0.995248 20.1384 1.04647 20.3008C1.09769 20.4633 1.18955 20.61 1.31336 20.727C1.43716 20.844 1.5888 20.9274 1.75389 20.9693C1.91898 21.0113 2.09205 21.0104 2.25669 20.9667L5.66969 19.9687C6.03741 19.8958 6.41822 19.9276 6.76869 20.0607C8.90408 21.0579 11.3231 21.2689 13.5988 20.6564C15.8746 20.0439 17.861 18.6473 19.2074 16.7131C20.5538 14.7788 21.1738 12.4311 20.958 10.0842C20.7422 7.73738 19.7044 5.54216 18.0278 3.88589C16.3511 2.22962 14.1434 1.21873 11.7941 1.03159C9.44475 0.844449 7.10483 1.49308 5.18713 2.86303C3.26944 4.23299 1.89722 6.23624 1.31258 8.51933C0.727946 10.8024 0.96846 13.2186 1.99169 15.3417Z"
                                stroke="#6A6A6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                    <div class="text-[#454545] self-center text-[12px] mt-1">{{ $commentsCount }}</div>

                    <!-- Three dots menu with dropdown -->
                    <div class="ml-5 pt-[6px] self-center relative z-20" x-data="{ open: false, showReasonModal: false, showDeleteModal: false, reason: '' }">

                        <button @click="open = !open" class="cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="4" height="18" viewBox="0 0 4 18"
                                fill="none">
                                <path
                                    d="M2 10C2.552 10 3 9.552 3 9C3 8.448 2.552 8 2 8C1.448 8 1 8.448 1 9C1 9.552 1.448 10 2 10Z"
                                    stroke="#6A6A6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M2 3C2.552 3 3 2.552 3 2C3 1.448 2.552 1 2 1C1.448 1 1 1.448 1 2C1 2.552 1.448 3 2 3Z"
                                    stroke="#6A6A6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M2 17C2.552 17 3 16.552 3 16C3 15.448 2.552 15 2 15C1.448 15 1 15.448 1 16C1 16.552 1.448 17 2 17Z"
                                    stroke="#6A6A6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                        <!-- Dropdown -->
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg border border-gray-200 py-1">

                            @if ($isOwner)
                                <!-- Delete Button -->
                                <button @click="open = false; showDeleteModal = true"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2">
                                    Delete
                                </button>
                            @else
                                <!-- Report Button -->
                                <button @click="open = false; showReasonModal = true"
                                    class="w-full text-left px-4 py-2 text-sm text-[#454545] hover:bg-gray-100 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                                        <line x1="4" y1="22" x2="4" y2="15" />
                                    </svg>
                                    Report
                                </button>
                            @endif
                        </div>

                        <!-- Report Modal -->
                        <template x-teleport="body">
                            <div x-show="showDeleteModal" @click.self="showDeleteModal = false" x-transition
                                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-[9999]">
                                <div class="p-8 bg-[#fafafa] rounded-[16px] w-[400px] flex flex-col gap-[20px]">
                                    <h3 class="text-[20px] font-bold text-[#454545]">Delete Post</h3>
                                    <p class="text-sm text-[#6a6a6a]">
                                        Are you sure you want to delete this post? This action cannot be undone.
                                    </p>

                                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="flex justify-end gap-3 mt-4">
                                            <button type="button" @click="showDeleteModal = false"
                                                class="px-4 py-2 text-sm text-[#454545] bg-gray-200 rounded-lg hover:bg-gray-300">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700">
                                                Delete
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </template>

                        <!-- Delete Modal -->
                        <template x-teleport="body">
                            <div x-show="showReasonModal" @click.self="showReasonModal = false" x-transition
                                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-[9999]">
                                <div class="p-8 bg-[#fafafa] rounded-[16px] w-[400px] flex flex-col gap-[20px]">
                                    <h3 class="text-[20px] font-bold text-[#454545]">Report Post</h3>

                                    <form action="{{ route('reports.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="post_id" value="{{ $postId }}">

                                        <textarea name="reason" x-model="reason" rows="4"
                                            class="block p-2.5 w-full text-sm text-[#454545] bg-white rounded-lg border border-[#dddddd] focus:ring-[#e4800d] focus:border-[#e4800d] mb-4"
                                            placeholder="Why are you reporting this post? (Optional)"></textarea>

                                        <div class="flex justify-end gap-3">
                                            <button type="button" @click="showReasonModal = false; reason = ''"
                                                class="px-4 py-2 text-sm text-[#454545] bg-gray-200 rounded-lg hover:bg-gray-300">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                class="px-4 py-2 text-sm text-white bg-[#FF9013] rounded-lg hover:bg-[#d77506]">
                                                Submit Report
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </template>

                    </div>

                </div>
            </div>

            <div class="mb-2.5 flex justify-start items-start text-[16px] text-[#454545] leading-[30px]">
                {{ $content }}
            </div>

        </div>
    </div>
</div>
