<x-app-layout>

    @forelse ($posts as $post)
        <x-post-card :post="$post" :username="$post->is_anonymous ? 'Anonymous' : $post->user->name" :time="$post->created_at->diffForHumans()" :content="$post->content" :commentsCount="$post->comments->count()"
            :postId="$post->id" :isOwner="Auth::check() && Auth::id() === $post->user_id" />
    @empty
        <p class="text-gray-500 mt-10">No one posted anything yet.</p>
    @endforelse


    @if (Auth::check() && Auth::user()->hasVerifiedEmail())
        <div x-data="{ open: false }">
            <x-add-post-button @click="open = true" />
            <x-add-post-modal />
        </div>
    @endif

</x-app-layout>
