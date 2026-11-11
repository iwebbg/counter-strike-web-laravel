# FragZone — Counter-Strike server portal

Laravel Blade portal for CS 1.6 and CS:GO communities. It includes a public server browser, live server details, news, categories, rankings, and a small administration area.

## Quick start

```bash
cp .env.example .env
cp docker/.env.example docker/.env
./docker/compose up --build -d
./docker/compose exec app php artisan key:generate
./docker/compose exec app php artisan migrate --seed
```

Open `http://localhost`. The seeded administrator is `admin@fragzone.local` / `ChangeMe123!`; change it immediately outside local development.

See [docs/concept.md](docs/concept.md), [docs/architecture.md](docs/architecture.md), and [docs/roadmap.md](docs/roadmap.md).
