@extends('layouts.storefront')

@section('content')
<section class="border-b border-stone-200 bg-stone-100 py-12 dark:border-neutral-800 dark:bg-neutral-900">
    <div class="container-lux">
        <h1 class="section-title">{{ __('Shop Perfumes') }}</h1>
        <p class="muted-copy mt-3 max-w-2xl">{{ __('Browse luxury perfumes, oud, bakhoor, attars, gift sets, and Maison De Mystere UAE exclusives.') }}</p>
    </div>
</section>

<section class="py-10">
    <div class="container-lux grid gap-8 lg:grid-cols-[280px_1fr]">
        <aside class="h-fit border border-stone-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <form method="GET" action="{{ route('products.index') }}" class="space-y-5">
                <div>
                    <label class="mb-2 block text-xs uppercase tracking-[0.18em] text-neutral-500">{{ __('Search') }}</label>
                    <input class="lux-input" name="q" value="{{ request('q') }}" placeholder="{{ __('Name, brand, notes') }}">
                </div>
                <div>
                    <label class="mb-2 block text-xs uppercase tracking-[0.18em] text-neutral-500">{{ __('Category') }}</label>
                    <select class="lux-input" name="category">
                        <option value="">{{ __('All') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->localized('name') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-xs uppercase tracking-[0.18em] text-neutral-500">{{ __('Brand') }}</label>
                    <select class="lux-input" name="brand">
                        <option value="">{{ __('All') }}</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->slug }}" @selected(request('brand') === $brand->slug)>{{ $brand->localized('name') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-[0.18em] text-neutral-500">{{ __('Min') }}</label>
                        <input class="lux-input" name="min_price" value="{{ request('min_price') }}" inputmode="decimal">
                    </div>
                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-[0.18em] text-neutral-500">{{ __('Max') }}</label>
                        <input class="lux-input" name="max_price" value="{{ request('max_price') }}" inputmode="decimal">
                    </div>
                </div>
                <div>
                    <label class="mb-2 block text-xs uppercase tracking-[0.18em] text-neutral-500">{{ __('Sort') }}</label>
                    <select class="lux-input" name="sort">
                        <option value="newest">{{ __('Newest') }}</option>
                        <option value="price_low" @selected(request('sort') === 'price_low')>{{ __('Price low to high') }}</option>
                        <option value="price_high" @selected(request('sort') === 'price_high')>{{ __('Price high to low') }}</option>
                        <option value="best_selling" @selected(request('sort') === 'best_selling')>{{ __('Best selling') }}</option>
                        <option value="rating" @selected(request('sort') === 'rating')>{{ __('Rating') }}</option>
                    </select>
                </div>
                <button class="btn-primary w-full" type="submit">{{ __('Apply Filters') }}</button>
            </form>
        </aside>

        <div>
            <div class="mb-5 flex items-center justify-between">
                <p class="text-sm text-neutral-500">{{ $products->total() }} {{ __('products') }}</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($products as $product)
                    @include('storefront.partials.product-card', ['product' => $product])
                @empty
                    <div class="lux-card col-span-full p-10 text-center">{{ __('No perfumes matched your filters.') }}</div>
                @endforelse
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        </div>
    </div>
</section>
@endsection
