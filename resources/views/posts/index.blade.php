<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home Feed') }}
        </h2>
    </x-slot>

    <a href="{{ route('posts.show', 1) }}">
    <div class="p-4 border rounded-lg shadow hover:bg-gray-50 transition">
        <h2 class="text-lg font-semibold">Test Post</h2>
        <p class="text-gray-700">Click me to test routing to Post #1</p>
    </div>
    </a>

</x-app-layout>
