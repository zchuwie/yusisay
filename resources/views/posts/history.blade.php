<x-app-layout>
    @if (Auth::check() && !Auth::user()->hasVerifiedEmail())
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        {{ __('Please verify your account first!') }}
                    </div>
                </div>
            </div>
        </div>
    @else
        @forelse ($posts as $post)
            <x-post-card :post="$post" :username="$post->is_anonymous ? 'Anonymous' : $post->user->name" :time="$post->created_at->diffForHumans()" :content="$post->content" :commentsCount="$post->comments->count()"
                :postId="$post->id" :isOwner="Auth::check() && Auth::id() === $post->user_id" />
        @empty
            <p class="text-gray-500 mt-10">You haven't posted anything yet.</p>
        @endforelse
    @endif
</x-app-layout>
