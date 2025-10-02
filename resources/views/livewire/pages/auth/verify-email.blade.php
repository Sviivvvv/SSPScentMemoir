<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            return;
        }

        Auth::user()->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="bg-[#FBF9E4] p-10 rounded-2xl w-full max-w-lg shadow-xl text-[#122C4F] mx-auto">
    <h2 class="text-center text-3xl font-semibold mb-6">Verify Email</h2>

    <p class="mb-4">
        We've sent a verification link to your email. Didn't receive it?
    </p>

    @if (session('status') === 'verification-link-sent')
        <div class="mb-4 text-sm bg-green-100 text-green-800 px-3 py-2 rounded">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="flex gap-4 justify-center">
        <!-- Trigger Volt method (no page reload) -->
        <button wire:click="sendVerification"
            class="bg-[#122C4F] text-[#FBF9E4] px-6 py-2 rounded hover:shadow-xl hover:-translate-y-0.5 transition">
            Resend Email
        </button>

        <button wire:click="logout" type="button" class="underline">
            Log Out
        </button>
    </div>
</div>