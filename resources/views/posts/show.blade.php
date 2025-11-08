<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Post #' . $post->id) }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <x-page-styles />
</head>

<body class="bg-[#f3f4f6]">
    @if (Auth::check() && !Auth::user()->hasVerifiedEmail())
        <x-verify-email-banner />
        <x-post-nav :commentsCount="$post->comments->count()" />
        <div class="relative group">
            <x-post-display :post="$post" />
        </div>
        <x-verify-card />
    @else
        <x-toast-notification />
        <x-post-nav :commentsCount="$post->comments->count()" />

        <div class="relative group">
            <x-post-display :post="$post" />
            <div class="flex flex-col items-center justify-center px-4">
                <x-comments-section :post="$post" />
            </div>
        </div>
    @endif
</body>

</html>
