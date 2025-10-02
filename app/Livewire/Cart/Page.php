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
        // compute current qty then set absolute qty
        $current = $this->currentQty($productId);
        $cart->setQty($productId, $current + 1);
        $this->refresh($cart);
    }

    public function decrement(int $productId, CartManager $cart): void
    {
        $current = $this->currentQty($productId);
        $new = max(0, $current - 1);

        // If it hits 0, remove the line
        if ($new === 0) {
            $cart->remove($productId);
        } else {
            $cart->setQty($productId, $new);
        }

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

    private function currentQty(int $productId): int
    {
        foreach ($this->rows as $row) {
            if ((int) ($row['product']->id ?? 0) === (int) $productId) {
                return (int) $row['qty'];
            }
        }
        return 0;
    }

    public function render()
    {
        return view('livewire.cart.page');
    }
}
