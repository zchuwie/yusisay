<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Post #' . $post->id) }}</title>

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        textarea::-webkit-scrollbar {
            width: 4px;
        }

        textarea::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 4px;
        }

        textarea::-webkit-scrollbar-thumb {
            background: #c9c9c9;
            border-radius: 4px;
        }

        textarea::-webkit-scrollbar-thumb:hover {
            background: #828282;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .toast-enter {
            animation: slideIn 0.3s ease-out;
        }

        .toast-exit {
            animation: slideOut 0.3s ease-out;
        }
    </style>
</head>


<body class="bg-[#f3f4f6]">
    @if (Auth::check() && !Auth::user()->hasVerifiedEmail())
        <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 p-3 rounded text-center">
            Your email is not verified yet.
            <form method="POST" action="{{ route('verification.send') }}" class="inline">
                @csrf
                <button type="submit" class="underline text-blue-600">Be verified</button>
            </form>
        </div>
        <nav class="bg-white h-[64px] flex items-center mb-4 px-4 sticky top-0 z-50">
            <button onclick="handleBack()" class="text-black hover:underline flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </button>

            <p class="absolute left-1/2 transform -translate-x-1/2 text-[16px] font-bold text-center">
                Comments ({{ $post->comments->count() }})
            </p>
        </nav>


        <div class="relative group">
            <div class="flex flex-col items-center justify-center gap-[10px]">
                <div x-data="{
                    expanded: true,
                    showButton: false,
                    checkHeight() {
                        this.$nextTick(() => {
                            const el = this.$refs.content;
                            if (el.scrollHeight > 134) {
                                this.showButton = true;
                            }
                        });
                    }
                }" x-init="checkHeight"
                    class="relative bg-[#fafafa] border border-[#dddddd] p-[20px] px-[37px] w-[700px] rounded-2xl flex flex-col items-center justify-center mb-2">
                    <div class="w-full flex flex-col justify-center gap-[10px]">
                        <div class="flex justify-between items-center w-full">
                            <div class="flex items-center gap-3 flex-1">
                                <x-user-avatar :user="$post->user" :isAnonymous="$post->is_anonymous" />

                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-900 text-sm">
                                            {{ $post->is_anonymous ? 'Anonymous' : $post->user->name }}
                                        </span>
                                        @if ($isOwner)
                                            <span
                                                class="px-2 py-0.5 text-xs font-medium text-green-700 bg-green-50 rounded-full">
                                                You
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        {{ $post->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex justify-end items-center">

                                <div class="ml-[20px] mt-[2px] self-center relative" x-data="{ open: false, showReportModal: false, showDeleteModal: false, reason: '' }">

                                    <button class="cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="4" height="18"
                                            viewBox="0 0 4 18" fill="none">
                                            <path
                                                d="M2 10C2.55 10 3 9.55 3 9C3 8.45 2.55 8 2 8C1.45 8 1 8.45 1 9C1 9.55 1.45 10 2 10Z"
                                                stroke="#6A6A6A" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M2 3C2.55 3 3 2.55 3 2C3 1.45 2.55 1 2 1C1.45 1 1 1.45 1 2C1 2.55 1.45 3 2 3Z"
                                                stroke="#6A6A6A" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M2 17C2.55 17 3 16.55 3 16C3 15.45 2.55 15 2 15C1.45 15 1 15.45 1 16C1 16.55 1.45 17 2 17Z"
                                                stroke="#6A6A6A" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </button>

                                    <div x-show="open" x-transition
                                        class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                        @if (Auth::check() && Auth::id() === $post->user_id)
                                            <button @click="open = false; showDeleteModal = true"
                                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                                Delete
                                            </button>
                                        @else
                                            <button @click="open = false; showReportModal = true"
                                                class="w-full text-left px-4 py-2 text-sm text-[#454545] hover:bg-gray-100 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path
                                                        d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                                                    <line x1="4" y1="22" x2="4" y2="15" />
                                                </svg>
                                                Report
                                            </button>
                                        @endif
                                    </div>


                                    <div x-show="showReportModal" @click.self="showReportModal = false" x-transition
                                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
                                        <div class="p-8 bg-[#fafafa] rounded-[16px] w-[400px] flex flex-col gap-[20px]">
                                            <h3 class="text-[20px] font-bold text-[#454545]">Report Post</h3>
                                            <form action="{{ route('reports.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="post_id" value="{{ $post->id }}">
                                                <textarea name="reason" x-model="reason" rows="4"
                                                    class="block p-2.5 w-full text-sm text-[#454545] bg-white rounded-lg border border-[#dddddd] focus:ring-[#e4800d] focus:border-[#e4800d] mb-4"
                                                    placeholder="Why are you reporting this post? (Optional)"></textarea>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="showReportModal = false; reason = ''"
                                                        class="px-4 py-2 text-sm text-[#454545] bg-gray-200 rounded-lg hover:bg-gray-300">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                        class="px-4 py-2 text-sm text-white bg-[#FF9013] rounded-lg hover:bg-[#d77506]">
                                                        Submit
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>


                                    <div x-show="showDeleteModal" @click.self="showDeleteModal = false" x-transition
                                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
                                        <div
                                            class="p-8 bg-[#fafafa] rounded-[16px] w-[400px] flex flex-col gap-[20px]">
                                            <h3 class="text-[20px] font-bold text-[#454545]">Delete Post</h3>
                                            <p class="text-sm text-[#6a6a6a]">Are you sure you want to delete this
                                                post?
                                                This action cannot be undone.</p>
                                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="flex justify-end gap-3">
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
                                </div>

                            </div>
                        </div>

                        <div x-ref="content" :class="expanded ? 'max-h-none' : 'max-h-[134px] overflow-hidden'"
                            class="transition-all duration-300 ease-in-out mb-[10px] flex justify-start items-start text-[16px] text-[#454545] leading-[30px]">
                            {{ $post->content }}
                        </div>
                    </div>

                    <button x-show="showButton" @click="expanded = !expanded" x-transition
                        class="absolute bottom-[10px] right-[10px] text-[#6a6a6a] text-[13px] hover:underline focus:outline-none">
                        <span x-text="expanded ? 'Minimize' : 'Expand'"></span>
                    </button>
                </div>
            </div>
        </div>
        <x-verify-card></x-verify-card>
    @else
        <div id="toastContainer" class="fixed right-4 top-20 z-50 pointer-events-none"></div>

        <nav class="bg-white h-[64px] flex items-center mb-4 px-4 sticky top-0 z-50">
            <button onclick="handleBack()" class="text-black hover:underline flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </button>

            <p class="absolute left-1/2 transform -translate-x-1/2 text-[16px] font-bold text-center">
                Comments ({{ $post->comments->count() }})
            </p>
        </nav>

        @if (session('success'))
            <script>
                window.addEventListener('load', function() {
                    setTimeout(() => {
                        showToast('{{ session('success') }}');
                    }, 100);
                });
            </script>
        @endif

        <div class="relative group">
            <div class="flex flex-col items-center justify-center gap-[10px]">
                <div x-data="{
                    expanded: true,
                    showButton: false,
                    checkHeight() {
                        this.$nextTick(() => {
                            const el = this.$refs.content;
                            if (el.scrollHeight > 134) {
                                this.showButton = true;
                            }
                        });
                    }
                }" x-init="checkHeight"
                    class="relative bg-[#fafafa] border border-[#dddddd] p-[20px] px-[37px] w-[700px] rounded-2xl flex flex-col items-center justify-center mb-2">
                    <div class="w-full flex flex-col justify-center gap-[10px]">
                        <div class="flex justify-between items-center w-full">
                            <div class="flex items-center gap-3 flex-1">
                                <x-user-avatar :user="$post->user" :isAnonymous="$post->is_anonymous" />

                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-900 text-sm">
                                            {{ $post->is_anonymous ? 'Anonymous' : $post->user->name }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        {{ $post->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex justify-end items-center">

                                <div class="ml-[20px] mt-[2px] self-center relative" x-data="{ open: false, showReportModal: false, showDeleteModal: false, reason: '' }">


                                    <button @click="open = !open" class="cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="4" height="18"
                                            viewBox="0 0 4 18" fill="none">
                                            <path
                                                d="M2 10C2.55 10 3 9.55 3 9C3 8.45 2.55 8 2 8C1.45 8 1 8.45 1 9C1 9.55 1.45 10 2 10Z"
                                                stroke="#6A6A6A" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M2 3C2.55 3 3 2.55 3 2C3 1.45 2.55 1 2 1C1.45 1 1 1.45 1 2C1 2.55 1.45 3 2 3Z"
                                                stroke="#6A6A6A" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M2 17C2.55 17 3 16.55 3 16C3 15.45 2.55 15 2 15C1.45 15 1 15.45 1 16C1 16.55 1.45 17 2 17Z"
                                                stroke="#6A6A6A" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </button>


                                    <div x-show="open" @click.outside="open = false" x-transition
                                        class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                        @if (Auth::check() && Auth::id() === $post->user_id)
                                            <button @click="open = false; showDeleteModal = true"
                                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                                Delete
                                            </button>
                                        @else
                                            <button @click="open = false; showReportModal = true"
                                                class="w-full text-left px-4 py-2 text-sm text-[#454545] hover:bg-gray-100 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path
                                                        d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                                                    <line x1="4" y1="22" x2="4"
                                                        y2="15" />
                                                </svg>
                                                Report
                                            </button>
                                        @endif
                                    </div>


                                    <div x-show="showReportModal" @click.self="showReportModal = false" x-transition
                                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
                                        <div
                                            class="p-8 bg-[#fafafa] rounded-[16px] w-[400px] flex flex-col gap-[20px]">
                                            <h3 class="text-[20px] font-bold text-[#454545]">Report Post</h3>
                                            <form action="{{ route('reports.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="post_id" value="{{ $post->id }}">
                                                <textarea name="reason" x-model="reason" rows="4"
                                                    class="block p-2.5 w-full text-sm text-[#454545] bg-white rounded-lg border border-[#dddddd] focus:ring-[#e4800d] focus:border-[#e4800d] mb-4"
                                                    placeholder="Why are you reporting this post? (Optional)"></textarea>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button"
                                                        @click="showReportModal = false; reason = ''"
                                                        class="px-4 py-2 text-sm text-[#454545] bg-gray-200 rounded-lg hover:bg-gray-300">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                        class="px-4 py-2 text-sm text-white bg-[#FF9013] rounded-lg hover:bg-[#d77506]">
                                                        Submit
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>


                                    <div x-show="showDeleteModal" @click.self="showDeleteModal = false" x-transition
                                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
                                        <div
                                            class="p-8 bg-[#fafafa] rounded-[16px] w-[400px] flex flex-col gap-[20px]">
                                            <h3 class="text-[20px] font-bold text-[#454545]">Delete Post</h3>
                                            <p class="text-sm text-[#6a6a6a]">Are you sure you want to delete this
                                                post?
                                                This action cannot be undone.</p>
                                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="flex justify-end gap-3">
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
                                </div>

                            </div>
                        </div>

                        <div x-ref="content" :class="expanded ? 'max-h-none' : 'max-h-[134px] overflow-hidden'"
                            class="transition-all duration-300 ease-in-out mb-[10px] flex justify-start items-start text-[16px] text-[#454545] leading-[30px]">
                            {{ $post->content }}
                        </div>
                    </div>

                    <button x-show="showButton" @click="expanded = !expanded" x-transition
                        class="absolute bottom-[10px] right-[10px] text-[#6a6a6a] text-[13px] hover:underline focus:outline-none">
                        <span x-text="expanded ? 'Minimize' : 'Expand'"></span>
                    </button>
                </div>

                <div
                    class="bg-[#fafafa] border border-[#dddddd] p-5 px-[37px] w-[700px] rounded-2xl flex flex-col items-center justify-between h-[67vh]">


                    <div
                        class="overflow-x-hidden overflow-y-auto flex-1 w-full scrollbar-thin scrollbar-thumb-[#c0c0c0] scrollbar-track-[#f0f0f0] scrollbar-thumb-rounded-[4px]">

                        @forelse($post->comments as $comment)
                            <div class="w-full flex flex-col justify-center gap-[5px] mb-[32px] mt-[20px] pr-3">
                                <div class="flex justify-between items-center w-full mb-1">
                                    {{-- <div class="flex justify-start items-center gap-[10px] w-full">
                                        <x-user-avatar :user="$comment->user" :isAnonymous="$comment->is_anonymous" />
                                        <div class="text-[16px] text-[#454545] font-bold">
                                            {{ $comment->is_anonymous ? 'Anonymous' : $comment->user->name }}
                                        </div>
                                        <div class="mt-[4px] text-[12px] text-[#8d8d8d]">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </div>
                                    </div> --}}

                                    <div class="flex items-center gap-3 flex-1">
                                        <x-user-avatar :user="$comment->user" :isAnonymous="$comment->is_anonymous" />

                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-gray-900 text-sm">
                                                    {{ $comment->is_anonymous ? 'Anonymous' : $comment->user->name }}
                                                </span>
                                            </div>
                                            <span class="text-xs text-gray-500">
                                                {{ $comment->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>

                                    @if (Auth::check() && Auth::id() === $comment->user_id)
                                        <div x-data="{ showDeleteModal: false }" class="relative">
                                            <button @click="showDeleteModal = true"
                                                class="text-red-600 hover:text-red-700 focus:outline-none transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3
                     0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                    </path>
                                                    <line x1="10" y1="11" x2="10"
                                                        y2="17">
                                                    </line>
                                                    <line x1="14" y1="11" x2="14"
                                                        y2="17">
                                                    </line>
                                                </svg>
                                            </button>

                                            <div x-show="showDeleteModal" @click.self="showDeleteModal = false"
                                                x-transition
                                                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
                                                <div
                                                    class="p-8 bg-[#fafafa] rounded-[16px] w-[400px] flex flex-col gap-[20px]">
                                                    <h3 class="text-[20px] font-bold text-[#454545]">Delete Comment
                                                    </h3>
                                                    <p class="text-sm text-[#6a6a6a]">
                                                        Are you sure you want to delete this comment? This action cannot
                                                        be undone.
                                                    </p>

                                                    <form action="{{ route('comments.destroy', $comment->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="flex justify-end gap-3">
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
                                        </div>
                                    @endif
                                </div>

                                <div class="w-full text-[16px] text-[#454545] break-words overflow-hidden">
                                    {{ $comment->content }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-8">
                                No comments yet. Be the first to comment!
                            </div>
                        @endforelse
                    </div>

                    @if (Auth::check() && Auth::user()->hasVerifiedEmail())

                        @if ($errors->has('content'))
                            <div class="w-full mb-3 p-3 bg-red-50 border border-red-200 rounded-xl">
                                <p class="text-sm text-red-600 font-medium">{{ $errors->first('content') }}</p>
                            </div>
                        @endif

                        <form action="{{ route('comments.store') }}" method="POST" x-data="{ content: '{{ old('content') }}', isAnonymous: {{ old('is_anonymous') ? 'true' : 'false' }} }"
                            id="commentForm" @submit.prevent="handleCommentSubmit($el)"
                            class="w-full rounded-[16px] bg-white border border-gray-200 p-4 mt-[16px] flex flex-col shadow-sm">
                            @csrf

                            <input type="hidden" name="post_id" value="{{ $post->id }}">

                            <textarea name="content" x-model="content" rows="2"
                                class="mb-4 bg-gray-50 border border-gray-200 rounded-xl resize-none w-full text-[16px] text-gray-800 p-4 overflow-y-auto scrollbar-thin scrollbar-thumb-[#c0c0c0] scrollbar-track-[#f0f0f0] scrollbar-thumb-rounded-[4px] focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('content') border-red-300 @enderror"
                                placeholder="What's your say?"></textarea>

                            <div
                                class="flex flex-row justify-between items-center w-full pt-4 border-t border-gray-100">
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="checkbox" name="is_anonymous" value="1" x-model="isAnonymous"
                                        class="sr-only peer" {{ old('is_anonymous') ? 'checked' : '' }}>
                                    <div
                                        class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all peer-checked:bg-orange-500 shadow-inner">
                                    </div>
                                    <div class="ms-3">
                                        <span
                                            class="text-sm font-semibold text-gray-700 group-hover:text-gray-900 transition-colors">Comment
                                            Anonymously</span>
                                        <p class="text-[14px] text-gray-500">Your identity will be hidden</p>
                                    </div>
                                </label>

                                <button type="submit"
                                    class="flex items-center gap-2 text-white bg-[#FF9013] font-semibold rounded-xl text-sm px-6 py-3 shadow hover:bg-[#e68010] transition-colors">
                                    Comment
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <script>
        function showToast(message, duration = 3000) {
            let container = document.getElementById('toastContainer');

            if (!container) {
                container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'fixed right-4 top-4 z-50 pointer-events-none';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className =
                'bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg toast-enter flex items-center gap-2 mb-2';
            toast.innerHTML = `
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                    </svg>
                    <span>${message}</span>
                `;
            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }

        function handleCommentSubmit(form) {
            const content = form.querySelector('textarea[name="content"]').value;

            if (content.trim().length < 1) {
                alert('Comment must have at least 1 character!');
                return;
            }

            form.submit();
        }

        function handleBack() {
            window.location.replace("{{ route('posts.index') }}");
        }
    </script>

</body>

</html>
