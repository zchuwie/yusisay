<x-app-layout>

    @foreach ($posts as $post)
        <x-post-card :username="$post->is_anonymous ? 'Anonymous' : $post->user->name" :time="$post->created_at->diffForHumans()" :content="$post->content" :commentsCount="$post->comments->count()" :postId="$post->id" />
    @endforeach

    <div x-data="{ open: false }">
        <x-add-post-button @click="open = true" />
        <x-add-post-modal />
    </div>

</x-app-layout>
