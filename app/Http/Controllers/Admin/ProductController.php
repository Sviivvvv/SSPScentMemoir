<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index');
    }

    public function create(Request $request)
    {
        $scope = $request->query('scope', 'men');

        return view('admin.products.create', ['scope' => $scope]);
    }

    public function store(Request $request)
    {
        $scope = $request->input('scope', 'men');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image_path' => ['nullable', 'string', 'max:2048'],
        ];
        if ($scope !== 'subs') {
            $rules['category'] = ['required', Rule::in(['men', 'women', 'limited'])];
        }

        $data = $request->validate($rules);

        if ($scope === 'subs') {
            $data['is_subscription'] = true;
            $data['category'] = null;
        } else {
            $data['is_subscription'] = false;
            $data['category'] = $data['category'] ?? $scope;
        }

        Product::create($data);

        return to_route('admin.products.index')->with('status', 'Product created.');
    }

    public function edit(Product $product)
    {
        $scope = $product->is_subscription ? 'subs' : ($product->category ?? 'men');
        return view('admin.products.edit', compact('product', 'scope'));
    }

    public function update(Request $request, Product $product)
    {
        $isSub = (bool) $product->is_subscription;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image_path' => ['nullable', 'string', 'max:2048'],
        ];
        if (!$isSub) {
            $rules['category'] = ['required', Rule::in(['men', 'women', 'limited'])];
        }

        $data = $request->validate($rules);
        if ($isSub) {
            $data['is_subscription'] = true;
            $data['category'] = null;
        } else {
            $data['is_subscription'] = false;
        }

        $product->update($data);

        return to_route('admin.products.index')->with('status', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return to_route('admin.products.index')->with('status', 'Product deleted.');
    }
}
