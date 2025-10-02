<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
   
    #[Locked]
    public string $token = '';

    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount with token and (optional) email from query string.
     */
    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = (string) request()->string('email'); // prefill if provided
    }

    /**
     * Handle password reset.
     */
    public function resetPassword(): void
    {
        // Match your previous rules: email required, password >= 8 and same as confirmation
        $this->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'same:password_confirmation'],
            'password_confirmation' => ['required', 'string', 'min:8'],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            $this->addError('email', __($status));
            return;
        }

        Session::flash('status', __($status)); // “Your password has been reset!”
        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div class="bg-[#FBF9E4] p-10 rounded-2xl w-full shadow-xl text-[#122C4F] max-w-lg mx-auto">
    <h2 class="text-center text-3xl font-semibold mb-6">Reset Password</h2>

    @if (session('status'))
        <div class="mb-4 text-sm text-green-700 bg-green-100 px-3 py-2 rounded">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 text-sm text-red-600">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="resetPassword" class="space-y-4">
        <input type="email" wire:model.defer="email" placeholder="Email" required autocomplete="username"
            class="w-full bg-[#EBEBE0] text-[#122C4F] p-2 rounded focus:outline-none" />

        <input type="password" wire:model.defer="password" placeholder="New Password (min 8 chars)" required
            autocomplete="new-password" class="w-full bg-[#EBEBE0] text-[#122C4F] p-2 rounded focus:outline-none" />

        <input type="password" wire:model.defer="password_confirmation" placeholder="Confirm Password" required
            autocomplete="new-password" class="w-full bg-[#EBEBE0] text-[#122C4F] p-2 rounded focus:outline-none" />

        <div class="flex justify-center">
            <button type="submit"
                class="bg-[#122C4F] text-[#FBF9E4] px-10 p-2 rounded hover:shadow-xl hover:-translate-y-0.5 transition">
                Reset
            </button>
        </div>
    </form>

    <div class="text-center mt-4">
        <a href="{{ route('login') }}" class="underline">Back to Login</a>
    </div>
</div>