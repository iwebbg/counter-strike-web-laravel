<?php

use App\Http\Middleware\ApplyTheme;
use App\Http\Middleware\EnsureAdministrator;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ShareSiteSettings;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['admin' => EnsureAdministrator::class]);
        $middleware->web(append: [SetLocale::class, ApplyTheme::class, ShareSiteSettings::class]);
        $middleware->redirectGuestsTo(fn (): string => route('admin.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {})
    ->create();
