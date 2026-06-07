@extends('layouts.storefront')

@section('content')
<section class="container-lux py-16">
    <article class="mx-auto max-w-3xl">
        <h1 class="section-title">{{ $page->localized('title') }}</h1>
        <div class="mt-8 max-w-none space-y-4 leading-8 text-neutral-700 dark:text-stone-200">
            {!! nl2br(e($page->localized('body'))) !!}
        </div>
    </article>
</section>
@endsection
