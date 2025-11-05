<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} | Admin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 2px; }
    </style>
</head>

<body class="bg-gray-100 font-sans text-gray-900 antialiased">
    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

        <aside class="flex flex-col bg-gray-800 text-gray-300 shadow-xl overflow-y-auto sidebar-scroll transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'w-64' : 'w-20'">
            <div class="h-16 flex items-center justify-center p-4 bg-gray-900 border-b border-gray-700">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-extrabold text-white tracking-wider overflow-hidden whitespace-nowrap"
                    :class="sidebarOpen ? 'w-full' : 'w-0'">
                    Yusisay.
                </a>
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-extrabold text-white tracking-wider"
                    :class="sidebarOpen ? 'hidden' : 'block'">
                    Y
                </a>
            </div>

            <nav class="flex-grow p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 text-sm font-medium rounded-lg transition-colors duration-200
                    @if(request()->routeIs('admin.dashboard')) bg-indigo-700 text-white shadow-lg @else hover:bg-gray-700/50 hover:text-white @endif">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mr-0'"></i>
                    <span :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Dashboard</span>
                </a>

                <a href="{{ route('admin.user') }}" class="flex items-center p-3 text-sm font-medium rounded-lg transition-colors duration-200
                    @if(request()->routeIs('admin.user')) bg-indigo-700 text-white shadow-lg @else hover:bg-gray-700/50 hover:text-white @endif">
                    <i data-lucide="users" class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mr-0'"></i>
                    <span :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Accounts</span>
                </a>

                <a href="{{ route('admin.report') }}" class="flex items-center p-3 text-sm font-medium rounded-lg transition-colors duration-200
                    @if(request()->routeIs('admin.report')) bg-indigo-700 text-white shadow-lg @else hover:bg-gray-700/50 hover:text-white @endif">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mr-0'"></i>
                    <span :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Reports</span>
                </a>

                <a href="#" class="flex items-center p-3 text-sm font-medium rounded-lg transition-colors duration-200 hover:bg-gray-700/50 hover:text-white">
                    <i data-lucide="settings" class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mr-0'"></i>
                    <span :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Settings</span>
                </a>
            </nav>

            <div class="p-4 border-t border-gray-700">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center p-3 text-sm font-medium text-gray-400 rounded-lg transition-colors duration-200 hover:bg-red-700 hover:text-white justify-start">
                        <i data-lucide="log-out" class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mr-0'"></i>
                        <span :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 shadow-sm">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-gray-500 hover:text-gray-700 rounded-full transition-colors mr-4">
                        <i data-lucide="arrow-left-to-line" class="w-5 h-5" x-show="sidebarOpen"></i>
                        <i data-lucide="arrow-right-to-line" class="w-5 h-5" x-show="!sidebarOpen"></i>
                    </button>

                    <div class="text-xl font-semibold text-gray-700">
                        {{ $header ?? 'Admin Panel' }}
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <button class="relative p-2 text-gray-500 hover:text-gray-700 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-500"></span>
                    </button>

                    <div class="flex items-center space-x-2 cursor-pointer">
                        <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name ?? 'Admin User' }}</span>
                        <img class="w-8 h-8 rounded-full object-cover border-2 border-indigo-400" src="https://placehold.co/150x150/5e66ff/ffffff?text=AD" alt="Profile">
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
