@props(['commentId'])

<div x-data="{ showDeleteModal: false }" class="relative">
    <button @click="showDeleteModal = true"
        class="text-red-600 hover:text-red-700 focus:outline-none transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            <line x1="10" y1="11" x2="10" y2="17"></line>
            <line x1="14" y1="11" x2="14" y2="17"></line>
        </svg>
    </button>

    <div x-show="showDeleteModal" @click.self="showDeleteModal = false" x-transition
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
        <div class="p-8 bg-[#fafafa] rounded-[16px] w-[400px] flex flex-col gap-[20px]">
            <h3 class="text-[20px] font-bold text-[#454545]">Delete Comment</h3>
            <p class="text-sm text-[#6a6a6a]">
                Are you sure you want to delete this comment? This action cannot be undone.
            </p>

            <form action="{{ route('comments.destroy', $commentId) }}" method="POST">
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
</div>