# FragZone — Counter-Strike server portal

FragZone is a Laravel Blade portal for CS 1.6 and CS:GO communities. It combines a public server browser, live Valve A2S telemetry, player rankings, bilingual news and a focused administration area.

## Requirements

- Docker Engine with Docker Compose v2 for the recommended installation; or
- PHP 8.5, Composer 2, MariaDB/MySQL and Apache/Nginx for a manual installation.
- Outbound UDP access from the scheduler to every configured game-server port.

## Local Docker quick start

```bash
git clone https://github.com/iwebbg/counter-strike-web-laravel.git
cd counter-strike-web-laravel
cp .env.example .env
cp docker/.env.example docker/.env
./docker/compose up --detach --build
./docker/compose exec -T app php artisan key:generate
./docker/compose exec -T app php artisan migrate --seed --force
```

Open `http://localhost`. The local seed creates `admin@fragzone.local` with password `ChangeMe123!`. This account is for local evaluation only.

## Documentation

- [Feature catalogue](docs/features.md)
- [Docker installation and production deployment](docs/installation.md)
- [LAMP, Apache and cPanel installation](docs/installation-manual.md)
- [Environment configuration reference](docs/configuration.md)
- [Testing and database-isolation rules](docs/testing.md)
- [Product concept](docs/concept.md)
- [Architecture](docs/architecture.md)
- [Roadmap](docs/roadmap.md)

## Essential commands

```bash
./docker/compose ps
./docker/compose logs --follow app scheduler nginx
./docker/compose exec -T app php artisan servers:refresh
./docker/compose exec app php artisan admin:create
./docker/compose test
```

Use `./docker/compose test` for PHPUnit. Never run `php artisan test` inside the long-running local application container.

## Security notice

Production installations must use `APP_ENV=production`, `APP_DEBUG=false`, unique database passwords and a generated `APP_KEY`. Do not run the demo seeder in production. Create the first production administrator interactively with `php artisan admin:create`.
