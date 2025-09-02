<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Register')]
#[Layout('components.layouts.auth')] // keeps your themed layout
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function register(): void
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password, // hashed by User::casts()
            'role' => 'customer',      // keep signups as customers
        ]);

        session()->flash('status', 'Account created successfully. Please log in.');
        $this->redirectRoute('login', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
