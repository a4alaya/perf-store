@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $cart = app(\App\Services\CartService::class)->current();
    $cartCount = $cart->items->sum('quantity');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $metaTitle ?? config('store.name') }}</title>
    <meta name="description" content="{{ $metaDescription ?? __('Luxury niche perfumes, oud, bakhoor, attars, and gift sets delivered across the UAE.') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="{{ $metaTitle ?? config('store.name') }}">
    <meta property="og:description" content="{{ $metaDescription ?? __('Maison De Mystere curates rare fragrances for UAE connoisseurs.') }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('images/mdm-logo.png') }}">
    <link rel="icon" href="{{ asset('images/mdm-logo.png') }}">
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ searchOpen: false, cartOpen: false, mobileOpen: false }">
    <div class="min-h-screen">
        <div class="bg-[#1a1a1a] text-xs text-stone-200">
            <div class="container-lux flex items-center justify-between py-2">
                <p>{{ __('Free UAE delivery over AED 500') }}</p>
                <p>{{ __('Secure card payments, Apple Pay and cash on delivery where enabled') }}</p>
            </div>
        </div>

        <header class="sticky top-0 z-40 border-b border-stone-200 bg-white/95 backdrop-blur dark:border-neutral-800 dark:bg-neutral-950/95">
            <div class="container-lux flex h-20 items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="Maison De Mystere">
                    <img src="{{ asset('images/mdm-logo.png') }}" alt="Maison De Mystere Perfumes logo" class="h-12 w-20 object-contain">
                </a>

                <nav class="hidden items-center gap-8 text-sm font-medium uppercase tracking-[0.16em] text-neutral-700 lg:flex dark:text-stone-200">
                    <a class="hover:text-[#ab7235]" href="{{ route('products.index') }}">{{ __('Shop') }}</a>
                    <a class="hover:text-[#ab7235]" href="{{ route('products.index', ['type' => 'oud']) }}">{{ __('Oud') }}</a>
                    <a class="hover:text-[#ab7235]" href="{{ route('products.index', ['type' => 'gift_set']) }}">{{ __('Gift Sets') }}</a>
                    <a class="hover:text-[#ab7235]" href="{{ route('contact') }}">{{ __('Contact') }}</a>
                </nav>

                <div class="flex items-center gap-2">
                    <button type="button" class="p-2 text-neutral-700 hover:text-[#ab7235] dark:text-stone-200" x-on:click="searchOpen = true" aria-label="{{ __('Search') }}">
                        <x-heroicon-o-magnifying-glass class="h-5 w-5" />
                    </button>
                    <a class="hidden p-2 text-neutral-700 hover:text-[#ab7235] dark:text-stone-200 sm:inline-flex" href="{{ auth()->check() ? route('wishlist.index') : route('login') }}" aria-label="{{ __('Wishlist') }}">
                        <x-heroicon-o-heart class="h-5 w-5" />
                    </a>
                    <a class="hidden p-2 text-neutral-700 hover:text-[#ab7235] dark:text-stone-200 sm:inline-flex" href="{{ route('compare.index') }}" aria-label="{{ __('Compare') }}">
                        <x-heroicon-o-scale class="h-5 w-5" />
                    </a>
                    <button type="button" class="relative p-2 text-neutral-700 hover:text-[#ab7235] dark:text-stone-200" x-on:click="cartOpen = true" aria-label="{{ __('Cart') }}">
                        <x-heroicon-o-shopping-bag class="h-5 w-5" />
                        @if($cartCount)
                            <span class="absolute -right-1 -top-1 grid h-5 w-5 place-items-center rounded-full bg-[#e9b26d] text-[10px] font-bold text-neutral-950">{{ $cartCount }}</span>
                        @endif
                    </button>
                    <div x-data="themeToggle">
                        <button type="button" class="p-2 text-neutral-700 hover:text-[#ab7235] dark:text-stone-200" x-on:click="toggle" aria-label="{{ __('Toggle dark mode') }}">
                            <x-heroicon-o-moon x-show="!dark" class="h-5 w-5" />
                            <x-heroicon-o-sun x-show="dark" class="h-5 w-5" />
                        </button>
                    </div>
                    <a class="px-2 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-700 hover:text-[#ab7235] dark:text-stone-200" href="{{ route(Route::currentRouteName() ?: 'home', array_merge(request()->route()?->parameters() ?? [], ['locale' => $locale === 'ar' ? 'en' : 'ar']) + request()->query()) }}">
                        {{ $locale === 'ar' ? 'EN' : 'AR' }}
                    </a>
                    <span class="hidden text-xs font-semibold tracking-[0.18em] text-[#ab7235] sm:block">AED</span>
                    <button type="button" class="p-2 lg:hidden" x-on:click="mobileOpen = !mobileOpen" aria-label="{{ __('Menu') }}">
                        <x-heroicon-o-bars-3 class="h-6 w-6" />
                    </button>
                </div>
            </div>

            <div x-show="mobileOpen" x-cloak class="border-t border-stone-200 bg-white px-4 py-4 dark:border-neutral-800 dark:bg-neutral-950 lg:hidden">
                <div class="flex flex-col gap-3 text-sm uppercase tracking-[0.16em]">
                    <a href="{{ route('products.index') }}">{{ __('Shop') }}</a>
                    <a href="{{ route('products.index', ['type' => 'oud']) }}">{{ __('Oud') }}</a>
                    <a href="{{ route('products.index', ['type' => 'gift_set']) }}">{{ __('Gift Sets') }}</a>
                    <a href="{{ route('contact') }}">{{ __('Contact') }}</a>
                </div>
            </div>
        </header>

        @if(session('status'))
            <div class="border-b border-[#e9b26d]/40 bg-[#e9b26d]/15 py-3 text-center text-sm text-neutral-900 dark:text-stone-100">{{ session('status') }}</div>
        @endif

        <main>
            @yield('content')
        </main>

        <footer class="border-t border-neutral-800 bg-[#1a1a1a] py-12 text-stone-200">
            <div class="container-lux grid gap-10 md:grid-cols-[1.2fr_1fr_1fr_1fr]">
                <div>
                    <img src="{{ asset('images/mdm-logo.png') }}" alt="Maison De Mystere logo" class="mb-5 h-16 w-28 object-contain">
                    <p class="max-w-sm text-sm leading-7 text-stone-300">{{ __('A luxury niche perfume boutique curating rare fragrances, oud, bakhoor, attars, and meaningful olfactory experiences across the UAE.') }}</p>
                </div>
                <div>
                    <h3 class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-[#e9b26d]">{{ __('Shop') }}</h3>
                    <div class="flex flex-col gap-2 text-sm">
                        <a href="{{ route('products.index') }}">{{ __('All Perfumes') }}</a>
                        <a href="{{ route('products.index', ['gender' => 'men']) }}">{{ __('Men') }}</a>
                        <a href="{{ route('products.index', ['gender' => 'women']) }}">{{ __('Women') }}</a>
                        <a href="{{ route('products.index', ['gender' => 'unisex']) }}">{{ __('Unisex') }}</a>
                    </div>
                </div>
                <div>
                    <h3 class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-[#e9b26d]">{{ __('Support') }}</h3>
                    <div class="flex flex-col gap-2 text-sm">
                        <a href="{{ route('pages.show', ['page' => 'faq']) }}">{{ __('FAQ') }}</a>
                        <a href="{{ route('pages.show', ['page' => 'return-and-refund-policy']) }}">{{ __('Returns') }}</a>
                        <a href="{{ route('pages.show', ['page' => 'privacy-policy']) }}">{{ __('Privacy') }}</a>
                        <a href="{{ route('pages.show', ['page' => 'terms-and-conditions']) }}">{{ __('Terms') }}</a>
                    </div>
                </div>
                <div>
                    <h3 class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-[#e9b26d]">{{ __('Newsletter') }}</h3>
                    <form method="POST" action="{{ route('newsletter.store') }}" class="flex gap-2">
                        @csrf
                        <input class="lux-input border-neutral-700 bg-neutral-900 text-stone-100" name="email" type="email" placeholder="{{ __('Email address') }}" required>
                        <button class="btn-primary px-4" type="submit" aria-label="{{ __('Subscribe') }}">
                            <x-heroicon-o-arrow-right class="h-4 w-4 {{ $isRtl ? 'rotate-180' : '' }}" />
                        </button>
                    </form>
                </div>
            </div>
        </footer>
    </div>

    <div x-show="searchOpen" x-cloak class="fixed inset-0 z-50 bg-neutral-950/80 p-4 backdrop-blur" x-on:keydown.escape.window="searchOpen = false">
        <div class="mx-auto mt-24 max-w-2xl bg-white p-6 shadow-2xl dark:bg-neutral-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-serif text-2xl">{{ __('Search Maison De Mystere') }}</h2>
                <button x-on:click="searchOpen = false" aria-label="{{ __('Close') }}"><x-heroicon-o-x-mark class="h-6 w-6" /></button>
            </div>
            <form action="{{ route('search') }}" method="GET" class="flex gap-3">
                <input class="lux-input" name="q" placeholder="{{ __('Search by perfume, brand, notes, oud...') }}" autofocus>
                <button class="btn-primary" type="submit">{{ __('Search') }}</button>
            </form>
        </div>
    </div>

    <aside x-show="cartOpen" x-cloak class="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white p-6 shadow-2xl dark:bg-neutral-950" x-on:keydown.escape.window="cartOpen = false">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="font-serif text-2xl">{{ __('Mini Cart') }}</h2>
            <button x-on:click="cartOpen = false" aria-label="{{ __('Close cart') }}"><x-heroicon-o-x-mark class="h-6 w-6" /></button>
        </div>
        <div class="space-y-4">
            @forelse($cart->items as $item)
                <div class="flex gap-3 border-b border-stone-200 pb-4 dark:border-neutral-800">
                    <img src="{{ asset($item->product->featured_image_path ?: 'images/products/perfume-1.jpeg') }}" alt="{{ $item->product->localized('name') }}" class="h-20 w-16 object-cover">
                    <div class="flex-1">
                        <p class="font-medium">{{ $item->product->localized('name') }}</p>
                        <p class="text-sm text-neutral-500">{{ $item->quantity }} x {{ app(\App\Services\MoneyFormatter::class)->format($item->unit_price) }}</p>
                    </div>
                </div>
            @empty
                <p class="muted-copy">{{ __('Your cart is waiting for its first fragrance.') }}</p>
            @endforelse
        </div>
        <div class="mt-6 border-t border-stone-200 pt-6 dark:border-neutral-800">
            <div class="mb-4 flex justify-between text-sm font-semibold">
                <span>{{ __('Total') }}</span>
                <span>{{ app(\App\Services\MoneyFormatter::class)->format($cart->total) }}</span>
            </div>
            <a class="btn-primary w-full" href="{{ route('cart.index') }}">{{ __('View Cart') }}</a>
        </div>
    </aside>
</body>
</html>
