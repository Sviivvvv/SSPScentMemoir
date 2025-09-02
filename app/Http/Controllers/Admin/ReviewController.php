<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::orderBy('sort_order')->orderByDesc('id')->get();
        return view('admin.reviews.index', compact('reviews'));
    }

    public function create(): View
    {
        return view('admin.reviews.create');
    }

    public function store(StoreReviewRequest $request): RedirectResponse
    {
        $path = $request->hasFile('image')
            ? $request->file('image')->store('reviews', 'public')
            : null;

        Review::create([
            'author_name' => $request->string('author_name'),
            'rating' => $request->integer('rating'),
            'quote' => $request->input('quote'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->integer('sort_order') ?? 0,
            'image_path' => $path,
        ]);

        return redirect()->route('admin.reviews.index')->with('success', 'Review created.');
    }

    public function edit(Review $review): View
    {
        return view('admin.reviews.edit', compact('review'));
    }

    public function update(UpdateReviewRequest $request, Review $review): RedirectResponse
    {
        $data = [
            'author_name' => $request->string('author_name'),
            'rating' => $request->integer('rating'),
            'quote' => $request->input('quote'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->integer('sort_order') ?? $review->sort_order,
        ];

        if ($request->hasFile('image')) {
            if ($review->image_path)
                Storage::disk('public')->delete($review->image_path);
            $data['image_path'] = $request->file('image')->store('reviews', 'public');
        }

        $review->update($data);

        return redirect()->route('admin.reviews.index')->with('success', 'Review updated.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        if ($review->image_path)
            Storage::disk('public')->delete($review->image_path);
        $review->delete();
        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted.');
    }

    public function toggle(Review $review): RedirectResponse
    {
        $review->is_active = !$review->is_active;
        $review->save();
        return back()->with('success', 'Review status updated.');
    }

    public function moveUp(Review $review): RedirectResponse
    {
        $prev = Review::where('sort_order', '<', $review->sort_order)->orderBy('sort_order', 'desc')->first();
        if ($prev) {
            [$review->sort_order, $prev->sort_order] = [$prev->sort_order, $review->sort_order];
            $review->save();
            $prev->save();
        }
        return back();
    }

    public function moveDown(Review $review): RedirectResponse
    {
        $next = Review::where('sort_order', '>', $review->sort_order)->orderBy('sort_order', 'asc')->first();
        if ($next) {
            [$review->sort_order, $next->sort_order] = [$next->sort_order, $review->sort_order];
            $review->save();
            $next->save();
        }
        return back();
    }
}
