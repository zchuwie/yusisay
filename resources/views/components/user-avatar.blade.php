@props(['user', 'isAnonymous' => false, 'size' => 'w-8 h-8'])

@if($isAnonymous) 
    <div class="{{ $size }} rounded-full bg-gray-400 flex items-center justify-center text-sm font-semibold text-white">
        A
    </div>
@elseif($user->userInfo && $user->userInfo->profile_picture) 
    <img src="{{ asset('assets/' . $user->userInfo->profile_picture) }}" 
         alt="{{ $user->name }}" 
         class="{{ $size }} rounded-full object-cover">
@else 
    <div class="{{ $size }} rounded-full bg-green-200 flex items-center justify-center text-sm font-semibold text-green-800">
        {{ strtoupper(substr($user->name, 0, 1)) }}
    </div>
@endif