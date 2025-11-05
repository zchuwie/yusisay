<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Post #' . $post->id) }}</title>

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
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
    @endif
    <nav class="bg-white h-[64px] flex items-center mb-4 px-4 sticky top-0 z-50">
        <a href="{{ route('posts.index') }}" class="text-black hover:underline flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>

        <p class="absolute left-1/2 transform -translate-x-1/2 text-[16px] font-bold text-center">
            Comments ({{ $post->comments->count() }})
        </p>
    </nav>

    @if (session('success'))
        <div class="max-w-[700px] mx-auto mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
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
                        <div class="flex justify-start items-center gap-[10px] w-full">
                            <div class="w-7 h-7 rounded-full overflow-hidden">
                                <img src="your-image-url.jpg" class="w-full h-full object-cover" alt="Profile Picture">
                            </div>
                            <div class="text-[16px] text-[#454545] font-bold">
                                {{ $post->is_anonymous ? 'Anonymous' : $post->user->name }}
                            </div>
                            <div class="mt-[4px] text-[12px] text-[#8d8d8d]">
                                {{ $post->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="flex justify-end items-center">
                            <!-- Three Dots Dropdown -->
                            <div class="ml-[20px] mt-[2px] self-center relative" x-data="{ open: false, showReportModal: false, showDeleteModal: false, reason: '' }">

                                <!-- Trigger -->
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

                                <!-- Dropdown -->
                                <div x-show="open" @click.outside="open = false" x-transition
                                    class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                    @if (Auth::check() && Auth::id() === $post->user_id)
                                        <!-- Delete -->
                                        <button @click="open = false; showDeleteModal = true"
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                            Delete
                                        </button>
                                    @else
                                        <!-- Report -->
                                        <button @click="open = false; showReportModal = true"
                                            class="w-full text-left px-4 py-2 text-sm text-[#454545] hover:bg-gray-100 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                                                <line x1="4" y1="22" x2="4" y2="15" />
                                            </svg>
                                            Report
                                        </button>
                                    @endif
                                </div>

                                <!-- Report Modal -->
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

                                <!-- Delete Modal -->
                                <div x-show="showDeleteModal" @click.self="showDeleteModal = false" x-transition
                                    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
                                    <div class="p-8 bg-[#fafafa] rounded-[16px] w-[400px] flex flex-col gap-[20px]">
                                        <h3 class="text-[20px] font-bold text-[#454545]">Delete Post</h3>
                                        <p class="text-sm text-[#6a6a6a]">Are you sure you want to delete this post?
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
                class="bg-[#fafafa] border border-[#dddddd] pt-5 pb-[30px] px-[37px] w-[700px] rounded-2xl flex flex-col items-center justify-center mb-5">

                <div
                    class="overflow-x-hidden overflow-y-auto max-h-[240px] scrollbar-thin scrollbar-thumb-[#c0c0c0] scrollbar-track-[#f0f0f0] scrollbar-thumb-rounded-[4px] w-full">

                    @forelse($post->comments as $comment)
                        <div class="w-full flex flex-col justify-center gap-[5px] mb-[20px] mt-[20px]">
                            <div class="flex justify-between items-center w-full">
                                <div class="flex justify-start items-center gap-[10px] w-full">
                                    <!-- Comment Profile Picture -->
                                    <x-user-avatar :user="$comment->user" :isAnonymous="$comment->is_anonymous" />

                                    <div class="text-[16px] text-[#454545] font-bold">
                                        {{ $comment->is_anonymous ? 'Anonymous' : $comment->user->name }}
                                    </div>
                                    <div class="mt-[4px] text-[12px] text-[#8d8d8d]">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            <div
                                class="mb-[10px] flex justify-start items-start text-[16px] text-[#454545] leading-[30px]">
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
                    <form action="{{ route('comments.store') }}" method="POST" x-data="{ content: '', isAnonymous: false }"
                        @submit.prevent="if (content.trim().length < 1) { alert('Comment must have at least 1 character!'); } else { $el.submit(); }"
                        class="w-full h-[140px] rounded-[16px] bg-[#ededed] px-[16px] pt-[16px] mt-[16px] flex flex-col">
                        @csrf

                        <input type="hidden" name="post_id" value="{{ $post->id }}">

                        <textarea name="content" x-model="content"
                            class="mb-[5px] bg-transparent border-none outline-none resize-none w-full text-[16px] text-[#454545] h-full overflow-y-auto scrollbar-thin scrollbar-thumb-[#c0c0c0] scrollbar-track-[#f0f0f0] scrollbar-thumb-rounded-[4px] focus:outline-none focus:ring-0 focus:border-transparent"
                            placeholder="What's your comment?"></textarea>

                        @error('content')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror

                        <div
                            class="h-[40px] flex flex-row justify-between items-center pr-[10px] mt-[2px] mb-[10px] pb-2">
                            <label class="pb-[2px] cursor-pointer flex items-center">
                                <input type="checkbox" name="is_anonymous" value="1" x-model="isAnonymous"
                                    class="sr-only peer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="20"
                                    viewBox="0 0 22 20" :class="isAnonymous ? 'stroke-[#FF9013]' : 'stroke-[#6A6A6A]'"
                                    fill="none">
                                    <path
                                        d="M13 16.0007C13 15.4702 12.7893 14.9615 12.4142 14.5864C12.0391 14.2114 11.5304 14.0007 11 14.0007C10.4696 14.0007 9.96086 14.2114 9.58579 14.5864C9.21071 14.9615 9 15.4702 9 16.0007M13 16.0007C13 17.6575 14.3431 19.0007 16 19.0007C17.6569 19.0007 19 17.6575 19 16.0007C19 14.3438 17.6569 13.0007 16 13.0007C14.3431 13.0007 13 14.3438 13 16.0007ZM9 16.0007C9 17.6575 7.65685 19.0007 6 19.0007C4.34315 19.0007 3 17.6575 3 16.0007C3 14.3438 4.34315 13.0007 6 13.0007C7.65685 13.0007 9 14.3438 9 16.0007ZM18 9.00066L15.89 2.34366C15.7976 2.0778 15.6502 1.83443 15.4573 1.62945C15.2645 1.42448 15.0305 1.26252 14.7708 1.15416C14.511 1.04581 14.2313 0.993495 13.9499 1.00065C13.6686 1.0078 13.3919 1.07425 13.138 1.19566L11.862 1.80566C11.5928 1.93413 11.2983 2.00075 11 2.00066H7.5C7.06434 2.00057 6.64057 2.14274 6.29311 2.40555C5.94565 2.66835 5.6935 3.03743 5.575 3.45666L4 9.00066M1 9.00066H21"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </label>
                            <button type="submit" class="cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                    viewBox="0 0 22 22" fill="none">
                                    <path
                                        d="M9.91337 12.0877C9.72226 11.897 9.49449 11.7469 9.24381 11.6465L1.31381 8.4665C1.21912 8.42851 1.13833 8.36246 1.08226 8.27722C1.0262 8.19199 0.997552 8.09164 1.00016 7.98966C1.00278 7.88767 1.03652 7.78892 1.09688 7.70667C1.15723 7.62442 1.2413 7.56259 1.33781 7.5295L20.3378 1.0295C20.4264 0.997494 20.5223 0.991386 20.6143 1.01189C20.7062 1.03239 20.7904 1.07866 20.857 1.14528C20.9236 1.21189 20.9699 1.2961 20.9904 1.38805C21.0109 1.48 21.0048 1.57589 20.9728 1.6645L14.4728 20.6645C14.4397 20.761 14.3779 20.8451 14.2956 20.9054C14.2134 20.9658 14.1146 20.9995 14.0126 21.0021C13.9107 21.0048 13.8103 20.9761 13.7251 20.92C13.6398 20.864 13.5738 20.7832 13.5358 20.6885L10.3558 12.7565C10.255 12.506 10.1045 12.2785 9.91337 12.0877ZM9.91337 12.0877L20.8538 1.1495"
                                        stroke="#6A6A6A" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

</body>

</html>
