<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Support\CartManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // Middleware is already in routes; keeping controller clean.

    public function show(CartManager $cart)
    {
        $rows = $cart->detailed();
        $total = $cart->total();

        if (empty($rows)) {
            return redirect()->route('products.index')->with('status', 'Your cart is empty.');
        }

        return view('checkout.show', compact('rows', 'total'));
    }

    public function store(Request $request, CartManager $cart)
    {
        $rows = $cart->detailed();
        if (empty($rows)) {
            return redirect()->route('products.index')->with('status', 'Your cart is empty.');
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

            // Only required for card
            'card_name' => ['required_if:payment_method,card', 'nullable', 'string', 'max:255'],
            'card_number' => ['required_if:payment_method,card', 'nullable', 'digits_between:12,19'],
            'exp_month' => ['required_if:payment_method,card', 'nullable', 'integer', 'between:1,12'],
            'exp_year' => ['required_if:payment_method,card', 'nullable', 'integer', 'min:' . date('Y'), 'max:' . (date('Y') + 10)],
            'cvv' => ['required_if:payment_method,card', 'nullable', 'digits_between:3,4'],
        ]);

        // (This is a demo — no real payment integration.)

        // 2) Create order + items atomically
        DB::transaction(function () use ($rows, $cart, $request, $data) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_amount' => 0,
                'status' => 'paid', // or 'pending' if you prefer
                'billing_first_name' => $data['billing_first_name'],
                'billing_last_name' => $data['billing_last_name'],
                'billing_email' => $data['billing_email'],
                'billing_phone' => $data['billing_phone'],
                'billing_address' => $data['billing_address'],
                'billing_city' => $data['billing_city'],
                'billing_zip' => $data['billing_zip'],
                'payment_method' => $data['payment_method'],
                'card_last4' => ($data['payment_method'] === 'card' && !empty($data['card_number']))
                    ? substr($data['card_number'], -4) : null,
            ]);

            $total = 0;
            foreach ($rows as $r) {
                $total += $r['subtotal'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $r['product']->id,
                    'price' => $r['product']->price, // snapshot
                    'quantity' => $r['qty'],
                ]);
            }

            $order->update(['total_amount' => $total]);

            // 3) Clear cart after successful order
            $cart->clear();
        });

        return redirect()->route('orders.history')->with('status', 'Order placed successfully!');
    }
}
