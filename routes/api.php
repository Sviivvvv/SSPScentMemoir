<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Api\TokenController;

Route::get('/health', function () {
    DB::connection('mongodb')->getMongoDB()->command(['ping' => 1]);
    return response()->json(['mongo' => 'ok']);
})->middleware('throttle:api-public');

// token (public)
Route::post('/auth/token', [TokenController::class, 'issue'])->middleware('throttle:api-public');

// catalogue (public)
Route::middleware('throttle:api-public')->group(function () {
    Route::get('/products', [ProductController::class, 'apiProducts']);
    Route::get('/products/{id}', [ProductController::class, 'apiProduct']);
    Route::get('/categories', [ProductController::class, 'apiCategories']);
    Route::get('/categories/{id}/products', [ProductController::class, 'apiCategoryProducts']);
});

// protected (Sanctum + abilities)
Route::middleware(['auth:sanctum', 'throttle:api-auth'])->group(function () {
    Route::delete('/auth/token', [TokenController::class, 'revokeCurrent']);
    Route::delete('/auth/tokens', [TokenController::class, 'revokeAll']);

    // cart
    Route::get('/cart', [CheckoutController::class, 'apiCartShow'])->middleware('abilities:cart:read');
    Route::post('/cart/items', [CheckoutController::class, 'apiCartAdd'])->middleware('abilities:cart:write');
    Route::patch('/cart/items', [CheckoutController::class, 'apiCartUpdate'])->middleware('abilities:cart:write');
    Route::delete('/cart/items', [CheckoutController::class, 'apiCartRemove'])->middleware('abilities:cart:write');

    // orders
    Route::get('/orders', [CheckoutController::class, 'apiOrders'])->middleware('abilities:orders:read');
    Route::get('/orders/{id}', [CheckoutController::class, 'apiOrderShow'])->middleware('abilities:orders:read');
    Route::post('/orders/checkout', [CheckoutController::class, 'apiCheckout'])->middleware('abilities:orders:create');
});
