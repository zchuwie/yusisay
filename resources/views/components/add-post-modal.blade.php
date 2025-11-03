<!-- Modal -->
<div x-show="open" @click.self="open = false" x-transition
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">

    <div class="p-8 bg-[#fafafa] rounded-[16px] w-[40%] flex flex-col gap-[20px]">
        <!-- Form -->
        <form method="POST" action="{{ route('posts.store') }}" @submit="open = false" class="w-full mx-auto">

            @csrf

            <!-- Post content -->
            <textarea name="content" id="message" rows="4" required minlength="1"
                class="block p-2.5 w-full text-sm text-[#454545] bg-[#FAFAFA] rounded-lg border border-[#dddddd] focus:ring-[#e4800d] focus:border-[#e4800d]"
                placeholder="What's on your mind?"></textarea>

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
</div>
