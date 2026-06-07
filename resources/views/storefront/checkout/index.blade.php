@extends('layouts.storefront')

@section('content')
@php($money = app(\App\Services\MoneyFormatter::class))
<section class="container-lux py-12">
    <h1 class="section-title">{{ __('Secure Checkout') }}</h1>
    @if($errors->any())
        <div class="mt-6 border border-red-300 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-100">
            {{ $errors->first() }}
        </div>
    @endif
    <form method="POST" action="{{ route('checkout.store') }}" class="mt-8 grid gap-8 lg:grid-cols-[1fr_380px]">
        @csrf
        <div class="space-y-6">
            <section class="lux-card p-6">
                <h2 class="font-serif text-2xl">{{ __('Shipping Address') }}</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <input class="lux-input" name="full_name" value="{{ old('full_name', optional(auth()->user())->name) }}" placeholder="{{ __('Full name') }}" required>
                    <input class="lux-input" name="email" type="email" value="{{ old('email', optional(auth()->user())->email) }}" placeholder="{{ __('Email') }}" required>
                    <input class="lux-input" name="phone" value="{{ old('phone', optional(auth()->user())->phone) }}" placeholder="+9715XXXXXXX" required>
                    <select class="lux-input" name="emirate" required>
                        <option value="">{{ __('Select emirate') }}</option>
                        @foreach($emirates as $key => $label)
                            <option value="{{ $key }}" @selected(old('emirate') === $key)>{{ __($label) }}</option>
                        @endforeach
                    </select>
                    <input class="lux-input" name="city" value="{{ old('city') }}" placeholder="{{ __('City or area') }}" required>
                    <input class="lux-input" name="street_address" value="{{ old('street_address') }}" placeholder="{{ __('Street address') }}" required>
                    <input class="lux-input" name="building" value="{{ old('building') }}" placeholder="{{ __('Building or villa number') }}" required>
                    <input class="lux-input" name="apartment" value="{{ old('apartment') }}" placeholder="{{ __('Apartment number, optional') }}">
                    <input class="lux-input md:col-span-2" name="company_trn" value="{{ old('company_trn') }}" placeholder="{{ __('Company TRN for invoice, optional') }}">
                    <textarea class="lux-input md:col-span-2" name="delivery_notes" rows="3" placeholder="{{ __('Delivery notes') }}">{{ old('delivery_notes') }}</textarea>
                </div>
                @auth
                    <label class="mt-4 flex items-center gap-2 text-sm"><input type="checkbox" name="save_address" value="1"> {{ __('Save this address') }}</label>
                @endauth
            </section>

            <section class="lux-card p-6">
                <h2 class="font-serif text-2xl">{{ __('Delivery & Payment') }}</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <select class="lux-input" name="delivery_slot_id">
                        <option value="">{{ __('Standard delivery') }}</option>
                        @foreach($deliverySlots as $slot)
                            <option value="{{ $slot->id }}">{{ $slot->localized('label') }} · {{ substr($slot->starts_at, 0, 5) }}-{{ substr($slot->ends_at, 0, 5) }}</option>
                        @endforeach
                    </select>
                    <select class="lux-input" name="payment_method" required>
                        <option value="card">{{ __('Online card payment') }}</option>
                        <option value="cod">{{ __('Cash on delivery') }}</option>
                    </select>
                </div>
                <p class="muted-copy mt-4">{{ __('Card payments use the configured gateway service layer. Apple Pay and Google Pay are enabled through the gateway dashboard when available.') }}</p>
            </section>
        </div>

        <aside class="h-fit border border-stone-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
            <h2 class="font-serif text-2xl">{{ __('Invoice Preview') }}</h2>
            <div class="mt-5 space-y-3 text-sm">
                @foreach($cart->items as $item)
                    <div class="flex justify-between gap-4">
                        <span>{{ $item->quantity }} x {{ $item->product->localized('name') }}</span>
                        <span>{{ $money->format((float) $item->unit_price * $item->quantity) }}</span>
                    </div>
                @endforeach
                <div class="border-t border-stone-200 pt-3 dark:border-neutral-800"></div>
                <div class="flex justify-between"><span>{{ __('Subtotal') }}</span><span>{{ $money->format($cart->subtotal) }}</span></div>
                <div class="flex justify-between"><span>{{ __('UAE VAT 5%') }}</span><span>{{ $money->format($cart->vat_total) }}</span></div>
                <div class="flex justify-between"><span>{{ __('Shipping') }}</span><span>{{ $money->format($cart->shipping_fee) }}</span></div>
                <div class="flex justify-between"><span>{{ __('Discount') }}</span><span>-{{ $money->format($cart->discount_total) }}</span></div>
                <div class="border-t border-stone-200 pt-3 text-base font-semibold dark:border-neutral-800">
                    <div class="flex justify-between"><span>{{ __('Total') }}</span><span>{{ $money->format($cart->total) }}</span></div>
                </div>
            </div>
            <button class="btn-primary mt-6 w-full" type="submit">{{ __('Place Order') }}</button>
            <p class="mt-4 text-xs leading-5 text-neutral-500">{{ __('By placing your order, you agree to the terms, return policy, and UAE VAT invoice details shown here.') }}</p>
        </aside>
    </form>
</section>
@endsection
