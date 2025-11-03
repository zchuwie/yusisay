<x-app-layout>
    @forelse ($posts as $post)
        <x-post-card :username="$post->is_anonymous ? 'Anonymous' : $post->user->name" :time="$post->created_at->diffForHumans()" :content="$post->content" :commentsCount="$post->comments->count()" :postId="$post->id" />
    @empty
        <p class="text-gray-500 mt-10">You haven't posted anything yet.</p>
    @endforelse
</x-app-layout>
