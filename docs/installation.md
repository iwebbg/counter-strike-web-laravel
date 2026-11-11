# Installation with Docker

Docker is the recommended way to run FragZone. It supplies the expected PHP, Nginx, MariaDB and Redis versions and includes long-running scheduler and queue services.

For a server without Docker, see [LAMP, Apache and cPanel installation](installation-manual.md). Every setting is described in [Environment configuration](configuration.md).

## Requirements

- Docker Engine and Docker Compose v2.
- Git.
- A domain with editable DNS for direct production HTTPS.
- Outbound UDP access to every monitored Counter-Strike query port.
- Host ports 80 and 443 available in direct production mode.

## Clone and select a release

Keep production as a Git working copy:

```bash
git clone https://github.com/iwebbg/counter-strike-web-laravel.git
cd counter-strike-web-laravel
git switch main
git pull --ff-only origin main
```

When version tags are available, use a fixed production release:

```bash
git fetch origin --tags --prune
git tag --sort=-version:refname
git switch --detach v1.0.0
```

Keep deployment values only in the untracked root `.env` and `docker/.env`.

## Local installation

```bash
cp .env.example .env
cp docker/.env.example docker/.env
./docker/compose up --detach --build
./docker/compose exec -T app php artisan key:generate
./docker/compose exec -T app php artisan migrate --seed --force
```

Keep `DOCKER_ENV=local` in `docker/.env` and open `http://localhost`.

```text
Email: admin@fragzone.local
Password: ChangeMe123!
```

The credentials and seeded content are for local evaluation only. Source is bind-mounted; Composer dependencies live in a Docker volume and install automatically on first start.

```bash
./docker/compose ps
./docker/compose logs --follow app scheduler nginx database redis
./docker/compose exec -T app php artisan about
./docker/compose exec -T app php artisan servers:refresh
./docker/compose down
```

Normal `down` keeps data volumes. Do not add `--volumes` unless all stored data is intentionally being deleted.

## Direct production with automatic HTTPS

### DNS and firewall

Point the domain's A/AAAA records to the Docker host. Allow inbound TCP 80/443 and outbound/return UDP traffic for the configured game-server query ports, commonly around 27015.

### Environment

```bash
cp .env.example .env
cp docker/.env.example docker/.env
```

Use strong unique secrets and this production shape in the root `.env`:

```dotenv
APP_NAME=FragZone
APP_ENV=production
APP_KEY=base64:replace-with-a-generated-32-byte-key
APP_DEBUG=false
APP_URL=https://cs.example.com
APP_TIMEZONE=Europe/Sofia
APP_LOCALE=bg
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mariadb
DB_HOST=database
DB_PORT=3306
DB_DATABASE=fragzone
DB_USERNAME=fragzone
DB_PASSWORD=replace-with-a-long-random-password
DB_ROOT_PASSWORD=replace-with-a-different-root-password

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_CLIENT=phpredis
REDIS_HOST=redis
AUTO_MIGRATE=true

GAME_QUERY_TIMEOUT_SECONDS=1.0
GAME_QUERY_MAX_PACKET_BYTES=65535
LOG_CHANNEL=stack
LOG_LEVEL=warning
```

Generate an application key and copy the complete output into `APP_KEY`:

```bash
php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

Set `docker/.env`:

```dotenv
DOCKER_ENV=production
APP_DOMAIN=cs.example.com
NGINX_CONTAINER_NAME=fragzone-nginx
EXTERNAL_DOCKER_NETWORK=server1_net
```

`APP_DOMAIN` is a host name only, without `https://` or a path.

### Start and provision

```bash
./docker/compose up --detach --build
./docker/compose ps
./docker/compose exec -T app php artisan migrate --force
./docker/compose exec app php artisan admin:create
./docker/compose exec -T app php artisan optimize
```

Do not run `db:seed` in production. `admin:create` requires a unique email and a password of at least 12 characters with uppercase/lowercase letters, a number and a symbol.

On first start, Nginx requests a Let's Encrypt certificate using `webmaster@APP_DOMAIN`, then redirects HTTP to HTTPS. Renewal is checked every 12 hours and successful renewal reloads Nginx.

```bash
./docker/compose logs --follow nginx app
```

## Behind an existing reverse proxy

Use external mode when Traefik, Nginx Proxy Manager, Caddy or another proxy already owns ports 80/443 and TLS.

Create the proxy network only if it does not already exist:

```bash
docker network create server1_net
```

Configure `docker/.env`:

```dotenv
DOCKER_ENV=external
APP_DOMAIN=cs.example.com
NGINX_CONTAINER_NAME=fragzone-nginx
EXTERNAL_DOCKER_NETWORK=server1_net
```

Keep `APP_URL=https://cs.example.com`, `APP_ENV=production` and `APP_DEBUG=false`, then run:

```bash
./docker/compose up --detach --build
./docker/compose exec -T app php artisan migrate --force
./docker/compose exec app php artisan admin:create
```

FragZone Nginx joins the named network as `fragzone-nginx`, exposes internal port 80 and publishes no host port. Forward the domain to `http://fragzone-nginx:80` and send `X-Forwarded-Proto: https`.

## Updates

Back up the database and environment files first:

```bash
git fetch origin --tags --prune
git pull --ff-only origin main
./docker/compose up --detach --build
./docker/compose exec -T app php artisan migrate --force
./docker/compose exec -T app php artisan optimize
```

For tag deployments, switch to the selected newer tag instead of pulling `main`.

## Backup and restore

Create a dump outside the containers:

```bash
./docker/compose exec -T database sh -c 'mariadb-dump -uroot -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"' > fragzone-backup.sql
```

Test backups on a separate installation. Restore only in a maintenance window because it replaces current database content:

```bash
./docker/compose exec -T database sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"' < fragzone-backup.sql
```

Persistent Docker volumes are not a substitute for an external backup.

## Troubleshooting

### Missing `vendor/autoload.php`

Wait for the first dependency installation and inspect logs:

```bash
./docker/compose logs --follow app
./docker/compose exec -T app composer install --no-interaction --prefer-dist
```

### Server always offline

```bash
./docker/compose exec -T app php artisan servers:refresh
./docker/compose logs --tail=200 scheduler
```

Verify host/query port, Valve A2S availability and UDP firewall rules. A successful TCP connection does not prove the UDP query works.

### Certificate request fails

Confirm DNS points to this host, 80/443 are free, `APP_DOMAIN` has no scheme and Let's Encrypt can reach port 80. Use external mode when another service owns the public ports.
