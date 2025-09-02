<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Support\CartManager;

#[Layout('components.layouts.site')] 
class Page extends Component
{
    public array $rows = [];
    public float $total = 0;

    public function mount(CartManager $cart): void
    {
        $this->refresh($cart);
    }

    public function refresh(CartManager $cart): void
    {
        $this->rows = $cart->detailed();
        $this->total = $cart->total();
    }

    public function increment(int $productId, CartManager $cart): void
    {
        $cart->add($productId, 1);
        $this->refresh($cart);
    }

    public function decrement(int $productId, CartManager $cart): void
    {
        $cart->add($productId, -1);
        $this->refresh($cart);
    }

    public function remove(int $productId, CartManager $cart): void
    {
        $cart->remove($productId);
        $this->refresh($cart);
    }

    public function clear(CartManager $cart): void
    {
        $cart->clear();
        $this->refresh($cart);
    }

    public function render()
    {
        return view('livewire.cart.page');
    }
}
