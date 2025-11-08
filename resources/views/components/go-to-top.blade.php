<button id="scrollToTop"
    class="fixed bottom-6 left-6 z-50 text-white bg-orange-500 hover:bg-orange-600
           font-bold rounded-full w-14 h-14 flex items-center justify-center
           shadow-2xl transition-all duration-300
           border-2 border-white/20
           opacity-0 invisible"
    onclick="scrollToTop()">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
</button>

<script>
    const scrollToTopBtn = document.getElementById('scrollToTop');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            scrollToTopBtn.classList.remove('opacity-0', 'invisible');
            scrollToTopBtn.classList.add('opacity-100', 'visible');
        } else {
            scrollToTopBtn.classList.add('opacity-0', 'invisible');
            scrollToTopBtn.classList.remove('opacity-100', 'visible');
        }
    });

    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
</script>