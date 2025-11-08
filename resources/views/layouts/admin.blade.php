<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} | Admin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=1">

    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Prevent FOUC (Flash of Unstyled Content) */
        [x-cloak] {
            display: none !important;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Gradient background for sidebar */
        .sidebar-gradient {
            background: linear-gradient(180deg, #FF9013 0%, #e67e0f 100%);
        }

        /* Smooth transitions */
        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Improved hover states */
        .nav-item {
            position: relative;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: white;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .nav-item:hover::before {
            transform: translateX(0);
        }

        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.15);
            padding-left: 1rem;
        }

        .nav-item-active {
            background-color: rgba(255, 255, 255, 0.25);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.15), 0 2px 4px -1px rgba(0, 0, 0, 0.1);
        }

        .nav-item-active::before {
            transform: translateX(0);
        }

        /* Header shadow on scroll */
        .header-shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
    </style>

    <script>
        // Initialize sidebar state BEFORE Alpine loads to prevent flicker
        (function() {
            const sidebarState = localStorage.getItem('sidebarOpen');
            if (sidebarState === 'false') {
                document.documentElement.classList.add('sidebar-closed');
            }
        })();
    </script>
</head>

<body class="bg-gray-50 font-sans text-gray-900 antialiased">
    <div class="flex h-screen overflow-hidden" x-data="{
        sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebarOpen', this.sidebarOpen);
        }
    }" x-cloak>

        <!-- Sidebar -->
        <aside
            class="flex flex-col sidebar-gradient text-white shadow-2xl overflow-y-auto sidebar-scroll sidebar-transition z-30"
            :class="sidebarOpen ? 'w-64' : 'w-20'">

            <!-- Logo Section -->
            <div
                class="h-16 flex items-center justify-center p-4 bg-black bg-opacity-10 border-b border-white border-opacity-20">
                <a href="{{ route('admin.dashboard') }}" class="block">
                    <h1 class="text-xl leading-[1.6] pt-2 pb-2 relative sidebar-transition"
                        style="
          font-family: 'Pacifico', cursive;
          font-weight: 400;
          color: white;
          display: inline-block;
          transform: translateY(-4px);
        "
                        x-text="sidebarOpen ? 'Yusisay' : 'Y'">
                    </h1>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-grow p-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-item flex items-center p-3 text-sm font-medium rounded-lg sidebar-transition
                          @if (request()->routeIs('admin.dashboard')) nav-item-active @endif">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"
                        :class="sidebarOpen ? 'mr-3' : 'mr-0'"></i>
                    <span class="sidebar-transition"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 hidden'">Dashboard</span>
                </a>

                <a href="{{ route('admin.user') }}"
                    class="nav-item flex items-center p-3 text-sm font-medium rounded-lg sidebar-transition
                          @if (request()->routeIs('admin.user')) nav-item-active @endif">
                    <i data-lucide="users" class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mr-0'"></i>
                    <span class="sidebar-transition"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 hidden'">Accounts</span>
                </a>

                <a href="{{ route('admin.report') }}"
                    class="nav-item flex items-center p-3 text-sm font-medium rounded-lg sidebar-transition
                          @if (request()->routeIs('admin.report')) nav-item-active @endif">
                    <i data-lucide="shield-check" class="w-5 h-5 flex-shrink-0"
                        :class="sidebarOpen ? 'mr-3' : 'mr-0'"></i>
                    <span class="sidebar-transition"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 hidden'">Content Moderation</span>
                </a>
            </nav>

            <!-- Logout Section -->
            <div class="p-4 border-t border-white border-opacity-20">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center p-3 text-sm font-medium rounded-lg sidebar-transition hover:bg-red-600 hover:bg-opacity-90 justify-start group">
                        <i data-lucide="log-out" class="w-5 h-5 flex-shrink-0 group-hover:animate-pulse"
                            :class="sidebarOpen ? 'mr-3' : 'mr-0'"></i>
                        <span class="sidebar-transition"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 hidden'">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 header-shadow">
                <div class="flex items-center space-x-4">
                    <button @click="toggleSidebar()"
                        class="p-2 text-gray-500 hover:text-orange-500 hover:bg-orange-50 rounded-lg sidebar-transition">
                        <i data-lucide="panel-left" class="w-5 h-5" x-show="sidebarOpen"></i>
                        <i data-lucide="panel-right" class="w-5 h-5" x-show="!sidebarOpen"></i>
                    </button>

                    <div class="flex items-center space-x-2">
                        <div class="h-8 w-1 bg-orange-500 rounded-full"></div>
                        <h1 class="text-xl font-bold text-gray-800">
                            {{ $header ?? 'Admin Panel' }}
                        </h1>
                    </div>
                </div>

                <!-- Admin Badge -->
                <div class="flex items-center space-x-2 px-3 py-1.5 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-semibold text-orange-700">Admin</span>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>

</html>
