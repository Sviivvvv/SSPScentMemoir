<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use App\Support\CartManager;

class Counter extends Component
{
    protected $listeners = ['cart-updated' => '$refresh'];

    public function render(CartManager $cart)
    {
        return view('livewire.cart.counter', ['count' => $cart->count()]);
    }
}
