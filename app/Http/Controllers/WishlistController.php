<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class WishlistController extends Controller
{
    public function index(): View
    {
        return view('storefront.wishlist.index', [
            'items' => auth()->user()->wishlists()->with('product.brand')->latest()->get(),
            'metaTitle' => __('Wishlist'),
        ]);
    }

    public function toggle(string $locale, Product $product): RedirectResponse
    {
        $wishlist = auth()->user()->wishlists()->where('product_id', $product->id)->first();

        $wishlist ? $wishlist->delete() : auth()->user()->wishlists()->create(['product_id' => $product->id]);

        return back()->with('status', __('Wishlist updated.'));
    }
}
