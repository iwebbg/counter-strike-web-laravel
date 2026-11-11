<?php

namespace App\GameServers\Contracts;

use App\GameServers\Data\ServerQueryResult;
use App\Models\GameServer;

/** Defines live telemetry retrieval for a supported game server. */
interface GameServerQuery
{
    /** Queries one configured server. */
    public function query(GameServer $server): ServerQueryResult;
}
