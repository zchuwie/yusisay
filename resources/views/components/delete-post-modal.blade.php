@props(['postId', 'showDeleteModal', 'isDeleting'])
<div x-show="showDeleteModal" @click.self="showDeleteModal = false" x-transition
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
    
    <div class="p-8 bg-[#fafafa] rounded-[16px] w-[400px] flex flex-col gap-[20px]">
        <h3 class="text-[20px] font-bold text-[#454545]">Delete Post</h3>
        <p class="text-sm text-[#6a6a6a]">
            Are you sure you want to delete this post? This action cannot be undone.
        </p>

        <form action="{{ route('posts.destroy', $postId) }}" method="POST" @submit="isDeleting = true">
            @csrf
            @method('DELETE')

            <div class="flex justify-end gap-3">
                <button type="button" @click="showDeleteModal = false"
                    class="px-4 py-2 text-sm text-[#454545] bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>

                <button type="submit" :disabled="isDeleting"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg flex items-center justify-center gap-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!isDeleting">Delete</span>
                    <span x-show="isDeleting" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2
                                  5.291A7.962 7.962 0 014 12H0c0 3.042 1.135
                                  5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Deleting...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
