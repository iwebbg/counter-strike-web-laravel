<?php

namespace Tests\Unit;

use App\GameServers\Queries\SourceEnginePacketParser;
use PHPUnit\Framework\TestCase;

/** Verifies binary Valve response parsing without network access. */
class SourceEnginePacketParserTest extends TestCase
{
    /** Parses players and orders them by score descending. */
    public function test_player_packet_is_sorted_by_score(): void
    {
        $packet = "\xFF\xFF\xFF\xFFD\x03";
        $packet .= $this->player(0, 'Alpha', 12, 180.4);
        $packet .= $this->player(1, 'Bravo', 31, 92.2);
        $packet .= $this->player(2, 'Charlie', 18, 360.8);

        $players = (new SourceEnginePacketParser)->parsePlayers($packet);

        self::assertSame(['Bravo', 'Charlie', 'Alpha'], array_column($players, 'name'));
        self::assertSame([31, 18, 12], array_column($players, 'score'));
        self::assertSame(92, $players[0]['duration_seconds']);
    }

    /** Builds one A2S_PLAYER record. */
    private function player(int $index, string $name, int $score, float $duration): string
    {
        return chr($index).$name."\0".pack('V', $score).pack('g', $duration);
    }
}
