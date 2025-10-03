<?php



use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\BannerController;

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Shop\ProductSearch;

use App\Livewire\Cart\Page as CartPage;
use App\Http\Controllers\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

Route::get('/', function () {
    if (Auth::check() && (Auth::user()->role ?? 'customer') === 'admin') {
        return redirect()->route('admin.home');
    }
    return app(HomeController::class)->index();
})->name('home');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search', ProductSearch::class)->name('products.search');
Route::get('/products/{product}', [ProductController::class, 'show'])
    ->whereNumber('product')
    ->name('products.show');

// Cart
Route::get('/cart', CartPage::class)->name('cart.index');


Route::get('/subscriptions', [SubscriptionController::class, 'index'])
    ->name('subscriptions.index');
// Checkout + Orders (auth only)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Orders (paginated)
    Route::get('/orders', function (Request $request) {
        $orders = \App\Models\Order::with('items.product')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    })->name('orders.history');
});

// Settings (auth + verified)
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

// Admin (auth + verified)
Route::middleware(['auth', 'can:admin-only'])
    ->prefix('admin')->name('admin.')->group(function () {
        Route::view('/', 'admin.index')->name('home');

        Route::resource('ads', AdController::class)->except(['show']);
        Route::post('ads/{ad}/toggle', [AdController::class, 'toggle'])->name('ads.toggle');
        Route::post('ads/{ad}/up', [AdController::class, 'moveUp'])->name('ads.up');
        Route::post('ads/{ad}/down', [AdController::class, 'moveDown'])->name('ads.down');

        Route::resource('reviews', ReviewController::class)->except(['show']);
        Route::post('reviews/{review}/toggle', [ReviewController::class, 'toggle'])->name('reviews.toggle');
        Route::post('reviews/{review}/up', [ReviewController::class, 'moveUp'])->name('reviews.up');
        Route::post('reviews/{review}/down', [ReviewController::class, 'moveDown'])->name('reviews.down');

        Route::resource('banners', BannerController::class)->except(['show']);
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)
            ->except(['show']);

        Route::resource('users', AdminUserController::class)->only([
            'index',
            'edit',
            'update',
            'destroy'
        ]);
        Route::view('orders', 'admin.orders.index')->name('orders.index');
    });

// Stray /dashboard  route by role 
Route::get('/dashboard', function () {
    if (Auth::check() && (Auth::user()->role ?? 'customer') === 'admin') {
        return redirect()->route('admin.home');
    }
    return redirect()->route('home');
})->name('dashboard');


/// vulnerability and safe sql injections
// if (app()->isLocal()) {
//     Route::get('/vuln-sql-1', function (Request $request) {
//         $email = $request->query('email');
//         $user = DB::select("SELECT * FROM users WHERE email = $email"); // VULN 
//         return response()->json($user ?: ['message' => 'No user found']);
//     });

//     Route::get('/safe-sql-1', function (Request $request) {
//         $data = $request->validate(['email' => 'required|email']);
//         $user = DB::table('users')->where('email', $data['email'])->first();
//         return response()->json($user ?: ['message' => 'No user found']);
//     });

//     Route::get('/vuln-sql-2', function (Request $request) {
//         $q = $request->query('q');
//         $rows = DB::table('products')->whereRaw("name LIKE '%$q%'")->get(); // VULN 
//         return response()->json($rows);
//     });

//     Route::get('/safe-sql-2', function (Request $request) {
//         $data = $request->validate(['q' => 'nullable|string|max:100']);
//         $rows = DB::table('products')->where('name', 'like', '%' . $data['q'] . '%')->get();
//         return response()->json($rows);
//     });

//     Route::get('/vuln-sql-3', function (Request $request) {
//         $id = $request->query('id');
//         $item = DB::select("SELECT * FROM orders WHERE id = " . $id); // VULN 
//         return response()->json($item ?: ['message' => 'No order']);
//     });

//     Route::get('/safe-sql-3', function (Request $request) {
//         $data = $request->validate(['id' => 'required|integer|min:1']);
//         $item = DB::table('orders')->where('id', $data['id'])->first();
//         return response()->json($item ?: ['message' => 'No order']);
//     });
// }



// Route::get('/health', function () {
//     try { DB::connection()->getPdo(); return 'ok'; }
//     catch (\Throwable $e) { return response('db error: '.$e->getMessage(), 500); }
// });

//  auth routes
require __DIR__ . '/auth.php';
