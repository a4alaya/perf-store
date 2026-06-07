<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('home', ['locale' => config('store.default_locale', 'en')]));
Route::post('/payments/webhook', PaymentWebhookController::class)->name('payments.webhook');
Route::get('/sitemap.xml', [SitemapController::class, 'xml'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::prefix('{locale}')
    ->whereIn('locale', config('store.locales'))
    ->middleware('locale')
    ->group(function (): void {
        Route::get('/', [StorefrontController::class, 'home'])->name('home');
        Route::get('/contact', [StorefrontController::class, 'contact'])->name('contact');
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
        Route::post('/products/{product:slug}/reviews', [ProductController::class, 'review'])->middleware('auth')->name('products.reviews.store');
        Route::get('/categories/{category:slug}', [ProductController::class, 'category'])->name('categories.show');
        Route::get('/brands/{brand:slug}', [ProductController::class, 'brand'])->name('brands.show');
        Route::get('/search', [ProductController::class, 'index'])->name('search');

        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{item}', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::post('/cart/coupon', [CartController::class, 'coupon'])->name('cart.coupon');

        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:checkout')->name('checkout.store');
        Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

        Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
        Route::post('/compare/{product:slug}', [CompareController::class, 'toggle'])->name('compare.toggle');
        Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

        Route::middleware('auth')->group(function (): void {
            Route::get('/account', [AccountController::class, 'dashboard'])->name('account.dashboard');
            Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
            Route::post('/wishlist/{product:slug}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        });

        Route::get('/pages/{page:slug}', [PageController::class, 'show'])->name('pages.show');
    });

Route::get('/dashboard', fn () => redirect()->route('account.dashboard', ['locale' => app()->getLocale()]))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
