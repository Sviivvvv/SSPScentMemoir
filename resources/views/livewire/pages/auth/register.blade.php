<?php
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // If your User model has casts ['password' => 'hashed'], you can omit Hash::make.
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
        ]);

        session()->flash('status', 'Account created successfully. Please log in.');
        $this->redirect(route('login', absolute: false), navigate: true);
    }
};
?>

<div class="bg-[#FBF9E4] p-10 rounded-2xl w-full max-w-md mx-auto shadow-xl text-[#122C4F]">
    <h2 class="text-center text-3xl font-semibold mb-6">Create Account</h2>

    @if (session('status'))
        <div class="mb-4 text-sm text-green-700">{{ session('status') }}</div>
    @endif

    <form wire:submit="register" class="space-y-4">
        <input type="text" wire:model="name" placeholder="Full Name" required
            class="w-full bg-[#EBEBE0] text-[#122C4F] p-2 rounded focus:outline-none">
        <x-input-error :messages="$errors->get('name')" class="text-red-600 text-sm" />

        <input type="email" wire:model="email" placeholder="Email" required autocomplete="username"
            class="w-full bg-[#EBEBE0] text-[#122C4F] p-2 rounded focus:outline-none">
        <x-input-error :messages="$errors->get('email')" class="text-red-600 text-sm" />

        <input type="password" wire:model="password" placeholder="Password" required autocomplete="new-password"
            class="w-full bg-[#EBEBE0] text-[#122C4F] p-2 rounded focus:outline-none">
        <x-input-error :messages="$errors->get('password')" class="text-red-600 text-sm" />

        <input type="password" wire:model="password_confirmation" placeholder="Confirm Password" required
            class="w-full bg-[#EBEBE0] text-[#122C4F] p-2 rounded focus:outline-none">
        <x-input-error :messages="$errors->get('password_confirmation')" class="text-red-600 text-sm" />

        <div class="flex justify-center">
            <button type="submit"
                class="bg-[#122C4F] text-[#FBF9E4] px-10 p-2 rounded hover:shadow-xl hover:-translate-y-0.5 transition">
                Sign up
            </button>
        </div>

        <p class="text-center mt-4">Already have an account?</p>
        <div class="flex justify-center">
            <a href="{{ route('login') }}" class="underline hover:underline-offset-2 transition" wire:navigate>
                Login
            </a>
        </div>
    </form>
</div>