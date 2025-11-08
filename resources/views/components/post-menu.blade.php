@props(['post'])

<div class="ml-[20px] mt-[2px] self-center relative" x-data="{ open: false, showReportModal: false, showDeleteModal: false, reason: '' }">
    <button @click="open = !open" class="cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" width="4" height="18" viewBox="0 0 4 18" fill="none">
            <path d="M2 10C2.55 10 3 9.55 3 9C3 8.45 2.55 8 2 8C1.45 8 1 8.45 1 9C1 9.55 1.45 10 2 10Z"
                stroke="#6A6A6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M2 3C2.55 3 3 2.55 3 2C3 1.45 2.55 1 2 1C1.45 1 1 1.45 1 2C1 2.55 1.45 3 2 3Z"
                stroke="#6A6A6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M2 17C2.55 17 3 16.55 3 16C3 15.45 2.55 15 2 15C1.45 15 1 15.45 1 16C1 16.55 1.45 17 2 17Z"
                stroke="#6A6A6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
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
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                    <line x1="4" y1="22" x2="4" y2="15" />
                </svg>
                Report
            </button>
        @endif
    </div>
 
    <x-report-modal :postId="$post->id" />
     
    <x-delete-post-modal :postId="$post->id" />
</div>