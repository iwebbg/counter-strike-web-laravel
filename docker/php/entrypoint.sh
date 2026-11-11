#!/bin/sh
set -eu
cd /var/www/html
mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
if [ "${APP_ENV:-production}" != production ]; then git config --global --add safe.directory /var/www/html; fi
if [ ! -f vendor/autoload.php ]; then composer install --no-interaction --prefer-dist; fi
if [ -z "${APP_KEY:-}" ]; then
    configured_key=""

    if [ -f .env ]; then
        configured_key="$(sed -n 's/^APP_KEY=//p' .env | tail -n 1)"
    fi

    if [ -z "$configured_key" ] && [ "${CONTAINER_ROLE:-app}" = app ] && [ "${APP_ENV:-production}" != production ]; then
        php artisan key:generate --force --no-interaction
        configured_key="$(sed -n 's/^APP_KEY=//p' .env | tail -n 1)"
    fi

    if [ -z "$configured_key" ]; then
        echo "APP_KEY is required. Generate it before starting this container." >&2
        exit 1
    fi

    export APP_KEY="$configured_key"
fi
if [ "${APP_ENV:-production}" != production ]; then chmod -R a+rwX storage bootstrap/cache; fi
if [ "${CONTAINER_ROLE:-app}" = app ] && [ "${AUTO_MIGRATE:-false}" = true ]; then php artisan migrate --force; fi
if [ "${CONTAINER_ROLE:-app}" = queue ]; then exec php artisan queue:work --sleep=3 --tries=3; fi
if [ "${CONTAINER_ROLE:-app}" = scheduler ]; then exec php artisan schedule:work --no-interaction; fi
exec "$@"
