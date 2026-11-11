<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use App\Themes\ThemeManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/** Activates the configured theme and prepends its optional Blade overrides. */
class ApplyTheme
{
    public function __construct(private readonly ThemeManager $themes) {}

    /** Handle an incoming request. */
    public function handle(Request $request, Closure $next): Response
    {
        $theme = $this->themes->activate(SiteSetting::query()->value('theme_slug'));
        if (! $request->is('admin', 'admin/*')) {
            $defaultTheme = $this->themes->available()[config('themes.default')] ?? null;
            if (is_array($defaultTheme) && is_dir($defaultTheme['views_path'])) {
                View::getFinder()->prependLocation($defaultTheme['views_path']);
            }
            if ($theme['slug'] !== ($defaultTheme['slug'] ?? null) && is_dir($theme['views_path'])) {
                View::getFinder()->prependLocation($theme['views_path']);
            }
        }
        View::share('activeTheme', $theme);

        return $next($request);
    }
}
