@props(['postId'])

<div x-show="showReportModal" @click.self="showReportModal = false" x-transition
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
    <div class="p-8 bg-[#fafafa] rounded-[16px] w-[400px] flex flex-col gap-[20px]">
        <h3 class="text-[20px] font-bold text-[#454545]">Report Post</h3>
        <form action="{{ route('reports.store') }}" method="POST">
            @csrf
            <input type="hidden" name="post_id" value="{{ $postId }}">
            <textarea name="reason" x-model="reason" rows="4"
                class="block p-2.5 w-full text-sm text-[#454545] bg-white rounded-lg border border-[#dddddd] focus:ring-[#e4800d] focus:border-[#e4800d] mb-4"
                placeholder="Why are you reporting this post? (Optional)"></textarea>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showReportModal = false; reason = ''"
                    class="px-4 py-2 text-sm text-[#454545] bg-gray-200 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm text-white bg-[#FF9013] rounded-lg hover:bg-[#d77506]">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>