<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\StoreSection;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

class StorefrontController extends Controller
{
    public function home(): View
    {
        $sections = StoreSection::query()
            ->active()
            ->ordered()
            ->with(['activeItems'])
            ->get();

        return view('storefront.home', [
            'sections' => $sections,
            'banners' => Banner::query()->where('location', 'home')->where('is_active', true)->orderBy('sort_order')->get(),
            'sectionProducts' => $sections
                ->whereIn('type', ['product_rail', 'split_product_feature'])
                ->mapWithKeys(fn (StoreSection $section) => [$section->id => $this->productsForSection($section)]),
            'sectionTaxonomies' => $sections
                ->where('type', 'taxonomy_grid')
                ->mapWithKeys(fn (StoreSection $section) => [$section->id => $this->taxonomiesForSection($section)]),
            'categories' => Category::query()->where('is_active', true)->orderBy('sort_order')->take(8)->get(),
            'brands' => Brand::query()->where('is_active', true)->withCount('products')->orderBy('name')->take(10)->get(),
            'reviews' => Review::query()->where('status', 'approved')->with('product')->latest()->take(6)->get(),
            'metaTitle' => __('Maison De Mystere Perfumes UAE'),
            'metaDescription' => __('Luxury niche perfumes, oud, bakhoor, attars, and curated gift sets delivered across the UAE.'),
        ]);
    }

    public function contact(): View
    {
        return view('storefront.contact', [
            'metaTitle' => __('Contact Maison De Mystere'),
        ]);
    }

    private function productsForSection(StoreSection $section): Collection
    {
        $limit = max(1, (int) $section->limit);

        return match ($section->product_source) {
            'best_sellers' => $this->productRail('is_best_seller', $limit),
            'new_arrivals' => $this->productRail('is_new_arrival', $limit),
            'uae_exclusive' => $this->productRail('is_uae_exclusive', $limit),
            'oud' => Product::active()->where('type', 'oud')->with('brand', 'category', 'variants')->latest('published_at')->take($limit)->get(),
            'gift_sets' => Product::active()->where('type', 'gift_set')->with('brand', 'category', 'variants')->latest('published_at')->take($limit)->get(),
            default => $this->productRail('is_featured', $limit),
        };
    }

    private function taxonomiesForSection(StoreSection $section): Collection
    {
        $limit = max(1, (int) $section->limit);

        if ($section->taxonomy_source === 'brands') {
            return Brand::query()
                ->where('is_active', true)
                ->withCount('products')
                ->orderBy('name')
                ->take($limit)
                ->get();
        }

        return Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take($limit)
            ->get();
    }

    private function productRail(string $flag, int $limit = 8): Collection
    {
        return Product::active()
            ->where($flag, true)
            ->with('brand', 'category', 'variants')
            ->latest('published_at')
            ->take($limit)
            ->get();
    }
}
