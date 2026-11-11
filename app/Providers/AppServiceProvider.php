<?php

namespace App\Providers;

use App\GameServers\Contracts\GameServerQuery;
use App\GameServers\Queries\SourceEngineQuery;
use Illuminate\Support\ServiceProvider;

/** Registers application services. */
class AppServiceProvider extends ServiceProvider
{
    /** Registers container bindings. */
    public function register(): void
    {
        $this->app->bind(GameServerQuery::class, SourceEngineQuery::class);
    }

    /** Boots application services. */
    public function boot(): void {}
}
