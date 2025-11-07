<x-app-layout>
    <div class="max-w-2xl mx-auto px-4"> 

        @if (Auth::check() && Auth::user()->hasVerifiedEmail())
            <div class="bg-[#FAFAFA] rounded-2xl shadow border border-gray-100 mb-6 overflow-hidden">
                <div class="bg-[#FF9013] p-4">
                    <div class="flex items-center gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-white">What's on your mind?</h2>
                            <p class="text-orange-100 text-[14px]">Share your thoughts with fellow Yusisistas</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('posts.store') }}" @submit="open = false" class="p-5">
                    @csrf

 
                    <div class="relative">
                        <textarea name="content" id="message" rows="4" required minlength="1"
                            class="block p-4 w-full text-sm text-gray-800 bg-[#FAFAFA] rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all resize-none"
                            placeholder="Spill it out! Share what's on your mind..."></textarea>
                    </div>

 
                    <div class="flex flex-row justify-between items-center w-full mt-4 pt-4 border-t border-gray-100">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" name="is_anonymous" value="1" class="sr-only peer">
                            <div
                                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all peer-checked:bg-orange-500 shadow-inner">
                            </div>
                            <div class="ms-3">
                                <span
                                    class="text-sm font-semibold text-gray-700 group-hover:text-gray-900 transition-colors">Post
                                    Anonymously</span>
                                <p class="text-[14px] text-gray-500">Your identity will be hidden</p>
                            </div>
                        </label>
 
                        <button type="submit" x-ref="submitBtn"
                            class="flex items-center gap-2 text-white bg-[#FF9013] font-semibold rounded-xl text-sm px-6 py-3 shadow">
                            Post Now
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="rounded-xl mt-6 mb-4 shadow overflow-hidden">
                <div class="bg-[#FF9013] p-4 ">
                    <div class="flex items-center gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-white">What's on your mind?</h2>
                            <p class="text-orange-100 text-[14px]">Share your thoughts with fellow Yusisistas</p>
                        </div>
                    </div>
                </div>
                <div class=" bg-[#FAFAFA] p-8 text-center ">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Verify email address to post</h3>
                    <p class="text-sm text-gray-500 mb-4">Join the conversation and share your thoughts</p>
                </div>
            </div>
        @endif

        <p class="block text-sm font-semibold text-gray-800 mb-4">
            Your Feed
        </p>
        <div class="flex flex-col justify-start items-center">
            @forelse ($posts as $post)
                <x-post-card :post="$post" :username="$post->is_anonymous ? 'Anonymous' : $post->user->name" :time="$post->created_at->diffForHumans()" :content="$post->content" :commentsCount="$post->comments->count()"
                    :postId="$post->id" :isOwner="Auth::check() && Auth::id() === $post->user_id" />
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
 
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
</script>
