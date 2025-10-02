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



    //mongo api

    // list products (search/sort/pagination)
    public function apiProducts(\Illuminate\Http\Request $req)
    {
        $page = max((int) $req->input('page', 1), 1);
        $size = min(max((int) $req->input('size', 12), 1), 50);

        $q = \App\Models\Mongo\Product::query()
            ->when($req->filled('search'), fn($qq) =>
                $qq->where(fn($w) => $w->where('name', 'like', '%' . $req->search . '%')
                    ->orWhere('category', 'like', '%' . $req->search . '%')))
            ->when($req->filled('category_id'), fn($qq) => $qq->where('category_id', (int) $req->category_id));

        $q = match ($req->input('sort')) {
            'price_asc' => $q->orderBy('price', 'asc'),
            'price_desc' => $q->orderBy('price', 'desc'),
            'new' => $q->orderBy('created_at', 'desc'),
            default => $q->orderBy('name', 'asc'),
        };

        $data = $q->skip(($page - 1) * $size)->take($size)->get();
        return response()->json(['data' => $data, 'page' => $page, 'size' => $size]);
    }

    // one product by SQL id mirrored in Mongo
    public function apiProduct($id)
    {
        $p = \App\Models\Mongo\Product::where('mysql_id', (int) $id)->first();
        return $p ? response()->json($p) : response()->json(['message' => 'Not found'], 404);
    }

    // list categories
    public function apiCategories()
    {
        return \App\Models\Mongo\Category::orderBy('name')->get();
    }

    // list products of one category
    public function apiCategoryProducts($categoryId, \Illuminate\Http\Request $req)
    {
        $page = max((int) $req->input('page', 1), 1);
        $size = min(max((int) $req->input('size', 12), 1), 50);

        $data = \App\Models\Mongo\Product::where('category_id', (int) $categoryId)
            ->orderBy('name')->skip(($page - 1) * $size)->take($size)->get();

        return response()->json(['data' => $data, 'page' => $page, 'size' => $size]);
    }
}
