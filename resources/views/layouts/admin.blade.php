<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Admin') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans text-gray-900">

    {{-- Admin Navbar --}}
    <nav class="bg-gray-800 text-white p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold">Admin Panel</a>

            <div class="space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-300">Dashboard</a>
                <a href="{{ route('admin.report') }}" class="hover:text-gray-300">Reports</a>
                <a href="{{ route('admin.user') }}" class="hover:text-gray-300">Users</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="hover:text-gray-300">Logout</button>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto p-6">
        {{ $slot }}
    </main>

</body>
</html>
