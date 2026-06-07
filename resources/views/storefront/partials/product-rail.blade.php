@if($products->isNotEmpty())
<section class="py-20">
    <div class="container-lux">
        <div class="mb-8 flex items-center justify-between gap-4">
            <h2 class="section-title">{{ $title }}</h2>
            <a class="btn-secondary hidden sm:inline-flex" href="{{ route('products.index') }}">{{ __('View All') }}</a>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($products->take(4) as $product)
                @include('storefront.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif
