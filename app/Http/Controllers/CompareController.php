<?php

namespace App\Http\Controllers;

use App\Models\CompareItem;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index(Request $request): View
    {
        $items = CompareItem::query()
            ->with('product.brand', 'product.category')
            ->when(auth()->check(), fn ($query) => $query->where('user_id', auth()->id()))
            ->when(! auth()->check(), fn ($query) => $query->where('session_id', $request->session()->getId()))
            ->latest()
            ->take(4)
            ->get();

        return view('storefront.compare.index', [
            'items' => $items,
            'metaTitle' => __('Compare Perfumes'),
        ]);
    }

    public function toggle(Request $request, string $locale, Product $product): RedirectResponse
    {
        $attributes = auth()->check()
            ? ['user_id' => auth()->id(), 'product_id' => $product->id]
            : ['session_id' => $request->session()->getId(), 'product_id' => $product->id];

        $item = CompareItem::query()->where($attributes)->first();

        $item ? $item->delete() : CompareItem::create($attributes);

        return back()->with('status', __('Compare list updated.'));
    }
}
