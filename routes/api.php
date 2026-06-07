<?php

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/products', function (Request $request) {
    return Product::active()
        ->with('brand:id,name,slug', 'category:id,name,slug')
        ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->q.'%'))
        ->paginate(20);
})->name('api.products.index');

Route::get('/orders/{order:order_number}', function (Request $request, \App\Models\Order $order) {
    abort_unless($request->query('email') === $order->customer_email, 403);

    return $order->load('items', 'payment');
})->name('api.orders.show');
