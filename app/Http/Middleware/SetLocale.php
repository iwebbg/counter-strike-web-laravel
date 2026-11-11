<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/** Applies the visitor's preferred locale to every web request. */
class SetLocale
{
    /** Handle an incoming request. */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', config('app.locale'));

        if (! array_key_exists($locale, config('localization.locales'))) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
