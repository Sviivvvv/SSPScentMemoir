<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('key')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $keys = [
            'products.limited.desktop',
            'products.limited.mobile',
            'products.men.desktop',
            'products.men.mobile',
            'products.women.desktop',
            'products.women.mobile',
        ];
        return view('admin.banners.create', compact('keys'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string|max:255|unique:banners,key',
            'image' => 'required|image|max:2048',
            'alt' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $path = $request->file('image')->store('banners', 'public');
        Banner::create([
            'key' => $data['key'],
            'image_path' => $path,
            'alt' => $data['alt'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.banners.index')->with('ok', 'Banner created');
    }

    public function edit(Banner $banner)
    {
        $keys = [
            'products.limited.desktop',
            'products.limited.mobile',
            'products.men.desktop',
            'products.men.mobile',
            'products.women.desktop',
            'products.women.mobile',
        ];
        return view('admin.banners.edit', compact('banner', 'keys'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'key' => 'required|string|max:255|unique:banners,key,' . $banner->id,
            'image' => 'nullable|image|max:2048',
            'alt' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $banner->image_path = $request->file('image')->store('banners', 'public');
        }

        $banner->key = $data['key'];
        $banner->alt = $data['alt'] ?? null;
        $banner->is_active = $request->boolean('is_active', true);
        $banner->save();

        return redirect()->route('admin.banners.index')->with('ok', 'Banner updated');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return back()->with('ok', 'Banner deleted');
    }
}
