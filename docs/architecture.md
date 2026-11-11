# Architecture

## Stack

- PHP 8.5 and Laravel 13
- Blade templates with hand-authored CSS and small vanilla JavaScript enhancements
- MariaDB 11.8 for application records
- Redis 8 for cache, queues and sessions
- Nginx/PHP-FPM containers, with local, automatic Let's Encrypt production, and external proxy modes

## Domain model

- `users`: administration identities with an `is_admin` capability flag.
- `server_categories`: editorial groupings with slugs and accent colors.
- `game_servers`: connection data, game/protocol selection, presentation data and last known state.
- `server_snapshots`: time-series query results used for charts and rankings.
- `news_posts`: draft or published editorial content.

## Query boundary

`GameServerQuery` is the application contract. `SourceEngineQuery` implements Valve A2S UDP queries for Source and GoldSrc servers. Controllers never open sockets. The scheduled `servers:refresh` command queues or executes refreshes, stores a compact last-known state on each server and appends a snapshot.

LGSL 7 is treated only as a protocol reference. Its `lgsl7` branch is GPL-3.0, marked experimental/unstable, and requires an old PHPUnit release as a runtime Composer dependency. No LGSL source is imported, preserving this project's license and PHPUnit 12 compatibility. Keeping an adapter boundary prevents third-party protocol code from leaking into controllers and models.

## Operations

- Local: bind-mounted source, MariaDB and Redis, available on port 80.
- Production: immutable PHP and Nginx images, persistent application/database volumes, HTTP-to-HTTPS redirect and automatic certificate request/renewal.
- External: the application Nginx joins an existing Docker network while the host reverse proxy owns TLS and public ports.

Server queries are outbound UDP. Production firewalls must allow UDP traffic to configured game server ports. Timeouts remain short and refreshes happen outside public requests.

## Security baseline

Admin routes require session authentication and the `is_admin` middleware. Form requests validate addresses, ports and publish state. Passwords use Laravel hashing, CSRF protection is enabled, Nginx emits security headers, and secrets stay in environment files.
