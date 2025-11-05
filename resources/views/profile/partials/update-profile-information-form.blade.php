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

        <!-- Profile Picture Selection -->
        <div>
            <x-input-label for="profile_picture" :value="__('Profile Picture')" />

            <!-- Current Profile Picture Display -->
            <div class="mt-2 mb-4">
                @if ($user->userInfo && $user->userInfo->profile_picture)
                    <img src="{{ asset('assets/' . $user->userInfo->profile_picture) }}" alt="Current Profile Picture"
                        class="w-20 h-20 rounded-full object-cover border-2 border-gray-300">
                @else
                    <div
                        class="w-20 h-20 rounded-full bg-green-200 flex items-center justify-center text-2xl font-semibold text-green-800">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- Profile Picture Options -->
            <div class="grid grid-cols-6 gap-4" x-data="{ selected: '{{ $user->userInfo->profile_picture ?? '' }}' }">
                @for ($i = 1; $i <= 6; $i++)
                    <label class="cursor-pointer">
                        <input type="radio" name="profile_picture" value="pfp{{ $i }}.jpg"
                            x-model="selected" class="sr-only peer"
                            {{ $user->userInfo && $user->userInfo->profile_picture === "pfp{$i}.jpg" ? 'checked' : '' }}>
                        <div
                            class="w-16 h-16 rounded-full overflow-hidden border-4 border-transparent peer-checked:border-[#FF9013] transition-all hover:scale-110">
                            <img src="{{ asset('/assets/pfp' . $i . '.jpg') }}"
                                alt="Profile Picture {{ $i }}" class="w-full h-full object-cover">
                        </div>
                    </label>
                @endfor

                <!-- Option to remove profile picture -->
                <label class="cursor-pointer">
                    <input type="radio" name="profile_picture" value="" x-model="selected" class="sr-only peer"
                        {{ !$user->userInfo || !$user->userInfo->profile_picture ? 'checked' : '' }}>
                    <div
                        class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center border-4 border-transparent peer-checked:border-[#FF9013] transition-all hover:scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </label>
            </div>
            <p class="mt-2 text-xs text-gray-500">Select a profile picture or choose the X to use your initial.</p>
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
