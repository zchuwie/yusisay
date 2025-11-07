<x-app-layout>
    <div class="max-w-2xl mx-auto px-4">
        {{-- Centered container --}}
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-4">
            <label for="example" class="block text-sm font-semibold text-gray-800">
                What's on your mind?
            </label>
            <p class="text-sm text-gray-500 mt-1 mb-4">
                Share your thoughts with fellow Yusisistas on the website.
            </p>
            <form method="POST" action="{{ route('posts.store') }}" @submit="open = false" class="w-full mx-auto">

                @csrf

                <!-- Post content -->
                <textarea name="content" id="message" rows="4" required minlength="1"
                    class="block p-2.5 w-full text-sm text-[#454545] bg-[#FAFAFA] rounded-lg border border-[#dddddd] focus:ring-[#e4800d] focus:border-[#e4800d]"
                    placeholder="Spill it out!"></textarea>

                <!-- Switch + Submit -->
                <div class="flex flex-row justify-between items-center w-full mt-4">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_anonymous" value="1" class="sr-only peer">
                        <div
                            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#FF9013]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all peer-checked:bg-[#FF9013]">
                        </div>
                        <span class="ms-3 text-sm font-medium text-[#454545]">Anonymous?</span>
                    </label>

                    <!-- Submit -->
                    <button type="submit" x-ref="submitBtn"
                        class="text-[#FAFAFA] bg-[#FF9013] hover:bg-[#d77506] focus:ring-1 focus:ring-[#e4800d] font-medium rounded-lg text-sm px-5 py-2.5">
                        Submit Post
                    </button>
                </div>
            </form>
        </div>
        <p class="block text-sm font-semibold text-gray-800 mb-4">
                Your Feed
            </p>
        @forelse ($posts as $post)
            <x-post-card :post="$post" :username="$post->is_anonymous ? 'Anonymous' : $post->user->name" :time="$post->created_at->diffForHumans()" :content="$post->content" :commentsCount="$post->comments->count()"
                :postId="$post->id" :isOwner="Auth::check() && Auth::id() === $post->user_id" />
        @empty
            <p class="text-gray-500 mt-10 text-center">
                No one posted anything yet.
            </p>
        @endforelse

        @if ($posts->count() > 0)
            <div class="pt-9 flex justify-center text-gray-500">
                Congrats! You've reached the end.
            </div>
        @endif
    </div>

    @if (Auth::check() && Auth::user()->hasVerifiedEmail())
        <div x-data="{ open: false }">
            <div class="fixed bottom-6 right-6 z-50">
                <x-add-post-button @click="open = true" />
            </div>
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
