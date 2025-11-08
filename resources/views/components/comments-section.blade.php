@props(['post'])

<div class="bg-[#fafafa] border border-[#dddddd] p-5 px-[37px] w-[700px] rounded-2xl flex flex-col items-center justify-between h-[65vh]">
     
    <div class="overflow-x-hidden overflow-y-auto flex-1 w-full scrollbar-thin scrollbar-thumb-[#c0c0c0] scrollbar-track-[#f0f0f0] scrollbar-thumb-rounded-[4px]">
        @forelse($post->comments as $comment)
            <x-comment-item :comment="$comment" />
        @empty
            <div class="text-center text-gray-500 py-8">
                No comments yet. Be the first to comment!
            </div>
        @endforelse
    </div>
 
    @if (Auth::check() && Auth::user()->hasVerifiedEmail())
        <x-comment-form :postId="$post->id" />
    @endif
</div>