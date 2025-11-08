<x-app-layout>
    @if (Auth::check() && !Auth::user()->hasVerifiedEmail())
        <x-verify-card></x-verify-card>
    @else
        <div class="max-w-2xl mx-auto px-4">
            <x-content-header title="History" description="All your posts will appear here."></x-content-header> 
            @forelse ($posts as $post)
                <x-post-card :post="$post" :username="$post->is_anonymous ? 'Anonymous' : $post->user->name" :time="$post->created_at->diffForHumans()" :content="$post->content" :commentsCount="$post->comments->count()"
                    :postId="$post->id" :isOwner="Auth::check() && Auth::id() === $post->user_id" />
            @empty
                <p class="text-gray-500 mt-10 text-center">
                    You haven't posted anything yet.
                </p>
            @endforelse
        </div>


        @if ($posts->count() > 0)
            <div class="pt-9 flex justify-center text-gray-500">
                Congrats! You've reached the end.
            </div>
        @endif
    @endif
</x-app-layout>
