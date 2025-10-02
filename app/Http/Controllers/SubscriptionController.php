<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        // Show only subscription products 
        $plans = Product::query()
            ->where(function ($q) {
                $q->where('is_subscription', true)
                  ->orWhere('is_subscription', 1); // tolerate either bool or tinyint
            })
            ->orderBy('price')
            ->get();

        return view('subscriptions.index', compact('plans'));
    }
}
