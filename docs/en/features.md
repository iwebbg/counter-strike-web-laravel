# Feature catalogue

[Български](../features.md) | [English](features.md)

This document describes the functionality currently implemented in FragZone.

## Public portal

- Responsive Laravel Blade interface with a dark Counter-Strike visual system.
- Bulgarian and English interfaces with a configurable fallback language.
- Language selector designed to support additional configured locales.
- Home page with live population, capacity and game-mode summaries.
- Featured-server cards, category shortcuts and latest published news.
- Server directory with text search, game filter and category filter.
- Server details with connection address, Steam Connect link, map, population, ping and refresh time.
- Player list ordered by kills/score and connected time when `A2S_PLAYER` data is available.
- Public localized news index and article pages.
- Admin login intentionally omitted from public navigation.

## Game-server telemetry

- Native Valve `A2S_INFO` and `A2S_PLAYER` UDP queries.
- GoldSrc support for Counter-Strike 1.6 and Source support for CS:GO-compatible servers.
- Challenge-response handling and bounded Source/GoldSrc split-packet reassembly.
- Configurable short query timeout with a graceful offline/error state.
- Persisted current status and historical server snapshots.
- Automatic refresh every minute through Laravel Scheduler.
- Manual refresh with `php artisan servers:refresh` or a specific server ID.
- Public host names and Internet IP addresses; servers do not need to run beside the website.

Valve `A2S_PLAYER` does not include CT/T membership. Team separation requires a game-server plugin or a separate API that publishes that information.

## Administration

- Separate rate-limited administrator login protected by sessions and CSRF.
- Dashboard totals for servers, online servers, players and news.
- Server create, edit and delete workflows with automatic slugs.
- CS 1.6/CS:GO and GoldSrc/Source selection, featured/published controls and localized descriptions.
- Category create, edit and delete with localized content and accent colors.
- Protected category deletion while servers still use the category.
- Localized news create, edit, publish and delete workflows.
- Rich-text editor with server-side HTML sanitization.
- Branding and localized SEO settings: identity, logo subtitle, metadata, keywords, homepage copy and footer.
- Reusable language tabs that open the locale containing a validation error.
- Interactive `admin:create` command for first-administrator provisioning.
- Directory-based Blade themes with a complete physical FragZone fallback and administration selection.

## Localization and operations

- Bulgarian and English UI dictionaries with session-persisted language choice.
- JSON-backed localized server, category, news and SEO content with fallback.
- Local Docker stack: PHP-FPM, Nginx, MariaDB, Redis and scheduler.
- Production stack: immutable images, queue, scheduler, persistent volumes and automatic Let's Encrypt.
- External reverse-proxy mode and Apache/cPanel `public/.htaccess` support.
- Password hashing, CSRF, admin middleware, login throttling and Nginx security headers.
- Isolated SQLite in-memory PHPUnit suite.

## Current boundaries

- Team membership is unavailable through the standard Valve player protocol.
- There is no public player account system; only website administrators authenticate.
- Rankings are snapshot-based and depend on servers allowing `A2S_PLAYER`.
- The portal does not execute commands or mutations against Counter-Strike servers.
- LGSL is not bundled; the native adapter avoids its GPL runtime and legacy PHPUnit dependency.
