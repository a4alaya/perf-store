<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale') ?: config('store.default_locale', 'en');

        if (! in_array($locale, config('store.locales', ['en']), true)) {
            abort(404);
        }

        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);
        View::share('isRtl', $locale === 'ar');

        return $next($request);
    }
}
