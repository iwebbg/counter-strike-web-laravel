<?php

namespace App\GameServers\Queries;

use App\GameServers\Contracts\GameServerQuery;
use App\GameServers\Data\ServerQueryResult;
use App\Models\GameServer;
use RuntimeException;
use Throwable;

/** Queries Valve GoldSrc and Source servers through A2S_INFO. */
class SourceEngineQuery implements GameServerQuery
{
    /** Queries one server and returns a safe unavailable result on network failure. */
    public function query(GameServer $server): ServerQueryResult
    {
        $startedAt = hrtime(true);

        try {
            $socket = stream_socket_client('udp://'.$server->host.':'.$server->port, $errorCode, $errorMessage, (float) env('GAME_QUERY_TIMEOUT_SECONDS', 1));
            if ($socket === false) {
                throw new RuntimeException($errorMessage ?: 'Unable to open UDP socket.', $errorCode);
            }

            stream_set_timeout($socket, 1);
            fwrite($socket, "\xFF\xFF\xFF\xFFTSource Engine Query\x00");
            $packet = fread($socket, (int) env('GAME_QUERY_MAX_PACKET_BYTES', 65535));
            fclose($socket);

            if ($packet === false || strlen($packet) < 8 || substr($packet, 0, 4) !== "\xFF\xFF\xFF\xFF") {
                throw new RuntimeException('Invalid or empty A2S response.');
            }

            return $this->parseInfoPacket($packet, (int) round((hrtime(true) - $startedAt) / 1_000_000));
        } catch (Throwable $exception) {
            return new ServerQueryResult(false, error: $exception->getMessage());
        }
    }

    /** Parses Source (0x49) and legacy GoldSrc (0x6D) info replies. */
    private function parseInfoPacket(string $packet, int $pingMilliseconds): ServerQueryResult
    {
        $offset = 4;
        $type = ord($packet[$offset++]);

        if ($type === 0x49) {
            $offset++;
            $name = $this->readString($packet, $offset);
            $map = $this->readString($packet, $offset);
            $this->readString($packet, $offset);
            $this->readString($packet, $offset);
            $offset += 2;
            $players = ord($packet[$offset] ?? "\0");
            $maxPlayers = ord($packet[$offset + 1] ?? "\0");

            return new ServerQueryResult(true, $name, $map, $players, $maxPlayers, $pingMilliseconds);
        }

        if ($type === 0x6D) {
            $this->readString($packet, $offset);
            $name = $this->readString($packet, $offset);
            $map = $this->readString($packet, $offset);
            $this->readString($packet, $offset);
            $this->readString($packet, $offset);
            $players = ord($packet[$offset] ?? "\0");
            $maxPlayers = ord($packet[$offset + 1] ?? "\0");

            return new ServerQueryResult(true, $name, $map, $players, $maxPlayers, $pingMilliseconds);
        }

        throw new RuntimeException('Unsupported A2S packet type.');
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
