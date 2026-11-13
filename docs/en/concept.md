# Product concept

[Български](../concept.md) | [English](concept.md)

## Vision

FragZone is a fast, welcoming home for Counter-Strike communities. Visitors can discover a server, verify its current map and population, inspect recent activity and connect without digging through Discord messages or raw IP lists. Operators can maintain all content from a focused administration area.

## Audience and primary journeys

- Players browse by game and category, compare population and latency, open a server detail page, then copy the address or launch Steam Connect.
- Returning players check current maps, top players, recent activity and community news.
- Administrators add servers and categories, publish news and monitor stale/offline query results.

## Information architecture

- Home: live network summary, featured servers, category shortcuts, latest news.
- Servers: filters for CS 1.6 / CS:GO, category, online status and search.
- Server detail: address, status, map, players, capacity, rules and recent snapshots.
- Categories: competitive, public, deathmatch, zombie, surf, jailbreak and custom groups.
- Rankings: aggregated player snapshots, play time and score when the protocol exposes them.
- News: announcements, updates, events and guides.
- Administration: dashboard, server CRUD, category CRUD, news CRUD and manual refresh.

## Visual direction

The interface uses a dark graphite field, warm tactical amber as the action color, cool cyan for live telemetry and restrained grid/noise details. Typography is compact and editorial rather than a generic gaming neon theme. Interaction is server-rendered Blade with small progressive JavaScript enhancements, responsive layouts, visible focus states and reduced-motion support.

## MVP acceptance criteria

- A visitor can browse, filter and open seeded servers without signing in.
- Live status degrades gracefully to the last snapshot or an unavailable state.
- An authenticated administrator can create, edit and remove a server.
- Scheduled refreshes do not block page requests.
- Local, production TLS and external reverse-proxy Docker modes remain available.
