<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function __construct(private readonly PricingService $pricing)
    {
    }

    public function current(?Request $request = null): Cart
    {
        $request ??= request();
        $sessionId = $request->session()->getId();
        $sessionCartId = $request->session()->get('cart_id');

        if ($sessionCartId) {
            $sessionCart = Cart::query()->with('items.product.brand', 'items.variant', 'coupon')->find($sessionCartId);

            if ($sessionCart) {
                if (Auth::check() && ! $sessionCart->user_id) {
                    $sessionCart->update(['user_id' => Auth::id()]);
                }

                return $sessionCart;
            }
        }

        $cart = Cart::query()
            ->with('items.product.brand', 'items.variant', 'coupon')
            ->when(Auth::check(), fn ($query) => $query->where('user_id', Auth::id()))
            ->when(! Auth::check(), fn ($query) => $query->where('session_id', $sessionId))
            ->first();

        $cart = $cart ?: Cart::create([
            'user_id' => Auth::id(),
            'session_id' => $sessionId,
            'currency' => config('store.currency', 'AED'),
            'expires_at' => now()->addDays(14),
        ]);

        $request->session()->put('cart_id', $cart->id);

        return $cart;
    }

    public function add(Product $product, ?ProductVariant $variant = null, int $quantity = 1): Cart
    {
        $cart = $this->current();
        $price = $variant?->currentPrice() ?? $product->currentPrice();

        $item = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variant?->id)
            ->first();

        if ($item) {
            $item->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity' => max(1, $quantity),
                'unit_price' => $price,
                'item_name' => $product->name,
            ]);
        }

        return $this->recalculate($cart);
    }

    public function update(int $itemId, int $quantity): Cart
    {
        $cart = $this->current();
        $item = $cart->items()->findOrFail($itemId);

        $quantity <= 0 ? $item->delete() : $item->update(['quantity' => $quantity]);

        return $this->recalculate($cart);
    }

    public function remove(int $itemId): Cart
    {
        $cart = $this->current();
        $cart->items()->whereKey($itemId)->delete();

        return $this->recalculate($cart);
    }

    public function applyCoupon(string $code): Cart
    {
        $cart = $this->current();
        $coupon = Coupon::query()->where('code', strtoupper(trim($code)))->first();

        if ($coupon && $coupon->isUsableFor((float) $cart->subtotal)) {
            $cart->coupon()->associate($coupon);
            $cart->save();
        }

        return $this->recalculate($cart);
    }

    public function recalculate(Cart $cart, ?string $emirate = null, string $paymentMethod = 'card'): Cart
    {
        $totals = $this->pricing->totals($cart->refresh(), $emirate, $paymentMethod);
        $cart->update($totals);

        return $cart->refresh()->load('items.product.brand', 'items.variant', 'coupon');
    }

    public function mergeGuestCartIntoUser(Request $request): void
    {
        if (! Auth::check()) {
            return;
        }

        Cart::query()
            ->where('session_id', $request->session()->getId())
            ->whereNull('user_id')
            ->update(['user_id' => Auth::id()]);
    }
}
