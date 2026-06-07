@extends('layouts.storefront')

@section('content')
@php($money = app(\App\Services\MoneyFormatter::class))
<section class="container-lux py-12">
    <h1 class="section-title">{{ __('Compare Perfumes') }}</h1>
    <div class="mt-8 overflow-x-auto border border-stone-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <table class="w-full min-w-[760px] text-sm">
            <thead>
                <tr class="border-b border-stone-200 text-left dark:border-neutral-800">
                    <th class="p-4">{{ __('Perfume') }}</th>
                    @foreach($items as $item)
                        <th class="p-4">{{ $item->product->localized('name') }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 dark:divide-neutral-800">
                @foreach([__('Brand') => 'brand', __('Type') => 'type', __('Gender') => 'gender', __('Price') => 'price', __('Rating') => 'average_rating'] as $label => $field)
                    <tr>
                        <td class="p-4 font-semibold">{{ $label }}</td>
                        @foreach($items as $item)
                            <td class="p-4">
                                @if($field === 'brand')
                                    {{ $item->product->brand->localized('name') }}
                                @elseif($field === 'price')
                                    {{ $money->format($item->product->currentPrice()) }}
                                @else
                                    {{ $item->product->{$field} }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($items->isEmpty())
            <div class="p-10 text-center">{{ __('Add up to four perfumes to compare fragrance families, prices, and notes.') }}</div>
        @endif
    </div>
</section>
@endsection
