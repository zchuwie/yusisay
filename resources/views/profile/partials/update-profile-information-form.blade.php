<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')
 
        <div class="space-y-4">
            <label class="block text-sm font-semibold text-gray-700">
                {{ __('Profile Picture') }}
            </label>
 
            <div
                class="flex items-center gap-5 p-5 bg-gradient-to-r from-orange-50 to-amber-50 rounded-2xl border-2 border-orange-100">
                @if ($user->userInfo && $user->userInfo->profile_picture)
                    <img src="{{ asset('assets/' . $user->userInfo->profile_picture) }}" alt="Current Profile Picture"
                        class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg ring-2 ring-[#FF9013]">
                @else
                    <div
                        class="w-24 h-24 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-3xl font-bold text-white border-4 border-white shadow-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif

                <div class="flex-1">
                    <p class="font-semibold text-gray-900 text-lg">{{ $user->name }}</p>
                    <p class="text-sm text-gray-600 mt-1">Choose your profile picture below</p>
                </div>
            </div>
 
            <div>
                <p class="text-[14px] font-bold text-gray-600 mb-3 tracking-wide">Available Pictures</p>

                <div class="grid grid-cols-7 gap-3" x-data="{ selected: '{{ $user->userInfo->profile_picture ?? '' }}' }">
                    @for ($i = 1; $i <= 6; $i++)
                        <label class="cursor-pointer group relative">
                            <input type="radio" name="profile_picture" value="pfp{{ $i }}.jpg"
                                x-model="selected" class="sr-only peer"
                                {{ $user->userInfo && $user->userInfo->profile_picture === "pfp{$i}.jpg" ? 'checked' : '' }}>

                            <div
                                class="relative w-full aspect-square rounded-full overflow-hidden border-4 border-gray-200 peer-checked:border-[#FF9013] peer-checked:shadow-lg peer-checked:scale-105 transition-all duration-300 hover:scale-105 hover:border-orange-300 group-hover:shadow-md">
                                <img src="{{ asset('/assets/pfp' . $i . '.jpg') }}"
                                    alt="Profile Picture {{ $i }}" class="w-full h-full object-cover">
 
                                <div
                                    class="absolute inset-0 bg-[#FF9013]/20 opacity-0 peer-checked:opacity-100 transition-opacity flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white drop-shadow-lg" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
 
                            <div
                                class="absolute -bottom-6 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-xs bg-gray-900 text-white px-2 py-1 rounded whitespace-nowrap">Option
                                    {{ $i }}</span>
                            </div>
                        </label>
                    @endfor
 
                    <label class="cursor-pointer group relative">
                        <input type="radio" name="profile_picture" value="" x-model="selected"
                            class="sr-only peer"
                            {{ !$user->userInfo || !$user->userInfo->profile_picture ? 'checked' : '' }}>

                        <div
                            class="w-full aspect-square rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center border-4 border-gray-200 peer-checked:border-[#FF9013] peer-checked:shadow-lg peer-checked:scale-105 transition-all duration-300 hover:scale-105 hover:border-orange-300 hover:from-gray-200 hover:to-gray-300 group-hover:shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-8 h-8 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
 
                        <div
                            class="absolute -bottom-6 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="text-xs bg-gray-900 text-white px-2 py-1 rounded whitespace-nowrap">Use
                                Initial</span>
                        </div>
                    </label>
                </div>
            </div>
 
            <div class="flex items-start gap-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-[14px] text-blue-800">
                    Select one of the available profile pictures or choose the <span class="font-semibold">X icon</span>
                    to display your name initial instead.
                </p>
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)"
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif

            @if ($user->hasVerifiedEmail())
                <div class="mt-2 font-medium text-sm text-green-600">
                    {{ __('Your email address is verified.') }}
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
