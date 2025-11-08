<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf
 
        <div>
            <x-input-label for="name" :value="__('Name')" class="text-sm font-semibold text-gray-700" />
            <x-text-input id="name"
                class="block mt-2 w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                type="text" name="name" :value="old('name')" placeholder="Enter your name" required autofocus
                autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
 
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="text-sm font-semibold text-gray-700" />
            <x-text-input id="email"
                class="block mt-2 w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                type="email" name="email" :value="old('email')" placeholder="Enter your email" required
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
 
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-gray-700" />
            <x-text-input id="password"
                class="block mt-2 w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                type="password" name="password" placeholder="Enter your password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
 
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-sm font-semibold text-gray-700" />
            <x-text-input id="password_confirmation"
                class="block mt-2 w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                type="password" name="password_confirmation" placeholder="Confirm your password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-100">
            <p class="text-sm text-gray-600">
                Already registered?
                <a href="{{ route('login') }}"
                    class="font-semibold text-orange-500 hover:text-orange-600 transition-colors">
                    Click here.
                </a>
            </p>

            <button type="submit"
                class="px-6 py-3 bg-[#FF9013] text-white font-semibold rounded-xl shadow hover:bg-[#e68010] focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all">
                {{ __('Register') }}
            </button>
        </div>
    </form>
</x-guest-layout>
