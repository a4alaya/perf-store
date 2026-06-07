@extends('layouts.storefront')

@section('content')
@php
    $locale = app()->getLocale();
    $sectionHref = function (?string $url) use ($locale): string {
        if (blank($url)) {
            return '#';
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        $path = ltrim($url, '/');
        $path = preg_replace('/^(en|ar)(\/|$)/', $locale.'$2', $path, 1, $count);

        if ($count === 0) {
            $path = $locale.'/'.ltrim($path, '/');
        }

        return url($path);
    };

    $sectionBand = fn ($section) => match ($section->background_style) {
        'dark' => 'bg-[#1a1a1a] text-stone-100',
        'soft' => 'bg-stone-100 dark:bg-neutral-900',
        'light' => 'bg-[#f7f0e7] dark:bg-neutral-950',
        default => 'bg-white dark:bg-neutral-950',
    };

    $sectionImage = fn (?string $path, string $fallback) => asset($path ?: $fallback);
@endphp

@foreach($sections as $section)
    @switch($section->type)
        @case('hero')
            <section class="relative overflow-hidden bg-[#f7f0e7] text-neutral-950 dark:bg-[#1a1a1a] dark:text-stone-100">
                <div class="absolute inset-0 opacity-15 mix-blend-multiply dark:opacity-30 dark:mix-blend-normal">
                    <img src="{{ $sectionImage($section->image_path, 'images/products/perfume-1.jpeg') }}" alt="" class="h-full w-full object-cover">
                </div>
                <div class="container-lux relative grid min-h-[680px] items-center gap-10 py-20 lg:grid-cols-[1.05fr_0.95fr]">
                    <div class="max-w-3xl">
                        @if($section->localized('eyebrow'))
                            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.24em] text-[#ab7235]">{{ $section->localized('eyebrow') }}</p>
                        @endif
                        <h1 class="font-serif text-5xl font-semibold leading-tight md:text-7xl">{{ $section->localized('title') }}</h1>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-neutral-700 dark:text-stone-300">{{ $section->localized('subtitle') }}</p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            @if($section->localized('cta_label'))
                                <a class="btn-primary" href="{{ $sectionHref($section->cta_url) }}">{{ $section->localized('cta_label') }}</a>
                            @endif
                            @if($section->localized('secondary_cta_label'))
                                <a class="btn-secondary border-[#ab7235]/60 text-neutral-900 dark:border-stone-500 dark:text-stone-100" href="{{ $sectionHref($section->secondary_cta_url) }}">{{ $section->localized('secondary_cta_label') }}</a>
                            @endif
                        </div>
                    </div>
                    <div class="relative">
                        <div class="mx-auto max-w-md border border-[#e9b26d]/60 bg-white/70 p-4 shadow-2xl dark:bg-neutral-950/70">
                            <img src="{{ $sectionImage($section->secondary_image_path, 'images/products/perfume-2.jpeg') }}" alt="{{ $section->localized('title') }}" class="aspect-[4/5] w-full object-cover">
                        </div>
                    </div>
                </div>
            </section>
            @break

        @case('feature_strip')
            <section class="border-b border-stone-200 bg-white py-5 dark:border-neutral-800 dark:bg-neutral-950">
                <div class="container-lux grid gap-4 text-sm md:grid-cols-3">
                    @foreach($section->activeItems as $item)
                        <div class="flex items-center gap-3">
                            @switch($item->icon)
                                @case('shield-check')
                                    <x-heroicon-o-shield-check class="h-5 w-5 text-[#ab7235]" />
                                    @break
                                @case('receipt-percent')
                                    <x-heroicon-o-receipt-percent class="h-5 w-5 text-[#ab7235]" />
                                    @break
                                @case('sparkles')
                                    <x-heroicon-o-sparkles class="h-5 w-5 text-[#ab7235]" />
                                    @break
                                @default
                                    <x-heroicon-o-truck class="h-5 w-5 text-[#ab7235]" />
                            @endswitch
                            <span>{{ $item->localized('title') }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
            @break

        @case('split_product_feature')
            <section class="{{ $sectionBand($section) }} py-20">
                <div class="container-lux grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                    <div>
                        <h2 class="font-serif text-4xl font-semibold">{{ $section->localized('title') }}</h2>
                        <p class="mt-4 max-w-md text-neutral-700 dark:text-stone-300">{{ $section->localized('subtitle') }}</p>
                        @if($section->localized('cta_label'))
                            <a class="btn-primary mt-8" href="{{ $sectionHref($section->cta_url) }}">{{ $section->localized('cta_label') }}</a>
                        @endif
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach(($sectionProducts[$section->id] ?? collect())->take((int) $section->limit) as $product)
                            @include('storefront.partials.product-card', ['product' => $product])
                        @endforeach
                    </div>
                </div>
            </section>
            @break

        @case('product_rail')
            @if($section->background_style === 'soft')
                <section class="bg-stone-100 py-20 dark:bg-neutral-900">
                    <div class="container-lux">
                        <div class="mb-8 flex items-end justify-between gap-4">
                            <div>
                                <h2 class="section-title">{{ $section->localized('title') }}</h2>
                                @if($section->localized('subtitle'))
                                    <p class="muted-copy mt-2">{{ $section->localized('subtitle') }}</p>
                                @endif
                            </div>
                            @if($section->localized('cta_label'))
                                <a class="btn-secondary hidden sm:inline-flex" href="{{ $sectionHref($section->cta_url) }}">{{ $section->localized('cta_label') }}</a>
                            @endif
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach(($sectionProducts[$section->id] ?? collect()) as $product)
                                @include('storefront.partials.product-card', ['product' => $product])
                            @endforeach
                        </div>
                    </div>
                </section>
            @else
                @include('storefront.partials.product-rail', [
                    'title' => $section->localized('title'),
                    'products' => $sectionProducts[$section->id] ?? collect(),
                ])
            @endif
            @break

        @case('taxonomy_grid')
            <section class="py-20">
                <div class="container-lux">
                    <h2 class="section-title">{{ $section->localized('title') }}</h2>
                    @if($section->localized('subtitle'))
                        <p class="muted-copy mt-2">{{ $section->localized('subtitle') }}</p>
                    @endif
                    <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($sectionTaxonomies[$section->id] ?? collect() as $taxonomy)
                            <a class="flex items-center justify-between border border-stone-200 bg-white p-5 transition hover:border-[#ab7235] dark:border-neutral-800 dark:bg-neutral-900" href="{{ $section->taxonomy_source === 'brands' ? route('brands.show', $taxonomy) : route('categories.show', $taxonomy) }}">
                                <span>
                                    <span class="block font-serif text-xl">{{ $taxonomy->localized('name') }}</span>
                                    @if($section->taxonomy_source === 'brands')
                                        <span class="mt-1 block text-xs uppercase tracking-[0.18em] text-neutral-500">{{ $taxonomy->products_count }} {{ __('items') }}</span>
                                    @endif
                                </span>
                                <x-heroicon-o-arrow-right class="h-5 w-5 {{ $locale === 'ar' ? 'rotate-180' : '' }}" />
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
            @break

        @case('review_grid')
            <section class="bg-white py-20 dark:bg-neutral-950">
                <div class="container-lux">
                    <h2 class="section-title">{{ $section->localized('title') }}</h2>
                    @if($section->localized('subtitle'))
                        <p class="muted-copy mt-2">{{ $section->localized('subtitle') }}</p>
                    @endif
                    <div class="mt-8 grid gap-5 md:grid-cols-3">
                        @forelse($reviews->take((int) $section->limit ?: 6) as $review)
                            <article class="lux-card p-6">
                                <div class="mb-3 flex text-[#e9b26d]">
                                    @for($i = 0; $i < $review->rating; $i++)
                                        <x-heroicon-s-star class="h-4 w-4" />
                                    @endfor
                                </div>
                                <p class="text-sm leading-7">{{ $review->localized('body') }}</p>
                                <p class="mt-4 text-xs uppercase tracking-[0.18em] text-neutral-500">{{ $review->product?->localized('name') }}</p>
                            </article>
                        @empty
                            <p class="muted-copy">{{ __('Reviews will appear here once approved by the Maison De Mystere team.') }}</p>
                        @endforelse
                    </div>
                </div>
            </section>
            @break
    @endswitch
@endforeach
@endsection
