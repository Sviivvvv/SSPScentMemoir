<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Banner;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // /products — list page (sections + banners)
    public function index(Request $request)
    {
        // Each section has its own paginator & page parameter
        $limited = Product::limited()
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->paginate(12, ['*'], 'limited_page');

        $men = Product::query()
            ->where(function ($q) {
                $q->where('category', 'men')
                    ->orWhereHas('categoryRef', fn($c) => $c->where('name', 'men'));
            })
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->paginate(12, ['*'], 'men_page');

        $women = Product::query()
            ->where(function ($q) {
                $q->where('category', 'women')
                    ->orWhereHas('categoryRef', fn($c) => $c->where('name', 'women'));
            })
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->paginate(12, ['*'], 'women_page');

        // Safe banner lookups (won't crash if you haven't created any yet)
        $bannerUrl = function ($key) {
            return class_exists(\App\Models\Banner::class)
                ? \App\Models\Banner::urlFor($key)
                : null;
        };

        $banners = [
            'limited_desktop' => $bannerUrl('products.limited.desktop'),
            'limited_mobile' => $bannerUrl('products.limited.mobile'),
            'men_desktop' => $bannerUrl('products.men.desktop'),
            'men_mobile' => $bannerUrl('products.men.mobile'),
            'women_desktop' => $bannerUrl('products.women.desktop'),
            'women_mobile' => $bannerUrl('products.women.mobile'),
        ];

        return view('products.index', compact('limited', 'men', 'women', 'banners'));
    }

    // /products/{product} — detail page (unchanged)
    public function show(Product $product)
    {
        $recommendations = Product::query()
            ->when(
                $product->category_id,
                fn($q) => $q->where('category_id', $product->category_id),
                fn($q) => $q->where('category', $product->category)
            )
            ->where('id', '!=', $product->id)
            ->whereNull('deleted_at')
            ->take(8)
            ->get();

        return view('products.show', compact('product', 'recommendations'));
    }
}
