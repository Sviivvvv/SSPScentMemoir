<?php

namespace App\Support;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartManager
{
    const SESSION_KEY = 'cart.items'; // [productId => qty]

    /** Add product (ignores subscription products) */
    public function add(int $productId, int $qty = 1): void
    {
        $product = Product::query()
            ->where('id', $productId)
            ->where(function ($q) {
                $q->whereNull('is_subscription')
                    ->orWhere('is_subscription', false);
            })
            ->first();

        if (!$product)
            return;

        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            $item = $cart->items()->firstOrNew(['product_id' => $productId]);
            $item->quantity = max(1, (int) $item->quantity + $qty);
            $item->save();
        } else {
            $items = session()->get(self::SESSION_KEY, []);
            $items[$productId] = max(1, (int) ($items[$productId] ?? 0) + $qty);
            session()->put(self::SESSION_KEY, $items);
        }
    }

    public function setQty(int $productId, int $qty): void
    {
        $qty = max(0, (int) $qty);
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            if ($qty === 0) {
                $cart->items()->where('product_id', $productId)->delete();
            } else {
                $item = $cart->items()->firstOrNew(['product_id' => $productId]);
                $item->quantity = $qty;
                $item->save();
            }
        } else {
            $items = session()->get(self::SESSION_KEY, []);
            if ($qty === 0)
                unset($items[$productId]);
            else
                $items[$productId] = $qty;
            session()->put(self::SESSION_KEY, $items);
        }
    }

    public function remove(int $productId): void
    {
        $this->setQty($productId, 0);
    }

    /** Clear cart completely */
    public function clear(): void
    {
        if (Auth::check()) {
            if ($cart = Cart::where('user_id', Auth::id())->first()) {
                $cart->items()->delete();
            }
        } else {
            session()->forget(self::SESSION_KEY);
        }
    }

    /** Sum of quantities */
    public function count(): int
    {
        if (Auth::check()) {
            return (int) Cart::firstOrCreate(['user_id' => Auth::id()])
                ->items()->sum('quantity');
        }
        return array_sum(session()->get(self::SESSION_KEY, []));
    }

    /** Detailed items for UI: [['product'=>Product, 'qty'=>int, 'subtotal'=>float], ...] */
    public function detailed(): array
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            $items = $cart->items()->with('product')->get();
            return $items->map(fn($i) => [
                'product' => $i->product,
                'qty' => (int) $i->quantity,
                'subtotal' => (float) ($i->product?->price ?? 0) * (int) $i->quantity,
            ])->filter(fn($row) => $row['product'])->values()->all();
        }

        $raw = session()->get(self::SESSION_KEY, []);
        if (!$raw)
            return [];
        $products = Product::whereIn('id', array_keys($raw))->get()->keyBy('id');
        $rows = [];
        foreach ($raw as $pid => $qty) {
            $p = $products[$pid] ?? null;
            if (!$p)
                continue;
            $rows[] = ['product' => $p, 'qty' => (int) $qty, 'subtotal' => (float) $p->price * (int) $qty];
        }
        return $rows;
    }

    public function total(): float
    {
        return array_sum(array_map(fn($r) => $r['subtotal'], $this->detailed()));
    }

    /** On login: merge session cart into user's DB cart */
    public function mergeSessionToUser(int $userId): void
    {
        $raw = session()->pull(self::SESSION_KEY, []);
        if (!$raw)
            return;

        $cart = Cart::firstOrCreate(['user_id' => $userId]);
        foreach ($raw as $pid => $qty) {
            $item = $cart->items()->firstOrNew(['product_id' => $pid]);
            $item->quantity = max(1, (int) $item->quantity + (int) $qty);
            $item->save();
        }
    }
}
