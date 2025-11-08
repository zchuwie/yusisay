<x-app-layout>
    <div class="max-w-2xl mx-auto px-4">

        @if (Auth::check() && Auth::user()->hasVerifiedEmail())
            <x-post-form />
            <x-post-feed :posts="$posts" />
            <x-go-to-top />
        @else
            <x-verify-email-card />
        @endif

    </div>

    @if (Auth::check() && Auth::user()->hasVerifiedEmail())
        <div x-data="{ open: false }">
            <div class="fixed bottom-6 right-6 z-50">
                <x-add-post-button @click="open = true" />
            </div>
            <x-add-post-modal />
        </div>
    @endif

    <x-toast-notification />
</x-app-layout>