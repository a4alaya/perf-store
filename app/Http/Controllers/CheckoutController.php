<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\DeliverySlot;
use App\Models\Order;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\Payments\PaymentManager;
use App\Services\ShippingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CheckoutController extends Controller
{
    public function index(CartService $cartService, ShippingService $shipping): View
    {
        $cart = $cartService->current();
        abort_if($cart->items->isEmpty(), 404);

        return view('storefront.checkout.index', [
            'cart' => $cart,
            'emirates' => config('store.emirates'),
            'deliverySlots' => DeliverySlot::query()->where('is_active', true)->get(),
            'progress' => $shipping->freeShippingProgress((float) $cart->subtotal),
            'metaTitle' => __('Secure Checkout'),
        ]);
    }

    public function store(CheckoutRequest $request, CartService $cartService, CheckoutService $checkout, PaymentManager $payments): RedirectResponse
    {
        $cart = $cartService->current();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => __('Your cart is empty.')]);
        }

        $order = $checkout->createOrder($cart, $request->validated());
        $payment = $payments->start($order);

        if ($payment->checkout_url && $order->payment_method !== 'cod') {
            return redirect()->away($payment->checkout_url);
        }

        return redirect()->route('checkout.confirmation', ['order' => $order]);
    }

    public function confirmation(string $locale, Order $order): View
    {
        $order->load('items.product', 'payment');

        return view('storefront.checkout.confirmation', [
            'order' => $order,
            'metaTitle' => __('Order Confirmation'),
        ]);
    }
}
