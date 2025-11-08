@props(['comment'])

<div class="w-full flex flex-col justify-center gap-[5px] mb-[32px] mt-[20px] pr-3">
    <div class="flex justify-between items-center w-full mb-1">
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
            <x-delete-comment-button :commentId="$comment->id" />
        @endif
    </div>

    <div class="w-full text-[16px] text-[#454545] break-words overflow-hidden">
        {{ $comment->content }}
    </div>
</div>