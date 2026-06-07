<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function show(string $locale, Page $page): View
    {
        abort_unless($page->is_active, 404);

        return view('storefront.page', [
            'page' => $page,
            'metaTitle' => $page->localized('meta_title') ?: $page->localized('title'),
            'metaDescription' => $page->localized('meta_description'),
        ]);
    }
}
