<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AuthController;

// Home / Product listing
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Cart (session-based, no login required)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/buy/{product}', [CartController::class, 'buyNow'])->name('cart.buy');
Route::patch('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/place', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

// Auth
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    if (\Filament\Facades\Filament::auth()->attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended(\Filament\Facades\Filament::getUrl());
    }
    return back()->withErrors(['email' => __('filament-panels::pages/auth/login.messages.failed')])->withInput($request->only('email', 'remember'));
});
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User orders (protected) & Public Guest Order Tracking
Route::get('/track-order', [CheckoutController::class, 'trackOrder'])->name('orders.track');
Route::middleware('auth')->group(function () {
    Route::get('/orders', [CheckoutController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}', [CheckoutController::class, 'orderDetail'])->name('orders.show');
});

// Invoices
Route::get('/orders/{order}/invoice', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('orders.invoice.download');
Route::get('/orders/{order}/invoice/view', [\App\Http\Controllers\InvoiceController::class, 'stream'])->name('orders.invoice.stream');

// Admin Reports (PDF & CSV Exports)
Route::middleware('auth')->prefix('admin/reports-export')->name('admin.reports.')->group(function () {
    Route::get('/{type}/pdf', [\App\Http\Controllers\AdminReportController::class, 'pdf'])->name('pdf');
    Route::get('/{type}/csv', [\App\Http\Controllers\AdminReportController::class, 'csv'])->name('csv');
});
