<?php

namespace App\GameServers\Data;

/** Carries normalized telemetry returned by a game server query. */
readonly class ServerQueryResult
{
    /** Creates a normalized query result. */
    public function __construct(public bool $online, public ?string $name = null, public ?string $map = null, public int $players = 0, public int $maxPlayers = 0, public ?int $pingMilliseconds = null, public ?string $error = null) {}
}
