@props(['post', 'username', 'time', 'content', 'commentsCount', 'postId', 'isOwner' => false])

<div class="w-full mb-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">

        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3 flex-1">
                <x-user-avatar :user="$post->user" :isAnonymous="$post->is_anonymous" />

                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-gray-900 text-sm">
                            {{ $post->is_anonymous ? 'Anonymous' : $post->user->name }}
                        </span>
                        @if ($isOwner)
                            <span class="px-2 py-0.5 text-xs font-medium text-green-700 bg-green-50 rounded-full">
                                You
                            </span>
                        @endif
                    </div>
                    <span class="text-xs text-gray-500">
                        {{ $post->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('posts.show', $postId) }}"
                    class="flex items-center gap-1.5 text-gray-600 hover:text-green-600 transition-colors group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 group-hover:scale-110 transition-transform"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="text-sm font-medium">{{ $commentsCount }}</span>
                </a>

                <div class="relative" x-data="{ open: false, showReasonModal: false, showDeleteModal: false, reason: '', isSubmitting: false }">
                    <button @click="open = !open" :disabled="isSubmitting"
                        class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <circle cx="12" cy="5" r="2" />
                            <circle cx="12" cy="12" r="2" />
                            <circle cx="12" cy="19" r="2" />
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                        style="display: none;">

                        @if ($isOwner)
                            <button @click="open = false; showDeleteModal = true"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete Post
                            </button>
                        @else
                            <button @click="open = false; showReasonModal = true"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                </svg>
                                Report Post
                            </button>
                        @endif
                    </div>

                    {{-- DELETE MODAL --}}
                    <template x-teleport="body">
                        <div x-show="showDeleteModal" @click.self="showDeleteModal = false"
                            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4"
                            style="display: none;">
                            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100">

                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">Delete Post</h3>
                                </div>

                                <p class="text-sm text-gray-600 mb-6">
                                    Are you sure you want to delete this post? This action cannot be undone.
                                </p>

                                <form action="{{ route('posts.destroy', $post->id) }}" method="POST"
                                    @submit.prevent="if (!isSubmitting) { isSubmitting = true; $el.submit(); }">
                                    @csrf
                                    @method('DELETE')
                                    <div class="flex gap-3">
                                        <button type="button" @click="showDeleteModal = false" :disabled="isSubmitting"
                                            class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            Cancel
                                        </button>
                                        <button type="submit" :disabled="isSubmitting"
                                            class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                            <span x-show="!isSubmitting">Delete</span>
                                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                Deleting...
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>

                    {{-- REPORT MODAL --}}
                    <template x-teleport="body">
                        <div x-show="showReasonModal" @click.self="showReasonModal = false"
                            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4"
                            style="display: none;">
                            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100">

                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-600"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">Report Post</h3>
                                </div>

                                <form action="{{ route('reports.store') }}" method="POST"
                                    @submit.prevent="if (!isSubmitting) { isSubmitting = true; $el.submit(); }">
                                    @csrf
                                    <input type="hidden" name="post_id" value="{{ $postId }}">

                                    <textarea name="reason" x-model="reason" rows="4" :disabled="isSubmitting"
                                        class="w-full px-3 py-2.5 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-transparent mb-4 resize-none disabled:opacity-50 disabled:cursor-not-allowed"
                                        placeholder="Please describe why you're reporting this post..."></textarea>

                                    <div class="flex gap-3">
                                        <button type="button" @click="showReasonModal = false; reason = ''"
                                            :disabled="isSubmitting"
                                            class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            Cancel
                                        </button>
                                        <button type="submit" :disabled="isSubmitting"
                                            class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                            <span x-show="!isSubmitting">Submit Report</span>
                                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                Submitting...
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="text-gray-800 text-[15px] leading-relaxed">
            {{ $content }}
        </div>
    </div>
</div>
