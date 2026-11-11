<?php

namespace App\GameServers\Queries;

use App\GameServers\Data\ServerQueryResult;
use RuntimeException;

/** Parses trusted-size Valve A2S response payloads into application data. */
class SourceEnginePacketParser
{
    /** Parses Source (0x49) and legacy GoldSrc (0x6D) info replies. */
    public function parseInfo(string $packet, int $pingMilliseconds): ServerQueryResult
    {
        $offset = $this->payloadOffset($packet);
        $type = ord($packet[$offset++] ?? "\0");

        if ($type === 0x49) {
            $offset++;
            $name = $this->readString($packet, $offset);
            $map = $this->readString($packet, $offset);
            $this->readString($packet, $offset);
            $this->readString($packet, $offset);
            $offset += 2;

            return new ServerQueryResult(true, $name, $map, ord($packet[$offset] ?? "\0"), ord($packet[$offset + 1] ?? "\0"), $pingMilliseconds);
        }

        if ($type === 0x6D) {
            $this->readString($packet, $offset);
            $name = $this->readString($packet, $offset);
            $map = $this->readString($packet, $offset);
            $this->readString($packet, $offset);
            $this->readString($packet, $offset);

            return new ServerQueryResult(true, $name, $map, ord($packet[$offset] ?? "\0"), ord($packet[$offset + 1] ?? "\0"), $pingMilliseconds);
        }

        throw new RuntimeException('Unsupported A2S info packet type.');
    }

    /**
     * Parses and ranks an A2S_PLAYER response by score and connected time.
     *
     * @return list<array{name: string, score: int, duration_seconds: int}>
     */
    public function parsePlayers(string $packet): array
    {
        $offset = $this->payloadOffset($packet);

        if (ord($packet[$offset++] ?? "\0") !== 0x44) {
            throw new RuntimeException('Unsupported A2S player packet type.');
        }

        $playerCount = ord($packet[$offset++] ?? "\0");
        $players = [];

        for ($position = 0; $position < $playerCount; $position++) {
            if ($offset >= strlen($packet)) {
                throw new RuntimeException('Truncated A2S player packet.');
            }

            $offset++;
            $name = $this->readString($packet, $offset);

            if ($offset + 8 > strlen($packet)) {
                throw new RuntimeException('Truncated A2S player metrics.');
            }

            $unsignedScore = unpack('V', substr($packet, $offset, 4))[1];
            $score = $unsignedScore >= 0x80000000
                ? $unsignedScore - 0x100000000
                : $unsignedScore;
            $offset += 4;
            $duration = unpack('g', substr($packet, $offset, 4))[1];
            $offset += 4;

            if ($name !== '') {
                $players[] = ['name' => mb_convert_encoding($name, 'UTF-8', 'UTF-8, Windows-1251, ISO-8859-1'), 'score' => $score, 'duration_seconds' => max(0, (int) round($duration))];
            }
        }

        usort($players, fn (array $left, array $right): int => [$right['score'], $right['duration_seconds']] <=> [$left['score'], $left['duration_seconds']]);

        return $players;
    }

    /** Validates the single-packet response header and returns its payload cursor. */
    private function payloadOffset(string $packet): int
    {
        if (strlen($packet) < 6 || substr($packet, 0, 4) !== "\xFF\xFF\xFF\xFF") {
            throw new RuntimeException('Invalid or empty A2S response.');
        }

        return 4;
    }

    /** Reads a null-terminated protocol string and advances the cursor. */
    private function readString(string $packet, int &$offset): string
    {
        $end = strpos($packet, "\0", $offset);

        if ($end === false) {
            throw new RuntimeException('Malformed A2S string.');
        }

        $value = substr($packet, $offset, $end - $offset);
        $offset = $end + 1;

        return $value;
    }
}
