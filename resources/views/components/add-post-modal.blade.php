<div x-show="open" @click.self="open = false" x-transition
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">

    <div class="p-2 bg-[#fafafa] rounded-[16px] w-[40%] flex flex-col gap-[20px]">

        <form method="POST" action="{{ route('posts.store') }}" @submit="open = false" class="p-5">
            @csrf

            @if ($errors->has('content'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-sm text-red-600 font-medium">{{ $errors->first('content') }}</p>
                </div>
            @endif


            <div class="relative">
                <textarea name="content" id="message" rows="4" required minlength="1"
                    class="block p-4 w-full text-sm text-gray-800 bg-gray-50 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all resize-none"
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
</div>
