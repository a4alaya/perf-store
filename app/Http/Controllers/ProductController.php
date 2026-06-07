<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, ?string $locale = null): View
    {
        $products = Product::active()
            ->with('brand', 'category', 'variants')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = '%'.$request->string('q')->toString().'%';
                $query->where(function ($nested) use ($term): void {
                    $nested->where('sku', 'like', $term)
                        ->orWhere('name', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('top_notes', 'like', $term)
                        ->orWhere('middle_notes', 'like', $term)
                        ->orWhere('base_notes', 'like', $term);
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->whereHas('category', fn ($category) => $category->where('slug', $request->category)))
            ->when($request->filled('brand'), fn ($query) => $query->whereHas('brand', fn ($brand) => $brand->where('slug', $request->brand)))
            ->when($request->filled('gender'), fn ($query) => $query->where('gender', $request->gender))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->type))
            ->when($request->filled('min_price'), fn ($query) => $query->where('price', '>=', $request->float('min_price')))
            ->when($request->filled('max_price'), fn ($query) => $query->where('price', '<=', $request->float('max_price')))
            ->when($request->filled('notes'), fn ($query) => $query->where('top_notes', 'like', '%'.$request->notes.'%')->orWhere('middle_notes', 'like', '%'.$request->notes.'%')->orWhere('base_notes', 'like', '%'.$request->notes.'%'));

        match ($request->get('sort')) {
            'price_low' => $products->orderBy('price'),
            'price_high' => $products->orderByDesc('price'),
            'rating' => $products->orderByDesc('average_rating'),
            'best_selling' => $products->orderByDesc('is_best_seller')->orderByDesc('reviews_count'),
            default => $products->latest('published_at'),
        };

        return view('storefront.products.index', [
            'products' => $products->paginate(12)->withQueryString(),
            'categories' => Category::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'brands' => Brand::query()->where('is_active', true)->orderBy('name')->get(),
            'metaTitle' => __('Shop Perfumes in UAE'),
        ]);
    }

    public function category(string $locale, Category $category): View
    {
        request()->merge(['category' => $category->slug]);

        return $this->index(request());
    }

    public function brand(string $locale, Brand $brand): View
    {
        request()->merge(['brand' => $brand->slug]);

        return $this->index(request());
    }

    public function show(string $locale, Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load('brand', 'category', 'images', 'variants', 'relatedProducts.brand', 'reviews.user');

        return view('storefront.products.show', [
            'product' => $product,
            'related' => $product->relatedProducts->take(4),
            'metaTitle' => $product->localized('meta_title') ?: $product->localized('name'),
            'metaDescription' => $product->localized('meta_description') ?: $product->localized('short_description'),
        ]);
    }

    public function review(ReviewRequest $request, string $locale, Product $product): RedirectResponse
    {
        $verified = $request->user()->orders()
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
            ->exists();

        $title = $request->string('title')->trim()->toString();
        $body = $request->string('body')->trim()->toString();

        $product->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $request->integer('rating'),
            'title' => $title !== '' ? ['en' => $title, 'ar' => $title] : null,
            'body' => ['en' => $body, 'ar' => $body],
            'is_verified_purchase' => $verified,
            'status' => 'pending',
        ]);

        return back()->with('status', __('Your review was submitted for approval.'));
    }
}
