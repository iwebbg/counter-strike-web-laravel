#!/bin/sh
set -eu
cd /var/www/html
mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
if [ ! -f vendor/autoload.php ]; then composer install --no-interaction --prefer-dist; fi
if [ "${APP_ENV:-production}" != production ]; then chmod -R a+rwX storage bootstrap/cache; fi
if [ "${CONTAINER_ROLE:-app}" = app ] && [ "${AUTO_MIGRATE:-false}" = true ]; then php artisan migrate --force; fi
if [ "${CONTAINER_ROLE:-app}" = queue ]; then exec php artisan queue:work --sleep=3 --tries=3; fi
if [ "${CONTAINER_ROLE:-app}" = scheduler ]; then exec php artisan schedule:work --no-interaction; fi
exec "$@"
