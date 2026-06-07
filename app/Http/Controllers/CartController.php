<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItemRequest;
use App\Http\Requests\CouponRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\ShippingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function index(ShippingService $shipping): View
    {
        $cart = $this->cartService->current();

        return view('storefront.cart.index', [
            'cart' => $cart,
            'progress' => $shipping->freeShippingProgress((float) $cart->subtotal),
            'metaTitle' => __('Shopping Cart'),
        ]);
    }

    public function store(CartItemRequest $request): RedirectResponse
    {
        $product = Product::active()->findOrFail($request->integer('product_id'));
        $variant = $request->filled('product_variant_id')
            ? ProductVariant::query()->where('product_id', $product->id)->findOrFail($request->integer('product_variant_id'))
            : null;

        $this->cartService->add($product, $variant, $request->integer('quantity', 1));

        return back()->with('status', __('Added to cart.'));
    }

    public function update(Request $request, string $locale, int $item): RedirectResponse
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:20']]);
        $this->cartService->update($item, $request->integer('quantity'));

        return back()->with('status', __('Cart updated.'));
    }

    public function destroy(string $locale, int $item): RedirectResponse
    {
        $this->cartService->remove($item);

        return back()->with('status', __('Item removed.'));
    }

    public function coupon(CouponRequest $request): RedirectResponse
    {
        $this->cartService->applyCoupon($request->string('code')->toString());

        return back()->with('status', __('Coupon applied if valid.'));
    }
}
