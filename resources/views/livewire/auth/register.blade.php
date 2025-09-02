<div class="bg-[#FBF9E4] p-10 rounded-2xl w-full shadow-xl text-[#122C4F]">
    <h2 class="text-center text-3xl font-semibold mb-6">Create Account</h2>

    @if ($errors->any())
        <div class="mb-4 text-sm text-red-600">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="register" class="space-y-4">
        <input type="text" wire:model="name" placeholder="Full Name" required
            class="w-full bg-[#EBEBE0] text-[#122C4F] p-2 rounded focus:outline-none">

        <input type="email" wire:model="email" placeholder="Email" required autocomplete="username"
            class="w-full bg-[#EBEBE0] text-[#122C4F] p-2 rounded focus:outline-none">

        <input type="password" wire:model="password" placeholder="Password" required autocomplete="new-password"
            class="w-full bg-[#EBEBE0] text-[#122C4F] p-2 rounded focus:outline-none">

        <input type="password" wire:model="password_confirmation" placeholder="Confirm Password" required
            class="w-full bg-[#EBEBE0] text-[#122C4F] p-2 rounded focus:outline-none">

        <div class="flex justify-center">
            <button type="submit"
                class="bg-[#122C4F] text-[#FBF9E4] px-10 p-2 rounded hover:shadow-xl hover:-translate-y-0.5 transition">
                Sign up
            </button>
        </div>

        <p class="text-center mt-4">Already have an account?</p>
        <div class="flex justify-center">
            <a href="{{ route('login') }}" class="underline hover:underline-offset-2 transition">Login</a>
        </div>
    </form>
</div>