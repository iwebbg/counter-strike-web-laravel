<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/** Shares the singleton branding and SEO configuration with Blade views. */
class ShareSiteSettings
{
    /** Handle an incoming request. */
    public function handle(Request $request, Closure $next): Response
    {
        View::share('siteSettings', SiteSetting::query()->firstOrFail());

        return $next($request);
    }
}
