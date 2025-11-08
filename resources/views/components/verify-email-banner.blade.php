<div class="bg-yellow-100 border border-yellow-300 text-yellow-800 p-3 rounded text-center">
    Your email is not verified yet.
    <form method="POST" action="{{ route('verification.send') }}" class="inline">
        @csrf
        <button type="submit" class="underline text-blue-600">Be verified</button>
    </form>
</div>