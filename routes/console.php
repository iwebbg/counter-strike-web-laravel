<?php

use App\GameServers\Contracts\GameServerQuery;
use App\Models\GameServer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('servers:refresh {server?}', function (GameServerQuery $query): int {
    $servers = GameServer::published()->when($this->argument('server'), fn ($builder, $id) => $builder->whereKey($id))->get();
    foreach ($servers as $server) {
        $result = $query->query($server);
        DB::transaction(function () use ($server, $result): void {
            $server->update(['is_online' => $result->online, 'current_map' => $result->map ?? $server->current_map, 'current_players' => $result->players, 'max_players' => $result->maxPlayers ?: $server->max_players, 'last_ping_ms' => $result->pingMilliseconds, 'last_queried_at' => now(), 'last_query_error' => $result->error]);
            $server->snapshots()->create(['is_online' => $result->online, 'map' => $result->map, 'players' => $result->players, 'max_players' => $result->maxPlayers, 'ping_ms' => $result->pingMilliseconds, 'player_data' => $result->playerData, 'queried_at' => now()]);
        });
        $this->line(($result->online ? 'ONLINE ' : 'OFFLINE ').$server->address());
    }

    return self::SUCCESS;
})->purpose('Refresh Counter-Strike server telemetry');

Schedule::command('servers:refresh')->everyMinute()->withoutOverlapping();
