FROM php:8.5-fpm-bookworm AS base
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y --no-install-recommends git unzip libicu-dev libonig-dev libsqlite3-dev libzip-dev libfcgi-bin && docker-php-ext-install -j"$(nproc)" intl mbstring pdo_mysql pdo_sqlite pcntl zip && pecl install redis && docker-php-ext-enable redis && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY docker/php/entrypoint.sh /usr/local/bin/fragzone-entrypoint
RUN chmod +x /usr/local/bin/fragzone-entrypoint
ENTRYPOINT ["fragzone-entrypoint"]
CMD ["php-fpm"]
FROM base AS development
FROM base AS testing
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts
COPY . .
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && composer dump-autoload --optimize --no-interaction
CMD ["composer", "test"]
FROM base AS production
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts
COPY --chown=www-data:www-data . .
RUN mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache && chown -R www-data:www-data storage bootstrap/cache && composer dump-autoload --no-dev --optimize --no-interaction
USER www-data
