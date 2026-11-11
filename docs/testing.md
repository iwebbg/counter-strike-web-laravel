# Testing

FragZone's PHPUnit suite runs in a disposable Docker service with SQLite in memory. It must remain isolated from the long-running MariaDB container.

## Full suite

```bash
cp .env.example .env
cp docker/.env.example docker/.env
./docker/compose test
```

The test service builds the testing PHP image, installs Composer dependencies, runs PHPUnit against `sqlite` / `:memory:` and removes the one-shot container.

The PHPUnit configuration and base test bootstrap force:

```text
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
```

## Narrow runs

```bash
./docker/compose test tests/Feature/AdminAuthenticationTest.php
./docker/compose test --filter=test_seeded_administrator_can_log_in
```

## Safety rule

Never run `php artisan test` or `vendor/bin/phpunit` inside the long-running `app` container. Always use `./docker/compose test`.

Database-refresh tests intentionally reset their configured database. Pointing them at development or production MariaDB destroys application data. The suite asserts that its environment is `testing`, its driver is `sqlite`, and its database is `:memory:`.

## Current coverage

- Public portal, language selection and server browsing.
- Server details and player score ordering.
- Administrator login and access control.
- Interactive administrator creation.
- Server, category and news administration.
- Rich-text sanitization, SEO settings and locale-tab markup.
- Valve A2S player packet parsing.

Automated tests do not replace a manual UDP check against a real server. Before production, run `servers:refresh`, inspect the public detail page and confirm scheduler execution.
