<?php

namespace App\GameServers\Queries;

use App\GameServers\Contracts\GameServerQuery;
use App\GameServers\Data\ServerQueryResult;
use App\Models\GameServer;
use RuntimeException;
use Throwable;

/** Queries Valve GoldSrc and Source servers through A2S_INFO and A2S_PLAYER. */
class SourceEngineQuery implements GameServerQuery
{
    /** Creates the query adapter with its packet parser. */
    public function __construct(private readonly SourceEnginePacketParser $parser) {}

    /** Queries one server and returns a safe unavailable result on network failure. */
    public function query(GameServer $server): ServerQueryResult
    {
        $startedAt = hrtime(true);
        $socket = null;

        try {
            $socket = stream_socket_client('udp://'.$server->host.':'.$server->port, $errorCode, $errorMessage, (float) env('GAME_QUERY_TIMEOUT_SECONDS', 1));

            if ($socket === false) {
                throw new RuntimeException($errorMessage ?: 'Unable to open UDP socket.', $errorCode);
            }

            stream_set_timeout($socket, 1);
            fwrite($socket, "\xFF\xFF\xFF\xFFTSource Engine Query\x00");
            $info = $this->parser->parseInfo($this->receivePacket($socket, $server->protocol), (int) round((hrtime(true) - $startedAt) / 1_000_000));

            try {
                $playerData = $this->queryPlayers($socket, $server->protocol);
            } catch (Throwable) {
                $playerData = [];
            }

            return new ServerQueryResult(true, $info->name, $info->map, $info->players, $info->maxPlayers, $info->pingMilliseconds, $playerData);
        } catch (Throwable $exception) {
            return new ServerQueryResult(false, error: $exception->getMessage());
        } finally {
            if (is_resource($socket)) {
                fclose($socket);
            }
        }
    }

    /** Completes the A2S_PLAYER challenge exchange and parses its response. */
    private function queryPlayers(mixed $socket, string $protocol): array
    {
        fwrite($socket, "\xFF\xFF\xFF\xFFU\xFF\xFF\xFF\xFF");
        $response = $this->receivePacket($socket, $protocol);

        if (ord($response[4] ?? "\0") === 0x41) {
            $challenge = substr($response, 5, 4);

            if (strlen($challenge) !== 4) {
                throw new RuntimeException('Invalid A2S player challenge.');
            }

            fwrite($socket, "\xFF\xFF\xFF\xFFU".$challenge);
            $response = $this->receivePacket($socket, $protocol);
        }

        return $this->parser->parsePlayers($response);
    }

    /** Reads a bounded single or split A2S response from the UDP socket. */
    private function receivePacket(mixed $socket, string $protocol): string
    {
        $maxBytes = (int) env('GAME_QUERY_MAX_PACKET_BYTES', 65535);
        $firstPacket = stream_socket_recvfrom($socket, $maxBytes);

        if (! is_string($firstPacket) || strlen($firstPacket) < 5) {
            throw new RuntimeException('Empty A2S response.');
        }

        if (substr($firstPacket, 0, 4) === "\xFF\xFF\xFF\xFF") {
            return $firstPacket;
        }

        if (substr($firstPacket, 0, 4) !== "\xFE\xFF\xFF\xFF") {
            throw new RuntimeException('Invalid A2S response header.');
        }

        return $this->assembleSplitPacket($socket, $firstPacket, $protocol, $maxBytes);
    }

    /** Reassembles bounded Source or GoldSrc split response datagrams. */
    private function assembleSplitPacket(mixed $socket, string $firstPacket, string $protocol, int $maxBytes): string
    {
        $requestId = substr($firstPacket, 4, 4);
        $parts = [];

        $extractPart = function (string $packet) use ($protocol, $requestId): array {
            if (substr($packet, 0, 4) !== "\xFE\xFF\xFF\xFF" || substr($packet, 4, 4) !== $requestId) {
                throw new RuntimeException('Mismatched A2S split packet.');
            }

            if ($protocol === 'goldsrc') {
                $descriptor = ord($packet[8] ?? "\0");

                return [$descriptor & 0x0F, $descriptor >> 4, substr($packet, 9)];
            }

            return [ord($packet[9] ?? "\0"), ord($packet[8] ?? "\0"), substr($packet, 12)];
        };

        [$number, $total, $payload] = $extractPart($firstPacket);

        if ($total < 1 || $total > 32) {
            throw new RuntimeException('Invalid A2S split packet count.');
        }

        $parts[$number] = $payload;

        while (count($parts) < $total) {
            $packet = stream_socket_recvfrom($socket, $maxBytes);

            if (! is_string($packet)) {
                throw new RuntimeException('Incomplete A2S split response.');
            }

            [$number, $packetTotal, $payload] = $extractPart($packet);

            if ($packetTotal !== $total) {
                throw new RuntimeException('Inconsistent A2S split packet count.');
            }

            $parts[$number] = $payload;
        }

        ksort($parts);
        $response = implode('', $parts);

        if (strlen($response) > $maxBytes) {
            throw new RuntimeException('A2S response exceeds the configured limit.');
        }

        return $response;
    }
}
