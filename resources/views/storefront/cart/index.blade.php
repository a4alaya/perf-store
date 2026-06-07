@extends('layouts.storefront')

@section('content')
@php($money = app(\App\Services\MoneyFormatter::class))
<section class="container-lux py-12">
    <h1 class="section-title">{{ __('Shopping Cart') }}</h1>
    <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_380px]">
        <div class="space-y-4">
            @forelse($cart->items as $item)
                <div class="lux-card grid gap-4 p-4 sm:grid-cols-[110px_1fr_auto] sm:items-center">
                    <img src="{{ asset($item->product->featured_image_path ?: 'images/products/perfume-1.jpeg') }}" alt="{{ $item->product->localized('name') }}" class="aspect-square w-full object-cover sm:w-28">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-[#ab7235]">{{ $item->product->brand?->localized('name') }}</p>
                        <h2 class="font-serif text-xl">{{ $item->product->localized('name') }}</h2>
                        @if($item->variant?->size_label)
                            <p class="mt-1 text-sm text-neutral-500">{{ $item->variant->size_label }}</p>
                        @endif
                        <p class="mt-2 font-semibold">{{ $money->format($item->unit_price) }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input class="lux-input w-20" name="quantity" type="number" min="0" max="20" value="{{ $item->quantity }}">
                            <button class="btn-secondary py-2" type="submit">{{ __('Update') }}</button>
                        </form>
                        <form method="POST" action="{{ route('cart.destroy', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button class="p-2 text-neutral-500 hover:text-red-600" aria-label="{{ __('Remove') }}"><x-heroicon-o-trash class="h-5 w-5" /></button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="lux-card p-10 text-center">
                    <p class="font-serif text-2xl">{{ __('Your cart is empty') }}</p>
                    <a class="btn-primary mt-6" href="{{ route('products.index') }}">{{ __('Shop Perfumes') }}</a>
                </div>
            @endforelse
        </div>

        <aside class="h-fit border border-stone-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
            <h2 class="font-serif text-2xl">{{ __('Order Summary') }}</h2>
            <div class="mt-5 space-y-3 text-sm">
                <div class="h-2 overflow-hidden bg-stone-200 dark:bg-neutral-800">
                    <div class="h-full bg-[#e9b26d]" style="width: {{ $progress['percent'] }}%"></div>
                </div>
                <p class="text-neutral-500">
                    {{ $progress['qualified'] ? __('You have qualified for free shipping.') : __('Add :amount more for free shipping.', ['amount' => $money->format($progress['remaining'])]) }}
                </p>
                <div class="flex justify-between"><span>{{ __('Subtotal') }}</span><span>{{ $money->format($cart->subtotal) }}</span></div>
                <div class="flex justify-between"><span>{{ __('VAT 5%') }}</span><span>{{ $money->format($cart->vat_total) }}</span></div>
                <div class="flex justify-between"><span>{{ __('Discount') }}</span><span>-{{ $money->format($cart->discount_total) }}</span></div>
                <div class="flex justify-between"><span>{{ __('Shipping') }}</span><span>{{ $money->format($cart->shipping_fee) }}</span></div>
                <div class="border-t border-stone-200 pt-3 text-base font-semibold dark:border-neutral-800">
                    <div class="flex justify-between"><span>{{ __('Total') }}</span><span>{{ $money->format($cart->total) }}</span></div>
                </div>
            </div>
            <form method="POST" action="{{ route('cart.coupon') }}" class="mt-5 flex gap-2">
                @csrf
                <input class="lux-input" name="code" placeholder="{{ __('Coupon code') }}">
                <button class="btn-secondary px-4" type="submit">{{ __('Apply') }}</button>
            </form>
            <a class="btn-primary mt-6 w-full" href="{{ route('checkout.index') }}">{{ __('Checkout') }}</a>
        </aside>
    </div>
</section>
@endsection
