<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Support\CartManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // Routes already wrap this controller in the 'auth' middleware.

    public function show(CartManager $cart)
    {
        $rows = $cart->detailed();
        $total = $cart->total();

        if (empty($rows)) {
            return redirect()
                ->route('products.index')
                ->with('status', 'Your cart is empty.');
        }

        return view('checkout.show', compact('rows', 'total'));
    }

    public function store(Request $request, CartManager $cart)
    {
        $rows = $cart->detailed();
        if (empty($rows)) {
            return redirect()
                ->route('products.index')
                ->with('status', 'Your cart is empty.');
        }

        // 1) Validate billing + payment
        $data = $request->validate([
            'billing_first_name' => ['required', 'string', 'max:100'],
            'billing_last_name' => ['required', 'string', 'max:100'],
            'billing_email' => ['required', 'email', 'max:255'],
            'billing_phone' => ['required', 'string', 'max:30'],
            'billing_address' => ['required', 'string', 'max:255'],
            'billing_city' => ['required', 'string', 'max:120'],
            'billing_zip' => ['required', 'string', 'max:20'],

            'payment_method' => ['required', 'in:card,cod'],

            // Card-only fields
            'card_name' => ['required_if:payment_method,card', 'nullable', 'string', 'max:255'],
            'card_number' => ['required_if:payment_method,card', 'nullable', 'digits_between:12,19'],
            'exp_month' => ['required_if:payment_method,card', 'nullable', 'integer', 'between:1,12'],
            'exp_year' => ['required_if:payment_method,card', 'nullable', 'integer', 'min:' . date('Y'), 'max:' . (date('Y') + 10)],
            'cvv' => ['required_if:payment_method,card', 'nullable', 'digits_between:3,4'],
        ]);

        $user = $request->user();

        // 2) Subscription rules: block if same as current; allow if different (will replace after order)
        $subscriptionIdsInCart = collect($rows)
            ->filter(fn($r) => (bool) ($r['product']->is_subscription ?? false))
            ->map(fn($r) => (int) $r['product']->id)
            ->values();

        // Safety: CartManager enforces at most one sub, but guard anyway.
        if ($subscriptionIdsInCart->count() > 1) {
            return back()
                ->withErrors(['subscription' => 'Only one subscription can be purchased at a time.'])
                ->withInput();
        }

        $newSubscriptionId = $subscriptionIdsInCart->first(); // may be null

        if ($newSubscriptionId && (int) $user->subscription_id === (int) $newSubscriptionId) {
            return back()
                ->withErrors(['subscription' => 'You already have this subscription.'])
                ->withInput();
        }

        // 3) Create order + items atomically
        DB::transaction(function () use ($rows, $cart, $request, $data, $user, $newSubscriptionId) {
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0,
                'status' => 'paid', // demo
                'billing_first_name' => $data['billing_first_name'],
                'billing_last_name' => $data['billing_last_name'],
                'billing_email' => $data['billing_email'],
                'billing_phone' => $data['billing_phone'],
                'billing_address' => $data['billing_address'],
                'billing_city' => $data['billing_city'],
                'billing_zip' => $data['billing_zip'],
                'payment_method' => $data['payment_method'],
                'card_last4' => ($data['payment_method'] === 'card' && !empty($data['card_number']))
                    ? substr($data['card_number'], -4)
                    : null,
            ]);

            $total = 0;
            foreach ($rows as $r) {
                $total += $r['subtotal'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $r['product']->id,
                    'price' => $r['product']->price, // snapshot price
                    'quantity' => $r['qty'],
                ]);
            }

            $order->update(['total_amount' => $total]);

            // 4) If a subscription was purchased, replace user's active subscription
            if ($newSubscriptionId) {
                $user->forceFill(['subscription_id' => $newSubscriptionId])->save();
            }

            // 5) Clear cart
            $cart->clear();
        });

        return redirect()
            ->route('orders.history')
            ->with('status', 'Order placed successfully!');
    }



    //api mongo


    // helper: create/find user's cart
    protected function apiUserCart($user)
    {
        return \App\Models\Mongo\Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['items' => [], 'updated_at' => now()]
        );
    }

    // GET /api/cart
    public function apiCartShow(\Illuminate\Http\Request $req)
    {
        $cart = $this->apiUserCart($req->user());

        $items = collect($cart->items)->map(function ($i) {
            $p = \App\Models\Mongo\Product::where('mysql_id', (int) $i['product_id'])->first();
            $price = (float) ($p?->price ?? 0);
            $qty = (int) ($i['quantity'] ?? 0);
            return [
                'product_id' => (int) $i['product_id'],
                'name' => $p?->name,
                'price' => $price,
                'quantity' => $qty,
                'image_url' => $p?->image_url,
                'line_total' => round($price * $qty, 2)
            ];
        });

        return response()->json([
            'user_id' => $cart->user_id,
            'items' => $items,
            'total' => round($items->sum('line_total'), 2),
            'updated_at' => $cart->updated_at?->toISOString()
        ]);
    }

    // POST /api/cart/items  {product_id, quantity}
    public function apiCartAdd(\Illuminate\Http\Request $req)
    {
        $data = $req->validate(['product_id' => 'required|integer', 'quantity' => 'required|integer|min:1']);
        abort_unless(\App\Models\Mongo\Product::where('mysql_id', (int) $data['product_id'])->exists(), 404);

        $cart = $this->apiUserCart($req->user());
        $items = collect($cart->items);
        $found = $items->firstWhere('product_id', (int) $data['product_id']);

        $items = $found
            ? $items->map(fn($i) => $i['product_id'] == (int) $data['product_id']
                ? ['product_id' => $i['product_id'], 'quantity' => (int) $i['quantity'] + (int) $data['quantity']]
                : $i)
            : $items->push(['product_id' => (int) $data['product_id'], 'quantity' => (int) $data['quantity']]);

        $cart->items = $items->values()->all();
        $cart->updated_at = now();
        $cart->save();

        return $this->apiCartShow($req);
    }

    // PATCH /api/cart/items  {product_id, quantity}
    public function apiCartUpdate(\Illuminate\Http\Request $req)
    {
        $data = $req->validate(['product_id' => 'required|integer', 'quantity' => 'required|integer|min:1']);
        $cart = $this->apiUserCart($req->user());

        $cart->items = collect($cart->items)->map(fn($i) => $i['product_id'] == (int) $data['product_id']
            ? ['product_id' => $i['product_id'], 'quantity' => (int) $data['quantity']]
            : $i)->values()->all();

        $cart->updated_at = now();
        $cart->save();

        return $this->apiCartShow($req);
    }

    // DELETE /api/cart/items  {product_id}
    public function apiCartRemove(\Illuminate\Http\Request $req)
    {
        $data = $req->validate(['product_id' => 'required|integer']);
        $cart = $this->apiUserCart($req->user());

        $cart->items = collect($cart->items)
            ->reject(fn($i) => $i['product_id'] == (int) $data['product_id'])
            ->values()->all();

        $cart->updated_at = now();
        $cart->save();

        return response()->json(['message' => 'Removed']);
    }

    // GET /api/orders
    public function apiOrders(\Illuminate\Http\Request $req)
    {
        return \App\Models\Mongo\Order::where('user_id', $req->user()->id)
            ->orderBy('ordered_at', 'desc')->paginate(10);
    }

    // GET /api/orders/{id}
    public function apiOrderShow(\Illuminate\Http\Request $req, $id)
    {
        $order = \App\Models\Mongo\Order::find($id);
        if (!$order || $order->user_id !== $req->user()->id) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return $order;
    }

    // POST /api/orders/checkout
    public function apiCheckout(\Illuminate\Http\Request $req)
    {
        $user = $req->user();
        $cart = \App\Models\Mongo\Cart::where('user_id', $user->id)->first();
        if (!$cart || empty($cart->items)) {
            return response()->json(['message' => 'Cart empty'], 422);
        }

        $items = collect($cart->items)->map(function ($i) {
            $p = \App\Models\Mongo\Product::where('mysql_id', (int) $i['product_id'])->first();
            $price = (float) ($p?->price ?? 0);
            $qty = (int) ($i['quantity'] ?? 0);
            return [
                'product_id' => (int) $i['product_id'],
                'name' => $p?->name,
                'price' => $price,
                'quantity' => $qty,
                'line_total' => round($price * $qty, 2)
            ];
        })->values()->all();

        $total = round(collect($items)->sum('line_total'), 2);

        $order = \App\Models\Mongo\Order::create([
            'user_id' => $user->id,
            'status' => 'paid',          // demo
            'total_amount' => $total,
            'ordered_at' => now(),
            'items' => $items,
            'created_at' => now(),
        ]);

        // clear cart
        $cart->items = [];
        $cart->updated_at = now();
        $cart->save();

        return response()->json($order, 201);
    }
}
