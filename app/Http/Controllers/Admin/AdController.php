<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdRequest;
use App\Http\Requests\UpdateAdRequest;
use App\Models\Ad;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdController extends Controller
{
    public function index(): View
    {
        $ads = Ad::orderBy('sort_order')->orderByDesc('id')->get();
        return view('admin.ads.index', compact('ads'));
    }

    public function create(): View
    {
        return view('admin.ads.create');
    }

    public function store(StoreAdRequest $request): RedirectResponse
    {
        $path = $request->file('image')->store('ads', 'public');

        Ad::create([
            'title' => $request->string('title'),
            'link_url' => $request->input('link_url'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->integer('sort_order') ?? 0,
            'image_path' => $path,
        ]);

        return redirect()->route('admin.ads.index')->with('success', 'Ad created.');
    }

    public function edit(Ad $ad): View
    {
        return view('admin.ads.edit', compact('ad'));
    }

    public function update(UpdateAdRequest $request, Ad $ad): RedirectResponse
    {
        $data = [
            'title' => $request->string('title'),
            'link_url' => $request->input('link_url'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->integer('sort_order') ?? $ad->sort_order,
        ];

        if ($request->hasFile('image')) {
            // delete old
            if ($ad->image_path)
                Storage::disk('public')->delete($ad->image_path);
            $data['image_path'] = $request->file('image')->store('ads', 'public');
        }

        $ad->update($data);

        return redirect()->route('admin.ads.index')->with('success', 'Ad updated.');
    }

    public function destroy(Ad $ad): RedirectResponse
    {
        if ($ad->image_path)
            Storage::disk('public')->delete($ad->image_path);
        $ad->delete();
        return redirect()->route('admin.ads.index')->with('success', 'Ad deleted.');
    }

    public function toggle(Ad $ad): RedirectResponse
    {
        $ad->is_active = !$ad->is_active;
        $ad->save();
        return back()->with('success', 'Ad status updated.');
    }

    public function moveUp(Ad $ad): RedirectResponse
    {
        $prev = Ad::where('sort_order', '<', $ad->sort_order)->orderBy('sort_order', 'desc')->first();
        if ($prev) {
            [$ad->sort_order, $prev->sort_order] = [$prev->sort_order, $ad->sort_order];
            $ad->save();
            $prev->save();
        }
        return back();
    }

    public function moveDown(Ad $ad): RedirectResponse
    {
        $next = Ad::where('sort_order', '>', $ad->sort_order)->orderBy('sort_order', 'asc')->first();
        if ($next) {
            [$ad->sort_order, $next->sort_order] = [$next->sort_order, $ad->sort_order];
            $ad->save();
            $next->save();
        }
        return back();
    }
}
