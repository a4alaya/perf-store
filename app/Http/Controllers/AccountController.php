<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class AccountController extends Controller
{
    public function dashboard(): View
    {
        return view('storefront.account.dashboard', [
            'orders' => auth()->user()->orders()->latest()->take(8)->get(),
            'addresses' => auth()->user()->addresses()->latest()->get(),
            'wishlist' => auth()->user()->wishlists()->with('product.brand')->latest()->get(),
            'metaTitle' => __('My Account'),
        ]);
    }
}
