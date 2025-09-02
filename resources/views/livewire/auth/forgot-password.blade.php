<div class="bg-[#FBF9E4] p-10 rounded-2xl w-full shadow-xl text-[#122C4F]">
    <h2 class="text-center text-3xl font-semibold mb-6">Forgot Password</h2>

    @if (session('status'))
        <div class="mb-4 text-sm text-green-700">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 text-sm text-red-600">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="sendPasswordResetLink" class="space-y-4">
        <input type="email" wire:model="email" placeholder="Email" required
            class="w-full bg-[#EBEBE0] text-[#122C4F] p-2 rounded focus:outline-none">

        <div class="flex justify-center">
            <button type="submit"
                class="bg-[#122C4F] text-[#FBF9E4] px-10 p-2 rounded hover:shadow-xl hover:-translate-y-0.5 transition">
                Email Reset Link
            </button>
        </div>
    </form>

    <div class="text-center mt-4">
        <a href="{{ route('login') }}" class="underline">Back to Login</a>
    </div>
</div>