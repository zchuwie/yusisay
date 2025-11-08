@props(['postId'])

<div x-show="showDeleteModal" @click.self="showDeleteModal = false" x-transition
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
    <div class="p-8 bg-[#fafafa] rounded-[16px] w-[400px] flex flex-col gap-[20px]">
        <h3 class="text-[20px] font-bold text-[#454545]">Delete Post</h3>
        <p class="text-sm text-[#6a6a6a]">Are you sure you want to delete this post? This action cannot be undone.</p>
        <form action="{{ route('posts.destroy', $postId) }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-3">
                <button type="button" @click="showDeleteModal = false"
                    class="px-4 py-2 text-sm text-[#454545] bg-gray-200 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Delete
                </button>
            </div>
        </form>
    </div>
</div>