@props(['postId'])

@if ($errors->has('content'))
    <div class="w-full mb-3 p-3 bg-red-50 border border-red-200 rounded-xl">
        <p class="text-sm text-red-600 font-medium">{{ $errors->first('content') }}</p>
    </div>
@endif

<form action="{{ route('comments.store') }}" method="POST" x-data="{
    content: '{{ old('content') }}',
    isAnonymous: {{ old('is_anonymous') ? 'true' : 'false' }},
    isSubmitting: false
}" id="commentForm"
    @submit.prevent="handleCommentSubmit($el)"
    class="w-full rounded-[16px] bg-white border border-gray-200 p-4 mt-[16px] flex flex-col shadow-sm">
    @csrf

    <input type="hidden" name="post_id" value="{{ $postId }}">

    <textarea name="content" x-model="content" rows="2" :disabled="isSubmitting"
        class="mb-4 bg-gray-50 border border-gray-200 rounded-xl resize-none w-full text-[16px] text-gray-800 p-4 overflow-y-auto scrollbar-thin scrollbar-thumb-[#c0c0c0] scrollbar-track-[#f0f0f0] scrollbar-thumb-rounded-[4px] focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all disabled:opacity-50 disabled:cursor-not-allowed @error('content') border-red-300 @enderror"
        placeholder="What's your say?"></textarea>

    <div class="flex flex-row justify-between items-center w-full pt-4 border-t border-gray-100">
        <label class="inline-flex items-center cursor-pointer group">
            <input type="checkbox" name="is_anonymous" value="1" x-model="isAnonymous" :disabled="isSubmitting"
                class="sr-only peer" {{ old('is_anonymous') ? 'checked' : '' }}>
            <div
                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all peer-checked:bg-orange-500 shadow-inner peer-disabled:opacity-50 peer-disabled:cursor-not-allowed">
            </div>
            <div class="ms-3">
                <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900 transition-colors">Comment
                    Anonymously</span>
                <p class="text-[14px] text-gray-500">Your identity will be hidden</p>
            </div>
        </label>

        <button type="submit" :disabled="isSubmitting"
            class="flex items-center gap-2 text-white bg-[#FF9013] font-semibold rounded-xl text-sm px-6 py-3 shadow hover:bg-[#e68010] transition-colors disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#FF9013]">
            <span x-show="!isSubmitting">Comment</span>
            <span x-show="isSubmitting" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Posting...
            </span>
        </button>
    </div>
</form>

<script>
    function handleCommentSubmit(form) {
        const content = form.querySelector('textarea[name="content"]').value;
        const alpineData = Alpine.$data(form);

        if (content.trim().length < 1) {
            alert('Comment must have at least 1 character!');
            return;
        }

        if (alpineData.isSubmitting) {
            return;  
        }

        alpineData.isSubmitting = true;
        form.submit();
    }
</script>
