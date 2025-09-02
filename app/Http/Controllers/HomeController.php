<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ad;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        // Limited products
        $products = Product::limited()->take(6)->get();

        // Ads & reviews (only active ones)
        $ads = Ad::latest()->get();
        $reviews = Review::latest()->get();

        return view('home.index', compact('products', 'ads', 'reviews'));
    }
}
