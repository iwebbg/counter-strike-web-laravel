<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Records one server telemetry sample. */
class ServerSnapshot extends Model
{
    public $timestamps = false;

    protected $fillable = ['game_server_id', 'is_online', 'map', 'players', 'max_players', 'ping_ms', 'player_data', 'queried_at'];

    /** Defines model casts. */
    protected function casts(): array
    {
        return ['is_online' => 'boolean', 'player_data' => 'array', 'queried_at' => 'datetime'];
    }
}
