@props(['postId', 'showReportModal', 'reason', 'isSubmitting'])
<div x-show="showReportModal" @click.self="showReportModal = false" x-transition
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
    <div class="p-8 bg-[#fafafa] rounded-[16px] w-[400px] flex flex-col gap-[20px]">

        <h3 class="text-[20px] font-bold text-[#454545]">Report Post</h3>

        <form action="{{ route('reports.store') }}" method="POST" @submit="isSubmitting = true">
            @csrf
            <input type="hidden" name="post_id" value="{{ $postId }}">
            <textarea name="reason" x-model="reason" rows="4"
                class="block p-2.5 w-full text-sm text-[#454545] bg-white rounded-lg border border-[#dddddd] focus:ring-[#e4800d] focus:border-[#e4800d] mb-4"
                placeholder="Why are you reporting this post? (Optional)"></textarea>

            <div class="flex justify-end gap-3">
                <button type="button" @click="showReportModal = false; reason = ''"
                    class="px-4 py-2 text-sm text-[#454545] bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>

                <button type="submit" :disabled="isSubmitting"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <span x-show="!isSubmitting">Submit Report</span>
                    <span x-show="isSubmitting" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0
                                  0 5.373 0 12h4zm2 5.291A7.962
                                  7.962 0 014 12H0c0 3.042 1.135
                                  5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Submitting...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
