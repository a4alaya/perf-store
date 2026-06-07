@extends('layouts.storefront')

@section('content')
@php($money = app(\App\Services\MoneyFormatter::class))
<section class="container-lux py-12">
    <h1 class="section-title">{{ __('My Account') }}</h1>
    <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_360px]">
        <div class="lux-card overflow-hidden">
            <div class="border-b border-stone-200 p-5 dark:border-neutral-800">
                <h2 class="font-serif text-2xl">{{ __('Recent Orders') }}</h2>
            </div>
            <div class="divide-y divide-stone-200 dark:divide-neutral-800">
                @forelse($orders as $order)
                    <div class="grid gap-3 p-5 text-sm md:grid-cols-4">
                        <span class="font-semibold">{{ $order->order_number }}</span>
                        <span>{{ __(ucwords(str_replace('_', ' ', $order->status))) }}</span>
                        <span>{{ $money->format($order->total) }}</span>
                        <a class="text-[#ab7235]" href="{{ route('checkout.confirmation', $order) }}">{{ __('View invoice') }}</a>
                    </div>
                @empty
                    <p class="p-5 muted-copy">{{ __('No orders yet.') }}</p>
                @endforelse
            </div>
        </div>

        <aside class="space-y-6">
            <div class="lux-card p-5">
                <h2 class="font-serif text-2xl">{{ __('Profile') }}</h2>
                <p class="mt-2">{{ auth()->user()->name }}</p>
                <p class="text-sm text-neutral-500">{{ auth()->user()->email }}</p>
                <a class="btn-secondary mt-4 w-full" href="{{ route('profile.edit') }}">{{ __('Edit Profile') }}</a>
            </div>
            <div class="lux-card p-5">
                <h2 class="font-serif text-2xl">{{ __('Wishlist') }}</h2>
                <p class="muted-copy mt-2">{{ $wishlist->count() }} {{ __('saved fragrances') }}</p>
                <a class="btn-secondary mt-4 w-full" href="{{ route('wishlist.index') }}">{{ __('Open Wishlist') }}</a>
            </div>
        </aside>
    </div>
</section>
@endsection
