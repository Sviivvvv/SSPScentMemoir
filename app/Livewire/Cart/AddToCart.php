<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Support\CartManager;
use App\Models\Product;

class AddToCart extends Component
{
    public int $productId;

    #[Validate('integer|min:1|max:99')]
    public int $qty = 1;

    public function mount(int $productId): void
    {
        $this->productId = $productId;
    }

    public function add(CartManager $cart): void
    {
        $this->validate();

        // Guard: don't allow deleted/subscription products
        $ok = Product::query()
            ->whereKey($this->productId)
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('is_subscription')
                    ->orWhere('is_subscription', false);
            })
            ->exists();

        if (!$ok) {
            $this->addError('qty', 'Sorry, this product cannot be added to the cart.');
            return;
        }

        $cart->add($this->productId, $this->qty);

        // Update navbar badge
        $this->dispatch('cart-updated');

        // Optional flash (your Blade already shows session('status'))
        session()->flash('status', 'Added to cart');

        // Reset qty
        $this->qty = 1;
    }

    public function render()
    {
        return view('livewire.cart.add-to-cart');
    }
}
