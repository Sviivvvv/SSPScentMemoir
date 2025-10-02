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

        $product = Product::find($this->productId);
        if (!$product) {
            $this->addError('qty', 'This product no longer exists.');
            return;
        }

        // Let CartManager handle subscriptions vs. normal
        $cart->add($this->productId, $this->qty);

        // Update navbar badge
        $this->dispatch('cart-updated');

        // Feedback
        session()->flash('status', 'Added to cart');

        // Reset qty (non-subscriptions only)
        if (!$product->is_subscription) {
            $this->qty = 1;
        }
    }

    public function render()
    {
        return view('livewire.cart.add-to-cart');
    }
}
