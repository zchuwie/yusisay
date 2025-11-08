<button {{ $attributes->merge(['type' => 'submit', 'class' => 'flex items-center gap-2 text-white <bg-red-6></bg-red-6>00 font-semibold rounded-xl text-sm px-6 py-3 shadow']) }}>
    {{ $slot }}
</button>
