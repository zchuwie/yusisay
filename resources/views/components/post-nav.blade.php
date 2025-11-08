@props(['commentsCount'])

<nav class="bg-[#FF9013] h-[64px] flex items-center mb-4 px-4 sticky top-0 z-50">
    <button onclick="handleBack()" class="text-[#FAFAFA] hover:underline flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back
    </button>

    <p class="absolute left-1/2 transform -translate-x-1/2 text-[16px] text-[#FAFAFA] font-bold text-center">
        Comments ({{ $commentsCount }})
    </p>
</nav>

<script>
    function handleBack() {
        window.location.replace("{{ route('posts.index') }}");
    }
</script>