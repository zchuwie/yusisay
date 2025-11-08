<button {{ $attributes->merge(['type' => 'submit', 'class' => 'flex items-center gap-2 text-white bg-[#FF9013] font-semibold rounded-xl text-sm px-6 py-3 shadow']) }}>
    {{ $slot }}
</button>
