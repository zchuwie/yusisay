<nav x-data="{ open: false }"
    class="bg-[#FAFAFA] border-b border-[#dddddd] fixed top-0 left-0 right-0 z-50 w-full shadow-sm">

    @if (Auth::check() && !Auth::user()->hasVerifiedEmail())
        <div class="bg-yellow-100 border-b border-yellow-300 text-yellow-800 py-2 text-center text-sm">
            Your email is not verified yet.
            <a href="{{ route('profile.edit') }}" class="underline text-blue-600 font-semibold hover:text-blue-800">
                Verify now
            </a>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-[80px]">

            <div class="shrink-0 flex items-center">
                <a href="{{ route('posts.index') }}" class="block">
                    <h1 class="text-3xl leading-[1.6] pt-2 pb-2 relative"
                        style="
          font-family: 'Pacifico', cursive;
          font-weight: 400;
          background: linear-gradient(135deg, #FF9013 0%, #FF6B6B 100%);
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
          background-clip: text;
          color: transparent;
          display: inline-block;
          transform: translateY(-4px);
        ">
                        Yusisay
                    </h1>
                </a>
            </div>
 
            <div class="flex justify-center gap-6 flex-1">

                <div class="relative py-3 px-6 cursor-pointer group transition-all rounded-xl duration-200
                    {{ request()->routeIs('posts.index')
                        ? 'bg-[#FF9013] text-white'
                        : 'hover:bg-gray-200 hover:text-gray-900 text-gray-400' }}"
                    x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false"
                    @click="$refs.link.click()">
                    <a href="{{ route('posts.index') }}" x-ref="link" class="flex justify-center">
                        <svg class="w-6 h-6 
                            {{ request()->routeIs('posts.index') ? 'text-white' : 'text-gray-400 group-hover:text-gray-700' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </a>

                    <div x-show="show" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="absolute left-1/2 top-[110%] -translate-x-1/2 bg-white border border-gray-200 
                        text-gray-600 text-sm rounded-lg shadow-md w-28 py-2 px-3 z-50 text-center">
                        <h3 class="font-semibold text-gray-900">Posts</h3>
                        <div
                            class="absolute top-[-5px] left-1/2 -translate-x-1/2 w-2 h-2 bg-white 
                            border-l border-t border-gray-200 rotate-45">
                        </div>
                    </div>
                </div>

                <div class="relative py-3 px-6 cursor-pointer group transition-all rounded-xl duration-200
                    {{ request()->routeIs('chat.index')
                        ? 'bg-[#FF9013] text-white'
                        : 'hover:bg-gray-200 hover:text-gray-900 text-gray-400' }}"
                    x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false"
                    @click="$refs.link.click()">
                    <a href="{{ route('chat.index') }}" x-ref="link" class="flex justify-center">
                        <svg class="w-6 h-6 
                            {{ request()->routeIs('chat.index') ? 'text-white' : 'text-gray-400 group-hover:text-gray-700' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </a>

                    <div x-show="show" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="absolute left-1/2 top-[110%] -translate-x-1/2 bg-white border border-gray-200 
                        text-gray-600 text-sm rounded-lg shadow-md w-28 py-2 px-3 z-50 text-center">
                        <h3 class="font-semibold text-gray-900">Chat</h3>
                        <div
                            class="absolute top-[-5px] left-1/2 -translate-x-1/2 w-2 h-2 bg-white 
                            border-l border-t border-gray-200 rotate-45">
                        </div>
                    </div>
                </div>

                <div class="relative py-3 px-6 cursor-pointer group transition-all rounded-xl duration-200
                    {{ request()->routeIs('posts.history')
                        ? 'bg-[#FF9013] text-white'
                        : 'hover:bg-gray-200 hover:text-gray-900 text-gray-400' }}"
                    x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false"
                    @click="$refs.link.click()">
                    <a href="{{ route('posts.history') }}" x-ref="link" class="flex justify-center">
                        <svg class="w-6 h-6 
                            {{ request()->routeIs('posts.history') ? 'text-white' : 'text-gray-400 group-hover:text-gray-700' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </a>

                    <div x-show="show" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="absolute left-1/2 top-[110%] -translate-x-1/2 bg-white border border-gray-200 
                        text-gray-600 text-sm rounded-lg shadow-md w-28 py-2 px-3 z-50 text-center">
                        <h3 class="font-semibold text-gray-900">History</h3>
                        <div
                            class="absolute top-[-5px] left-1/2 -translate-x-1/2 w-2 h-2 bg-white 
                            border-l border-t border-gray-200 rotate-45">
                        </div>
                    </div>
                </div>
            </div>
 
            <div class="flex items-center sm:ms-6">
                <div class="hidden sm:flex">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <div class="sm:hidden ms-2">
                    <button @click="open = ! open"
                        class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
 
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden bg-[#FAFAFA] border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('posts.index')" :active="request()->routeIs('posts.index')">Posts</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.index')">Chat</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('posts.history')" :active="request()->routeIs('posts.history')">History</x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
