<?php

use App\GameServers\Contracts\GameServerQuery;
use App\Models\GameServer;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

Artisan::command('admin:create', function (): int {
    $data = [
        'name' => trim((string) $this->ask('Administrator name')),
        'email' => trim((string) $this->ask('Email address')),
        'password' => (string) $this->secret('Password'),
        'password_confirmation' => (string) $this->secret('Confirm password'),
    ];
    $validator = Validator::make($data, [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
    ]);

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        return self::FAILURE;
    }

    User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => $data['password'],
        'is_admin' => true,
    ]);
    $this->info('Administrator created.');

    return self::SUCCESS;
})->purpose('Create a website administrator interactively');

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
