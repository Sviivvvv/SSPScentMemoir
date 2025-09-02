<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class ForgotPassword extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', __($status)); // “We have emailed your password reset link!”
        } else {
            $this->addError('email', __($status));   // e.g. user not found
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
