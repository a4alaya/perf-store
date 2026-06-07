@extends('layouts.storefront')

@section('content')
@php($money = app(\App\Services\MoneyFormatter::class))
<section class="container-lux py-16">
    <div class="mx-auto max-w-3xl text-center">
        <x-heroicon-o-check-circle class="mx-auto h-14 w-14 text-[#ab7235]" />
        <h1 class="section-title mt-4">{{ __('Thank you for your order') }}</h1>
        <p class="muted-copy mt-3">{{ __('Your Maison De Mystere order is being prepared. A VAT-compliant invoice summary is below.') }}</p>
    </div>
    <div class="mx-auto mt-10 max-w-4xl border border-stone-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
        <div class="grid gap-4 border-b border-stone-200 pb-5 text-sm md:grid-cols-3 dark:border-neutral-800">
            <div><span class="text-neutral-500">{{ __('Order') }}</span><p class="font-semibold">{{ $order->order_number }}</p></div>
            <div><span class="text-neutral-500">{{ __('Status') }}</span><p class="font-semibold">{{ __(ucwords(str_replace('_', ' ', $order->status))) }}</p></div>
            <div><span class="text-neutral-500">{{ __('Estimated Delivery') }}</span><p class="font-semibold">{{ $order->estimated_delivery_date?->format('d M Y') }}</p></div>
        </div>
        <div class="mt-6 space-y-3">
            @foreach($order->items as $item)
                <div class="flex justify-between gap-4 text-sm">
                    <span>{{ $item->quantity }} x {{ $item->localized('name') }}</span>
                    <span>{{ $money->format($item->total) }}</span>
                </div>
            @endforeach
        </div>
        <div class="mt-6 space-y-2 border-t border-stone-200 pt-5 text-sm dark:border-neutral-800">
            <div class="flex justify-between"><span>{{ __('Subtotal') }}</span><span>{{ $money->format($order->subtotal) }}</span></div>
            <div class="flex justify-between"><span>{{ __('UAE VAT 5%') }}</span><span>{{ $money->format($order->vat_total) }}</span></div>
            <div class="flex justify-between"><span>{{ __('Shipping') }}</span><span>{{ $money->format($order->shipping_fee) }}</span></div>
            <div class="flex justify-between"><span>{{ __('Discount') }}</span><span>-{{ $money->format($order->discount_total) }}</span></div>
            <div class="flex justify-between text-lg font-semibold"><span>{{ __('Total') }}</span><span>{{ $money->format($order->total) }}</span></div>
        </div>
        <div class="mt-8 flex justify-center">
            <a class="btn-primary" href="{{ route('products.index') }}">{{ __('Continue Shopping') }}</a>
        </div>
    </div>
</section>
@endsection
