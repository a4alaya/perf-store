@extends('layouts.storefront')

@section('content')
<section class="container-lux grid gap-10 py-16 lg:grid-cols-[0.9fr_1.1fr]">
    <div>
        <h1 class="section-title">{{ __('Contact Maison De Mystere') }}</h1>
        <p class="muted-copy mt-4">{{ __('Our team is ready to help with fragrance recommendations, UAE delivery questions, corporate gifting, and order care.') }}</p>
        <div class="mt-8 space-y-4 text-sm">
            <p><strong>{{ __('Email') }}:</strong> <bdi dir="ltr">{{ config('store.support_email') }}</bdi></p>
            <p><strong>{{ __('Phone') }}:</strong> <bdi dir="ltr">{{ config('store.support_phone') }}</bdi></p>
            <p><strong>{{ __('Currency') }}:</strong> <bdi dir="ltr">AED</bdi></p>
        </div>
    </div>
    <form class="lux-card grid gap-4 p-6">
        <input class="lux-input" placeholder="{{ __('Full name') }}">
        <input class="lux-input" type="email" placeholder="{{ __('Email address') }}">
        <input class="lux-input" placeholder="{{ __('UAE phone number') }}">
        <textarea class="lux-input" rows="6" placeholder="{{ __('How can we help?') }}"></textarea>
        <button class="btn-primary" type="button">{{ __('Send Message') }}</button>
    </form>
</section>
@endsection
