<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\ReviewController;

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Http\Controllers\Admin\BannerController;
use App\Livewire\Shop\ProductSearch;
use Illuminate\Http\Request;

use App\Livewire\Cart\Page as CartPage;
use App\Http\Controllers\CheckoutController;



Route::get('/', function () {
    if (Auth::check() && (Auth::user()->role ?? 'customer') === 'admin') {
        return redirect()->route('admin.home');
    }
    return app(HomeController::class)->index();
})->name('home');

// Product details
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Route::get('/products/search', ProductSearch::class)->name('products.search');

Route::get('/products/search', ProductSearch::class)->name('products.search');
Route::get('/products/{product}', [ProductController::class, 'show'])
    ->whereNumber('product')
    ->name('products.show');



Route::get('/cart', CartPage::class)->name('cart.index');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // (Optional) Order history
    Route::get('/orders', function (Request $request) {
        $perPage = $request->integer('per_page', 8); // default 8 per page

        $orders = \App\Models\Order::with(['items.product'])
            ->where('user_id', auth()->id())
            ->latest('ordered_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    })->name('orders.history');
});


// Protected settings pages (example Livewire pages)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

// Admin area (role-gated)
Route::middleware(['auth', 'verified', 'can:admin-only'])
    ->prefix('admin')->name('admin.')->group(function () {

        // Admin home 
        Route::view('/', 'admin.index')->name('home');

        // Ads
        Route::resource('ads', AdController::class)->except(['show']);
        Route::post('ads/{ad}/toggle', [AdController::class, 'toggle'])->name('ads.toggle');
        Route::post('ads/{ad}/up', [AdController::class, 'moveUp'])->name('ads.up');
        Route::post('ads/{ad}/down', [AdController::class, 'moveDown'])->name('ads.down');

        // Reviews 
        Route::resource('reviews', ReviewController::class)->except(['show']);
        Route::post('reviews/{review}/toggle', [ReviewController::class, 'toggle'])->name('reviews.toggle');
        Route::post('reviews/{review}/up', [ReviewController::class, 'moveUp'])->name('reviews.up');
        Route::post('reviews/{review}/down', [ReviewController::class, 'moveDown'])->name('reviews.down');
        Route::resource('banners', BannerController::class)->except(['show']);
    });

// Any stray /dashboard hits: redirect by role
Route::get('/dashboard', function () {
    if (Auth::check() && (Auth::user()->role ?? 'customer') === 'admin') {
        return redirect()->route('admin.home');
    }
    return redirect()->route('home');
})->name('dashboard');

require __DIR__ . '/auth.php';
