# LAMP, Apache and cPanel installation

[Български](../installation-manual.md) | [English](installation-manual.md)

This guide runs FragZone without Docker. The operator supplies PHP, Composer, MariaDB, TLS, cron, process supervision, backups and security updates.

Manual hosting works only when the domain document root can point to Laravel's `public` directory and the host allows outbound UDP.

## Requirements

- Apache 2.4 with `mod_rewrite`, or equivalent Nginx rules.
- PHP 8.5 for web and CLI.
- Composer 2, MariaDB 10.6+ or MySQL 8+, Git and per-minute cron.
- Optional Redis/Supervisor for larger installations.
- Outbound UDP access to monitored Counter-Strike query ports.

Required PHP extensions: `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `iconv`, `intl`, `json`, `libxml`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `session`, `tokenizer`, `xml` and `zip`. Add `redis` only for Redis-backed services.

```bash
php --version
php -m
composer check-platform-reqs
```

## Linux LAMP installation

```bash
cd /var/www
git clone https://github.com/iwebbg/counter-strike-web-laravel.git fragzone
cd fragzone
cp .env.example .env
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan key:generate
```

Create a database and restricted user as a database administrator:

```sql
CREATE DATABASE fragzone CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'fragzone'@'localhost' IDENTIFIED BY 'replace-with-a-long-random-password';
GRANT ALL PRIVILEGES ON fragzone.* TO 'fragzone'@'localhost';
FLUSH PRIVILEGES;
```

Basic production `.env` without Redis:

```dotenv
APP_NAME=FragZone
APP_ENV=production
APP_KEY=base64:generated-by-artisan
APP_DEBUG=false
APP_URL=https://cs.example.com
APP_TIMEZONE=Europe/Sofia
APP_LOCALE=bg
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fragzone
DB_USERNAME=fragzone
DB_PASSWORD=replace-with-a-long-random-password

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=sync
AUTO_MIGRATE=false
GAME_QUERY_TIMEOUT_SECONDS=1.0
GAME_QUERY_MAX_PACKET_BYTES=65535
LOG_CHANNEL=stack
LOG_LEVEL=warning
```

Docker-only values such as `DB_ROOT_PASSWORD`, `APP_DOMAIN`, `NGINX_CONTAINER_NAME` and the default Docker `REDIS_HOST` are not needed.

```bash
php artisan migrate --force
php artisan admin:create
php artisan optimize
```

Do not run the demo seeder in production.

Set appropriate ownership and writable directories:

```bash
sudo chown -R www-data:www-data /var/www/fragzone
sudo find /var/www/fragzone -type d -exec chmod 755 {} \;
sudo find /var/www/fragzone -type f -exec chmod 644 {} \;
sudo chmod -R ug+rwX /var/www/fragzone/storage /var/www/fragzone/bootstrap/cache
```

## Apache VirtualHost

```apacheconf
<VirtualHost *:80>
    ServerName cs.example.com
    DocumentRoot /var/www/fragzone/public

    <Directory /var/www/fragzone/public>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/fragzone-error.log
    CustomLog ${APACHE_LOG_DIR}/fragzone-access.log combined
</VirtualHost>
```

```bash
sudo a2enmod rewrite
sudo a2ensite fragzone.conf
sudo apachectl configtest
sudo systemctl reload apache2
```

The repository contains `public/.htaccess`. Never point a domain at the repository root because that can expose `.env` and source files.

## Scheduler and queue

Add one cron entry under the application owner:

```cron
* * * * * cd /var/www/fragzone && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

```bash
php artisan servers:refresh
php artisan schedule:list
```

No worker is required for `QUEUE_CONNECTION=sync`. Database/Redis queues require `php artisan queue:work` under Supervisor/systemd and `php artisan queue:restart` after deployment.

## cPanel installation

Confirm the plan provides PHP 8.5, required extensions, Terminal/SSH, Composer, MariaDB/MySQL, one-minute cron, editable document root and outbound UDP. Many shared hosts block UDP; live server status then cannot work.

Recommended layout:

```text
/home/account/fragzone/          complete checkout
/home/account/fragzone/public/   domain document root
```

Set the domain/subdomain document root in cPanel to `/home/account/fragzone/public`. Do not place `.env`, `storage` or the repository root under a browsable `public_html`. If the plan cannot target `public` or safely follow a symlink, use a VPS/Docker host.

From cPanel Terminal:

```bash
cd /home/account
git clone https://github.com/iwebbg/counter-strike-web-laravel.git fragzone
cd fragzone
cp .env.example .env
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan key:generate
```

Create a database/user in **MySQL Databases**, assign **ALL PRIVILEGES**, and use the prefixed names:

```dotenv
DB_CONNECTION=mariadb
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=account_fragzone
DB_USERNAME=account_fraguser
DB_PASSWORD=replace-with-the-cpanel-database-password
```

Complete the production `.env`, then run:

```bash
php artisan migrate --force
php artisan admin:create
php artisan optimize
chmod -R ug+rwX storage bootstrap/cache
```

Add a cPanel Cron Job:

```cron
* * * * * cd /home/account/fragzone && /usr/local/bin/ea-php85 artisan schedule:run >> /dev/null 2>&1
```

Use `which php` to find the actual cPanel PHP binary. Cron and web PHP versions must match. Enable AutoSSL, set the exact HTTPS `APP_URL` and `SESSION_SECURE_COOKIE=true`, then run `php artisan optimize:clear && php artisan optimize`.

## Updates

Back up first, then:

```bash
php artisan down --retry=60
git pull --ff-only origin main
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan queue:restart
php artisan up
```

## Troubleshooting

- **All routes return 404:** enable `mod_rewrite`, allow `.htaccess`, and target `public`.
- **500 error:** inspect `storage/logs/laravel.log`, verify `APP_KEY`, and fix writable permissions.
- **CSS missing:** the domain root is wrong; assets are committed in `public/assets` and need no Node.js build.
- **Server offline:** run `php artisan servers:refresh` and confirm outbound UDP/PHP stream sockets.
- **Status never changes:** install the per-minute scheduler cron and verify its PHP binary.
- **HTTPS login loop:** set exact `APP_URL`, `SESSION_SECURE_COOKIE=true`, clear config cache and verify the session table.
