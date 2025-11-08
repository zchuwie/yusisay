@props(['postId'])

@if ($errors->has('content'))
    <div class="w-full mb-3 p-3 bg-red-50 border border-red-200 rounded-xl">
        <p class="text-sm text-red-600 font-medium">{{ $errors->first('content') }}</p>
    </div>
@endif

<form action="{{ route('comments.store') }}" method="POST" 
    x-data="{ content: '{{ old('content') }}', isAnonymous: {{ old('is_anonymous') ? 'true' : 'false' }} }"
    id="commentForm" @submit.prevent="handleCommentSubmit($el)"
    class="w-full rounded-[16px] bg-white border border-gray-200 p-4 mt-[16px] flex flex-col shadow-sm">
    @csrf

    <input type="hidden" name="post_id" value="{{ $postId }}">

    <textarea name="content" x-model="content" rows="2"
        class="mb-4 bg-gray-50 border border-gray-200 rounded-xl resize-none w-full text-[16px] text-gray-800 p-4 overflow-y-auto scrollbar-thin scrollbar-thumb-[#c0c0c0] scrollbar-track-[#f0f0f0] scrollbar-thumb-rounded-[4px] focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('content') border-red-300 @enderror"
        placeholder="What's your say?"></textarea>

    <div class="flex flex-row justify-between items-center w-full pt-4 border-t border-gray-100">
        <label class="inline-flex items-center cursor-pointer group">
            <input type="checkbox" name="is_anonymous" value="1" x-model="isAnonymous"
                class="sr-only peer" {{ old('is_anonymous') ? 'checked' : '' }}>
            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all peer-checked:bg-orange-500 shadow-inner">
            </div>
            <div class="ms-3">
                <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900 transition-colors">Comment Anonymously</span>
                <p class="text-[14px] text-gray-500">Your identity will be hidden</p>
            </div>
        </label>

        <button type="submit"
            class="flex items-center gap-2 text-white bg-[#FF9013] font-semibold rounded-xl text-sm px-6 py-3 shadow hover:bg-[#e68010] transition-colors">
            Comment
        </button>
    </div>
</form>

<script>
    function handleCommentSubmit(form) {
        const content = form.querySelector('textarea[name="content"]').value;

        if (content.trim().length < 1) {
            alert('Comment must have at least 1 character!');
            return;
        }

        form.submit();
    }
</script>