<?php

namespace App\Providers;

use App\GameServers\Contracts\GameServerQuery;
use App\GameServers\Queries\SourceEngineQuery;
use App\Themes\ThemeManager;
use Illuminate\Support\ServiceProvider;

/** Registers application services. */
class AppServiceProvider extends ServiceProvider
{
    /** Registers container bindings. */
    public function register(): void
    {
        $this->app->bind(GameServerQuery::class, SourceEngineQuery::class);
        $this->app->singleton(ThemeManager::class, fn (): ThemeManager => new ThemeManager(
            config('themes.path'),
            config('themes.default'),
            config('themes.labels', []),
        ));
    }

    /** Boots application services. */
    public function boot(): void {}
}
