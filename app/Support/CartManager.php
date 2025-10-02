<?php

namespace App\Support;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartManager
{
    const SESSION_KEY = 'cart.items';

    /** Add product (subscriptions allowed; only one at a time, qty=1) */
    public function add(int $productId, int $qty = 1): void
    {
        $product = Product::findOrFail($productId);
        $isSub = (bool) $product->is_subscription;

        // subscriptions are always qty=1; non-sub cannot be <1
        $qty = $isSub ? 1 : max(1, (int) $qty);

        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            if ($isSub) {
                // remove any existing subscription item from this cart
                $cart->items()
                    ->whereHas('product', fn($q) => $q->where('is_subscription', 1))
                    ->delete();
            }

            $item = $cart->items()->firstOrNew(['product_id' => $productId]);
            $item->quantity = $isSub ? 1 : max(1, (int) $item->quantity + $qty);
            $item->save();
        } else {
            $items = session()->get(self::SESSION_KEY, []);

            if ($isSub) {
                // remove any existing subscription from the session cart
                if (!empty($items)) {
                    $subIds = Product::whereIn('id', array_keys($items))
                        ->where('is_subscription', 1)
                        ->pluck('id')
                        ->all();
                    foreach ($subIds as $sid) {
                        unset($items[$sid]);
                    }
                }
                $items[$productId] = 1;
            } else {
                $items[$productId] = max(1, (int) ($items[$productId] ?? 0) + $qty);
            }

            session()->put(self::SESSION_KEY, $items);
        }
    }

    /** Set absolute quantity (subscriptions forced to 1) */
    public function setQty(int $productId, int $qty): void
    {
        $product = Product::findOrFail($productId);
        $isSub = (bool) $product->is_subscription;

        $qty = $isSub ? min(1, max(0, (int) $qty)) : max(0, (int) $qty);

        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            if ($qty === 0) {
                $cart->items()->where('product_id', $productId)->delete();
                return;
            }

            if ($isSub) {
                // ensure no other subs remain, then set this one to qty=1
                $cart->items()
                    ->whereHas('product', fn($q) => $q->where('is_subscription', 1)
                        ->where('id', '!=', $productId))
                    ->delete();

                $cart->items()->updateOrCreate(
                    ['product_id' => $productId],
                    ['quantity' => 1]
                );
            } else {
                $cart->items()->updateOrCreate(
                    ['product_id' => $productId],
                    ['quantity' => $qty]
                );
            }
        } else {
            $items = session()->get(self::SESSION_KEY, []);

            if ($qty === 0) {
                unset($items[$productId]);
            } else {
                if ($isSub) {
                    // remove other subs and force qty=1
                    if (!empty($items)) {
                        $subIds = Product::whereIn('id', array_keys($items))
                            ->where('is_subscription', 1)
                            ->pluck('id')
                            ->all();
                        foreach ($subIds as $sid) {
                            if ($sid !== $productId)
                                unset($items[$sid]);
                        }
                    }
                    $items[$productId] = 1;
                } else {
                    $items[$productId] = $qty;
                }
            }

            session()->put(self::SESSION_KEY, $items);
        }
    }

    public function remove(int $productId): void
    {
        $this->setQty($productId, 0);
    }

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

    public function count(): int
    {
        if (Auth::check()) {
            return (int) Cart::firstOrCreate(['user_id' => Auth::id()])
                ->items()->sum('quantity');
        }
        return array_sum(session()->get(self::SESSION_KEY, []));
    }

    /** [['product'=>Product, 'qty'=>int, 'subtotal'=>float, 'is_sub'=>bool], ...] */
    public function detailed(): array
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            $items = $cart->items()->with('product')->get();

            return $items->map(fn($i) => [
                'product' => $i->product,
                'qty' => (int) $i->quantity,
                'subtotal' => (float) ($i->product?->price ?? 0) * (int) $i->quantity,
                'is_sub' => (bool) ($i->product?->is_subscription ?? false),
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
            $rows[] = [
                'product' => $p,
                'qty' => (int) $qty,
                'subtotal' => (float) $p->price * (int) $qty,
                'is_sub' => (bool) ($p->is_subscription ?? false),
            ];
        }
        return $rows;
    }

    public function total(): float
    {
        return array_sum(array_map(fn($r) => $r['subtotal'], $this->detailed()));
    }

    /** Merge session cart into user's cart (keep only one subscription; qty=1). */
    public function mergeSessionToUser(int $userId): void
    {
        $raw = session()->pull(self::SESSION_KEY, []);
        if (!$raw)
            return;

        // keep at most one subscription (last wins)
        $subIds = Product::whereIn('id', array_keys($raw))
            ->where('is_subscription', 1)
            ->pluck('id')->all();

        if (count($subIds) > 1) {
            $keep = end($subIds);
            foreach ($subIds as $sid) {
                if ($sid !== $keep)
                    unset($raw[$sid]);
            }
            $raw[$keep] = 1;
        } elseif (count($subIds) === 1) {
            $raw[$subIds[0]] = 1;
        }

        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        // ensure DB has max one subscription as well
        if (!empty($subIds)) {
            $keep = end($subIds);
            $cart->items()
                ->whereHas('product', fn($q) => $q->where('is_subscription', 1)
                    ->where('id', '!=', $keep))
                ->delete();
        }

        foreach ($raw as $pid => $qty) {
            $isSub = Product::where('id', $pid)->value('is_subscription') ? true : false;
            $item = $cart->items()->firstOrNew(['product_id' => $pid]);
            $item->quantity = $isSub ? 1 : max(1, (int) $item->quantity + (int) $qty);
            $item->save();
        }
    }
}
