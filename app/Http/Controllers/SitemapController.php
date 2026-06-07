<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function xml(): Response
    {
        $urls = collect(config('store.locales'))->flatMap(function (string $locale) {
            return collect([
                route('home', ['locale' => $locale]),
                route('products.index', ['locale' => $locale]),
            ])
                ->merge(Product::active()->pluck('slug')->map(fn ($slug) => route('products.show', ['locale' => $locale, 'product' => $slug])))
                ->merge(Category::where('is_active', true)->pluck('slug')->map(fn ($slug) => route('categories.show', ['locale' => $locale, 'category' => $slug])))
                ->merge(Brand::where('is_active', true)->pluck('slug')->map(fn ($slug) => route('brands.show', ['locale' => $locale, 'brand' => $slug])))
                ->merge(Page::where('is_active', true)->pluck('slug')->map(fn ($slug) => route('pages.show', ['locale' => $locale, 'page' => $slug])));
        });

        return response()
            ->view('storefront.sitemap', ['urls' => $urls], 200)
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        return response("User-agent: *\nAllow: /\nSitemap: ".url('/sitemap.xml')."\n", 200)
            ->header('Content-Type', 'text/plain');
    }
}
