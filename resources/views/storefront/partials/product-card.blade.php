@props(['product'])
@php($money = app(\App\Services\MoneyFormatter::class))

<article class="group lux-card overflow-hidden">
    <a href="{{ route('products.show', $product) }}" class="block">
        <div class="aspect-[4/5] overflow-hidden bg-stone-100 dark:bg-neutral-800">
            <img src="{{ asset($product->featured_image_path ?: 'images/products/perfume-1.jpeg') }}" alt="{{ $product->localized('name') }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
        </div>
    </a>
    <div class="space-y-3 p-4">
        <div>
            <p class="text-xs uppercase tracking-[0.18em] text-[#ab7235]">{{ $product->brand?->localized('name') }}</p>
            <h3 class="mt-1 min-h-12 font-serif text-lg leading-6">
                <a href="{{ route('products.show', $product) }}">{{ $product->localized('name') }}</a>
            </h3>
        </div>
        <div class="flex items-center justify-between">
            <div>
                @if($product->sale_price)
                    <p class="text-sm text-neutral-500 line-through">{{ $money->format($product->price) }}</p>
                @endif
                <p class="font-semibold">{{ $money->format($product->currentPrice()) }}</p>
            </div>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('compare.toggle', $product) }}">
                    @csrf
                    <button class="p-2 text-neutral-500 hover:text-[#ab7235]" aria-label="{{ __('Compare') }}"><x-heroicon-o-scale class="h-5 w-5" /></button>
                </form>
                @auth
                    <form method="POST" action="{{ route('wishlist.toggle', $product) }}">
                        @csrf
                        <button class="p-2 text-neutral-500 hover:text-[#ab7235]" aria-label="{{ __('Wishlist') }}"><x-heroicon-o-heart class="h-5 w-5" /></button>
                    </form>
                @endauth
            </div>
        </div>
        <form method="POST" action="{{ route('cart.store') }}" class="pt-1">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">
            <button class="btn-secondary w-full py-2" type="submit">{{ __('Add to Cart') }}</button>
        </form>
    </div>
</article>
