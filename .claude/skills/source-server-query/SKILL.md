---
name: source-server-query
description: Implement or troubleshoot Counter-Strike GoldSrc/Source server querying, snapshots, and rankings in this repository. Use for A2S, UDP, live server state, maps, players, or LGSL adapter work.
---

# Source server query work

Keep game-server networking behind `App\GameServers\Contracts\GameServerQuery`; HTTP controllers must consume persisted last-known state and must not open UDP sockets.

Support both `goldsrc` and `source` protocol selections. Treat UDP responses as untrusted binary input: apply strict packet-size limits, validate headers and terminators, handle split/compressed packets explicitly, and fail to a typed unavailable result. Keep timeouts short and never retry indefinitely.

Refresh through the `servers:refresh` command or queued jobs. Update the server's compact current state and append snapshots transactionally. A timeout must preserve useful last-known telemetry while recording failure time.

When adapting LGSL or another library, verify its current license first, isolate third-party-derived code in its own adapter, retain required notices, and add fixture-based parser tests. Do not copy the whole project for a single protocol.

Read `docs/architecture.md` before changing the query boundary and add binary fixtures only when they represent a real protocol response.
