<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly PricingService $pricing,
        private readonly ShippingService $shipping,
    ) {
    }

    public function createOrder(Cart $cart, array $data): Order
    {
        return DB::transaction(function () use ($cart, $data): Order {
            $cart = $this->cartService->recalculate($cart, $data['emirate'], $data['payment_method']);
            $cart->loadMissing('items.product', 'items.variant', 'coupon');

            $totals = $this->pricing->totals($cart, $data['emirate'], $data['payment_method']);
            $address = $this->addressPayload($data);
            $order = Order::create([
                'user_id' => Auth::id(),
                'coupon_id' => $cart->coupon_id,
                'delivery_slot_id' => $data['delivery_slot_id'] ?? null,
                'order_number' => $this->orderNumber(),
                'status' => $data['payment_method'] === 'cod' ? 'processing' : 'pending_payment',
                'payment_status' => $data['payment_method'] === 'cod' ? 'cod_pending' : 'pending',
                'delivery_status' => 'not_dispatched',
                'customer_name' => $data['full_name'],
                'customer_email' => $data['email'],
                'customer_phone' => $data['phone'],
                'company_trn' => $data['company_trn'] ?? null,
                'emirate' => $data['emirate'],
                'shipping_address' => $address,
                'billing_address' => ($data['billing_same_as_shipping'] ?? true) ? $address : ($data['billing_address'] ?? $address),
                'subtotal' => $totals['subtotal'],
                'vat_total' => $totals['vat_total'],
                'shipping_fee' => $totals['shipping_fee'],
                'cod_fee' => $totals['cod_fee'],
                'discount_total' => $totals['discount_total'],
                'total' => $totals['total'],
                'currency' => config('store.currency', 'AED'),
                'payment_method' => $data['payment_method'],
                'customer_notes' => $data['delivery_notes'] ?? null,
                'estimated_delivery_date' => $this->shipping->estimatedDeliveryDate($data['emirate'])->toDateString(),
                'idempotency_key' => (string) Str::uuid(),
            ]);

            foreach ($cart->items as $item) {
                $lineTotal = (float) $item->unit_price * $item->quantity;
                $vatAmount = $item->product->vat_taxable
                    ? round($lineTotal * ((float) config('store.vat_rate', 5) / 100), 2)
                    : 0;

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'sku' => $item->variant?->sku ?: $item->product->sku,
                    'name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'vat_amount' => $vatAmount,
                    'total' => $lineTotal,
                    'options' => ['size' => $item->variant?->size_label],
                ]);

                $this->decrementStock($item, $order);
            }

            if (Auth::check() && ($data['save_address'] ?? false)) {
                Address::updateOrCreate(
                    ['user_id' => Auth::id(), 'type' => 'shipping', 'is_default' => true],
                    $address + ['user_id' => Auth::id(), 'type' => 'shipping', 'is_default' => true],
                );
            }

            $cart->items()->delete();
            $cart->delete();
            request()->session()->forget('cart_id');

            Notification::route('mail', $order->customer_email)->notify(new OrderPlacedNotification($order));

            return $order->load('items.product', 'payment');
        });
    }

    private function addressPayload(array $data): array
    {
        return [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'emirate' => $data['emirate'],
            'city' => $data['city'],
            'street_address' => $data['street_address'],
            'building' => $data['building'],
            'apartment' => $data['apartment'] ?? null,
            'delivery_notes' => $data['delivery_notes'] ?? null,
        ];
    }

    private function decrementStock($item, Order $order): void
    {
        $product = $item->product()->lockForUpdate()->first();
        $before = $product->stock_quantity;
        $product->decrement('stock_quantity', $item->quantity);

        InventoryLog::create([
            'product_id' => $product->id,
            'product_variant_id' => $item->product_variant_id,
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'change' => -$item->quantity,
            'before_quantity' => $before,
            'after_quantity' => max(0, $before - $item->quantity),
            'reason' => 'order_placed',
        ]);

        if ($item->variant) {
            $item->variant->decrement('stock_quantity', $item->quantity);
        }
    }

    private function orderNumber(): string
    {
        return 'MDM-'.now()->format('ymd').'-'.strtoupper(Str::random(6));
    }
}
