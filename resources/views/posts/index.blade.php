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
 
@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showToast("{{ session('success') }}");
        });
    </script>
@endif

<script>
    function showToast(message, duration = 3000) {
        let container = document.getElementById('toastContainer');

        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'fixed right-4 top-4 z-50 pointer-events-none';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className =
            'bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg toast-enter flex items-center gap-2 mb-2 transition-all duration-300';
        toast.innerHTML = `
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
            </svg>
            <span>${message}</span>
        `;
        container.appendChild(toast);

        // Auto dismiss animation
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
</script>
