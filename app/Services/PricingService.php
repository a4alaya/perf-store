<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;

class PricingService
{
    public function __construct(private readonly ShippingService $shipping)
    {
    }

    public function totals(Cart $cart, ?string $emirate = null, string $paymentMethod = 'card'): array
    {
        $cart->loadMissing('items.product', 'coupon');

        $subtotal = $cart->items->sum(fn ($item) => (float) $item->unit_price * $item->quantity);
        $taxableSubtotal = $cart->items
            ->filter(fn ($item) => (bool) $item->product?->vat_taxable)
            ->sum(fn ($item) => (float) $item->unit_price * $item->quantity);

        $shippingFee = $this->shipping->feeFor($emirate, $subtotal);
        $discount = $this->discountFor($cart->coupon, $subtotal, $shippingFee);

        if ($cart->coupon?->type === 'free_shipping') {
            $shippingFee = 0;
        }

        $discountOnGoods = min($discount, $subtotal);
        $taxableAfterDiscount = max(0, $taxableSubtotal - $this->proportionalDiscount($taxableSubtotal, $subtotal, $discountOnGoods));
        $vatTotal = round($taxableAfterDiscount * ((float) config('store.vat_rate', 5) / 100), 2);
        $codFee = $this->shipping->codFeeFor($emirate, $paymentMethod);
        $total = max(0, $subtotal - $discountOnGoods) + $vatTotal + $shippingFee + $codFee;

        return [
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountOnGoods, 2),
            'vat_total' => round($vatTotal, 2),
            'shipping_fee' => round($shippingFee, 2),
            'cod_fee' => round($codFee, 2),
            'total' => round($total, 2),
        ];
    }

    private function discountFor(?Coupon $coupon, float $subtotal, float $shippingFee): float
    {
        if (! $coupon || ! $coupon->isUsableFor($subtotal)) {
            return 0;
        }

        return match ($coupon->type) {
            'percentage' => round($subtotal * ((float) $coupon->value / 100), 2),
            'fixed' => min($subtotal, (float) $coupon->value),
            'free_shipping' => $shippingFee,
            default => 0,
        };
    }

    private function proportionalDiscount(float $taxableSubtotal, float $subtotal, float $discount): float
    {
        if ($subtotal <= 0 || $taxableSubtotal <= 0 || $discount <= 0) {
            return 0;
        }

        return $discount * ($taxableSubtotal / $subtotal);
    }
}
