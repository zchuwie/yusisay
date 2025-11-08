@props(['posts'])

<div>
    <p class="block text-[18px] font-semibold text-gray-800 mb-4">
        Your Feed
    </p>
    <div class="flex flex-col justify-start items-center">
        @forelse ($posts as $post)
            <x-post-card 
                :post="$post" 
                :username="$post->is_anonymous ? 'Anonymous' : $post->user->name" 
                :time="$post->created_at->diffForHumans()" 
                :content="$post->content"
                :commentsCount="$post->comments->count()" 
                :postId="$post->id" 
                :isOwner="Auth::check() && Auth::id() === $post->user_id" 
            />
        @empty
            <p class="text-gray-500 mt-10 text-center">
                No one posted anything yet.
            </p>
        @endforelse
    </div>

    @if ($posts->count() > 0)
        <div class="pt-9 flex justify-center text-gray-500">
            Congrats! You've reached the end.
        </div>
    @endif
</div>