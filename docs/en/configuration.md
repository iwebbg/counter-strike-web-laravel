# Environment configuration

[Български](../configuration.md) | [English](configuration.md)

FragZone reads runtime settings from the untracked root `.env`. The Docker wrapper reads `docker/.env` to select an infrastructure mode.

```bash
cp .env.example .env
cp docker/.env.example docker/.env
```

Never commit real secrets. After production changes, clear and rebuild configuration. For a manual/LAMP installation run:

```bash
php artisan optimize:clear
php artisan optimize
```

Inside Docker use the application-user helper, which prevents root-owned cache files:

```bash
./docker/compose artisan optimize:clear
./docker/compose artisan optimize
```

## Application

| Variable | Example | Purpose |
| --- | --- | --- |
| `APP_NAME` | `FragZone` | Internal Laravel name; public branding is managed from administration. |
| `APP_ENV` | `local` / `production` | Runtime environment. |
| `APP_KEY` | `base64:...` | Encryption/signing key; generate once and preserve it. |
| `APP_DEBUG` | `false` | Detailed exceptions; always false publicly. |
| `APP_URL` | `https://cs.example.com` | Canonical URL including scheme. |
| `APP_TIMEZONE` | `Europe/Sofia` | Publishing and scheduler timezone. |
| `APP_LOCALE` | `bg` | Default locale configured in `config/localization.php`. |
| `APP_FALLBACK_LOCALE` | `en` | Fallback for missing translations. |
| `APP_PREVIOUS_KEYS` | empty | Optional previous keys during controlled key rotation. |

Changing `APP_KEY` invalidates sessions and may make encrypted values unreadable. Back it up securely.

## MariaDB/MySQL

| Variable | Docker | Manual/cPanel | Purpose |
| --- | --- | --- | --- |
| `DB_CONNECTION` | `mariadb` | `mariadb` | Database driver. |
| `DB_HOST` | `database` | `127.0.0.1` or `localhost` | Host visible from PHP. |
| `DB_PORT` | `3306` | `3306` | Database port. |
| `DB_DATABASE` | `fragzone` | hosting database name | Application database. |
| `DB_USERNAME` | `fragzone` | restricted user | Runtime login. |
| `DB_PASSWORD` | strong secret | strong secret | Runtime password. |
| `DB_ROOT_PASSWORD` | different strong secret | unused | Docker MariaDB initialization only. |

Docker initialization values apply only when `database-data` is first created. Editing them later does not change credentials already stored in MariaDB.

## Sessions, cache, queues and Redis

| Variable | Docker production | Simple cPanel | Purpose |
| --- | --- | --- | --- |
| `SESSION_DRIVER` | `database` | `database` | Administrator sessions. |
| `SESSION_SECURE_COOKIE` | `true` | `true` on HTTPS | HTTPS-only session cookie. |
| `SESSION_DOMAIN` | empty | empty | Optional shared cookie domain. |
| `CACHE_STORE` | `redis` | `database` | Cache backend. |
| `QUEUE_CONNECTION` | `redis` | `sync` | Queue backend; non-sync needs a worker. |
| `REDIS_CLIENT` | `phpredis` | optional | Redis client. |
| `REDIS_HOST` | `redis` | provider host | Redis host. |
| `REDIS_PORT` | `6379` | provider port | Redis port. |
| `REDIS_PASSWORD` | empty/private network | provider secret | Optional authentication. |

Database sessions/cache/queues use tables included in the migrations.

## Game-server queries

| Variable | Default | Purpose |
| --- | --- | --- |
| `GAME_QUERY_TIMEOUT_SECONDS` | `1.0` | UDP query timeout. Keep it short. |
| `GAME_QUERY_MAX_PACKET_BYTES` | `65535` | Maximum assembled A2S response size. |

Host, query port, protocol and game are stored per server in administration. Container `127.0.0.1` is the container itself, not the Docker host. Use a reachable host address, DNS name or public IP.

## Logging and migrations

| Variable | Example | Purpose |
| --- | --- | --- |
| `LOG_CHANNEL` | `stack` | Laravel log channel. |
| `LOG_LEVEL` | `warning` | Production threshold; use `debug` locally. |
| `AUTO_MIGRATE` | `true` Docker / `false` manual | Docker entrypoint runs migrations; it never seeds. |

## Docker infrastructure

These values belong in `docker/.env`:

| Variable | Values | Purpose |
| --- | --- | --- |
| `DOCKER_ENV` | `local`, `production`, `external` | Selects Compose files. |
| `APP_DOMAIN` | `cs.example.com` | Production Nginx domain without scheme. |
| `NGINX_CONTAINER_NAME` | `fragzone-nginx` | Stable, unique container name. |
| `EXTERNAL_DOCKER_NETWORK` | `server1_net` | Existing reverse-proxy network. |

- `local`: host port 80, bind-mounted source, MariaDB, Redis and scheduler.
- `production`: ports 80/443, Let's Encrypt, immutable app, queue and scheduler.
- `external`: production services on an existing proxy network, with no published Nginx ports.

## Production checklist

- `APP_ENV=production`, `APP_DEBUG=false`, exact HTTPS `APP_URL`.
- Unique preserved `APP_KEY`, database runtime password and root password.
- `SESSION_SECURE_COOKIE=true` when TLS is used.
- No demo seed; create the administrator with `admin:create`.
- Scheduler every minute and a queue worker when the queue is not `sync`.
- Outbound UDP verified with `servers:refresh`.
- Database and `.env` backups tested.
