<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Session;

new #[Layout('components.layouts.auth')] class extends Component {
    public \App\Livewire\Forms\LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();

        $user = auth()->user();
        $default = (($user->role ?? 'customer') === 'admin')
            ? route('admin.home', absolute: false)
            : route('home', absolute: false);

        $this->redirectIntended(default: $default, navigate: true);
    }
};
?>

   
    <div class="px-6 pt-15 "> 
        <div class="max-w-7xl mx-auto">
            <div class="bg-[#FBF9E4] p-10 rounded-2xl w-full max-w-md mx-auto shadow-xl text-[#122C4F]">
                <h2 class="text-center text-3xl font-semibold mb-6">Login</h2>

                @if (session('status'))
                    <div class="mb-4 text-sm text-green-700">{{ session('status') }}</div>
                @endif

                <form wire:submit="login" class="space-y-4">
                    <input type="email" wire:model="form.email" placeholder="Email" required autocomplete="username"
                        class="w-full bg-[#EBEBE0] text-[#122C4F] p-2 rounded focus:outline-none" />
                    <x-input-error :messages="$errors->get('form.email')" class="text-red-600 text-sm" />

                    <input type="password" wire:model="form.password" placeholder="Password" required
                        autocomplete="current-password"
                        class="w-full bg-[#EBEBE0] text-[#122C4F] p-2 rounded focus:outline-none" />
                    <x-input-error :messages="$errors->get('form.password')" class="text-red-600 text-sm" />

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" wire:model="form.remember">
                            <span>Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a class="underline" href="{{ route('password.request') }}" wire:navigate>Forgot Password?</a>
                        @endif
                    </div>

                    <div class="flex justify-center">
                        <button type="submit"
                            class="bg-[#122C4F] text-[#FBF9E4] px-10 p-2 rounded hover:shadow-xl hover:-translate-y-0.5 transition">
                            Login
                        </button>
                    </div>

                    <p class="text-center mt-4">Don't Have An Account?</p>
                    <div class="flex justify-center">
                        <a href="{{ route('register') }}" class="underline hover:underline-offset-2 transition"
                            wire:navigate>
                            Sign-up
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
