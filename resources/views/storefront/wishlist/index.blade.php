@extends('layouts.storefront')

@section('content')
<section class="container-lux py-12">
    <h1 class="section-title">{{ __('Wishlist') }}</h1>
    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($items as $item)
            @include('storefront.partials.product-card', ['product' => $item->product])
        @empty
            <div class="lux-card col-span-full p-10 text-center">{{ __('Your wishlist is empty.') }}</div>
        @endforelse
    </div>
</section>
@endsection
