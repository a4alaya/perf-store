@extends('layouts.storefront')

@section('content')
@php($money = app(\App\Services\MoneyFormatter::class))
<section class="container-lux grid gap-10 py-12 lg:grid-cols-[0.95fr_1.05fr]">
    <div class="grid gap-4 sm:grid-cols-[90px_1fr]">
        <div class="hidden space-y-3 sm:block">
            @foreach($product->images->take(4) as $image)
                <img src="{{ asset($image->path) }}" alt="{{ $image->localized('alt_text') ?: $product->localized('name') }}" class="aspect-square w-full object-cover">
            @endforeach
        </div>
        <div class="bg-stone-100 dark:bg-neutral-900">
            <img src="{{ asset($product->featured_image_path ?: 'images/products/perfume-1.jpeg') }}" alt="{{ $product->localized('name') }}" class="aspect-[4/5] w-full object-cover">
        </div>
    </div>
    <div>
        <nav class="mb-5 text-sm text-neutral-500">
            <a href="{{ route('products.index') }}">{{ __('Shop') }}</a>
            <span>/</span>
            <a href="{{ route('brands.show', $product->brand) }}">{{ $product->brand->localized('name') }}</a>
        </nav>
        <h1 class="font-serif text-4xl font-semibold md:text-5xl">{{ $product->localized('name') }}</h1>
        <p class="mt-3 text-sm uppercase tracking-[0.2em] text-[#ab7235]">
            {{ $product->brand->localized('name') }} &middot; {{ __(ucwords(str_replace('_', ' ', $product->type))) }}
        </p>
        <div class="mt-5 flex items-center gap-3">
            <span class="text-2xl font-semibold">{{ $money->format($product->currentPrice()) }}</span>
            @if($product->sale_price)
                <span class="text-neutral-500 line-through">{{ $money->format($product->price) }}</span>
            @endif
        </div>
        <p class="muted-copy mt-6 max-w-2xl">{{ $product->localized('description') ?: $product->localized('short_description') }}</p>

        <form method="POST" action="{{ route('cart.store') }}" class="mt-8 space-y-5">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="flex flex-wrap gap-3">
                <input class="lux-input w-24" name="quantity" type="number" min="1" max="20" value="1">
                <button class="btn-primary" type="submit">{{ __('Add to Cart') }}</button>
                @auth
                    <button class="btn-secondary" formaction="{{ route('wishlist.toggle', $product) }}" type="submit">{{ __('Wishlist') }}</button>
                @endauth
            </div>
        </form>

        <div class="mt-10 grid gap-4 md:grid-cols-3">
            @foreach(['top_notes' => __('Top Notes'), 'middle_notes' => __('Middle Notes'), 'base_notes' => __('Base Notes')] as $field => $label)
                <div class="border border-stone-200 p-4 dark:border-neutral-800">
                    <h3 class="text-xs uppercase tracking-[0.18em] text-[#ab7235]">{{ $label }}</h3>
                    @php($notes = $product->{$field} ?: [])
                    <p class="mt-2 text-sm">{{ implode(', ', $notes[app()->getLocale()] ?? $notes['en'] ?? []) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-stone-100 py-14 dark:bg-neutral-900">
    <div class="container-lux">
        <h2 class="section-title">{{ __('Reviews') }}</h2>
        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_420px]">
            <div class="space-y-4">
                @forelse($product->reviews->where('status', 'approved') as $review)
                    <article class="lux-card p-5">
                        <div class="flex text-[#e9b26d]">
                            @for($i = 0; $i < $review->rating; $i++)
                                <x-heroicon-s-star class="h-4 w-4" />
                            @endfor
                        </div>
                        <h3 class="mt-3 font-semibold">{{ $review->localized('title') }}</h3>
                        <p class="muted-copy mt-2">{{ $review->localized('body') }}</p>
                        @if($review->is_verified_purchase)
                            <p class="mt-3 text-xs uppercase tracking-[0.18em] text-[#ab7235]">{{ __('Verified purchase') }}</p>
                        @endif
                    </article>
                @empty
                    <p class="muted-copy">{{ __('No approved reviews yet.') }}</p>
                @endforelse
            </div>
            @auth
                <form method="POST" action="{{ route('products.reviews.store', $product) }}" class="lux-card space-y-4 p-5">
                    @csrf
                    <h3 class="font-serif text-2xl">{{ __('Write a Review') }}</h3>
                    <select class="lux-input" name="rating" required>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}">{{ $i }} {{ __('stars') }}</option>
                        @endfor
                    </select>
                    <input class="lux-input" name="title" placeholder="{{ __('Review title') }}">
                    <textarea class="lux-input" name="body" rows="5" placeholder="{{ __('Share your fragrance experience') }}" required></textarea>
                    <button class="btn-primary" type="submit">{{ __('Submit Review') }}</button>
                </form>
            @else
                <div class="lux-card p-5">
                    <a class="btn-primary" href="{{ route('login') }}">{{ __('Login to Review') }}</a>
                </div>
            @endauth
        </div>
    </div>
</section>

@include('storefront.partials.product-rail', ['title' => __('Related Products'), 'products' => $related])

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product->localized('name'),
    'sku' => $product->sku,
    'brand' => ['@type' => 'Brand', 'name' => $product->brand->localized('name')],
    'offers' => [
        '@type' => 'Offer',
        'priceCurrency' => 'AED',
        'price' => $product->currentPrice(),
        'availability' => $product->stock_quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
    ],
    'aggregateRating' => $product->reviews_count ? [
        '@type' => 'AggregateRating',
        'ratingValue' => $product->average_rating,
        'reviewCount' => $product->reviews_count,
    ] : null,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection
