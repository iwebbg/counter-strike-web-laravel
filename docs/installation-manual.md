# LAMP, Apache и cPanel инсталация

[Български](installation-manual.md) | [English](en/installation-manual.md)

Това ръководство стартира FragZone без Docker. Операторът осигурява PHP, Composer, MariaDB, TLS, cron, process supervision, backups и security updates.

Manual hosting работи само когато document root на domain-а може да сочи към Laravel директорията `public` и host-ът позволява изходящ UDP.

## Изисквания

- Apache 2.4 с `mod_rewrite` или еквивалентни Nginx правила.
- PHP 8.5 за web и CLI.
- Composer 2, MariaDB 10.6+ или MySQL 8+, Git и cron всяка минута.
- Опционални Redis/Supervisor за по-големи инсталации.
- Изходящ UDP достъп до query портовете на наблюдаваните Counter-Strike сървъри.

Задължителни PHP extensions: `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `iconv`, `intl`, `json`, `libxml`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `session`, `tokenizer`, `xml` и `zip`. Добавете `redis` само за Redis-базирани услуги.

```bash
php --version
php -m
composer check-platform-reqs
```

## Linux LAMP инсталация

```bash
cd /var/www
git clone https://github.com/iwebbg/counter-strike-web-laravel.git fragzone
cd fragzone
cp .env.example .env
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan key:generate
```

Създайте база и ограничен потребител като database administrator:

```sql
CREATE DATABASE fragzone CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'fragzone'@'localhost' IDENTIFIED BY 'replace-with-a-long-random-password';
GRANT ALL PRIVILEGES ON fragzone.* TO 'fragzone'@'localhost';
FLUSH PRIVILEGES;
```

Основен production `.env` без Redis:

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

Docker-only стойности като `DB_ROOT_PASSWORD`, `APP_DOMAIN`, `NGINX_CONTAINER_NAME` и default Docker `REDIS_HOST` не са необходими.

```bash
php artisan migrate --force
php artisan admin:create
php artisan optimize
```

Не стартирайте demo seeder-а в production.

Задайте подходящи ownership и writable директории:

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

Хранилището съдържа `public/.htaccess`. Никога не насочвайте domain към root директорията на хранилището, защото това може да разкрие `.env` и source файлове.

## Scheduler и queue

Добавете един cron запис под application owner-а:

```cron
* * * * * cd /var/www/fragzone && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

```bash
php artisan servers:refresh
php artisan schedule:list
```

При `QUEUE_CONNECTION=sync` не е нужен worker. Database/Redis queues изискват `php artisan queue:work` под Supervisor/systemd и `php artisan queue:restart` след deployment.

## cPanel инсталация

Потвърдете, че планът осигурява PHP 8.5, нужните extensions, Terminal/SSH, Composer, MariaDB/MySQL, cron всяка минута, редактируем document root и изходящ UDP. Много shared hosting доставчици блокират UDP; тогава live статусът няма да работи.

Препоръчителна структура:

```text
/home/account/fragzone/          пълно checkout копие
/home/account/fragzone/public/   document root на domain-а
```

Задайте document root на domain/subdomain в cPanel на `/home/account/fragzone/public`. Не поставяйте `.env`, `storage` или repository root под достъпен `public_html`. Ако планът не може да насочи към `public` или безопасно да следва symlink, използвайте VPS/Docker host.

От cPanel Terminal:

```bash
cd /home/account
git clone https://github.com/iwebbg/counter-strike-web-laravel.git fragzone
cd fragzone
cp .env.example .env
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan key:generate
```

Създайте база/потребител в **MySQL Databases**, задайте **ALL PRIVILEGES** и използвайте имената с prefix:

```dotenv
DB_CONNECTION=mariadb
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=account_fragzone
DB_USERNAME=account_fraguser
DB_PASSWORD=replace-with-the-cpanel-database-password
```

Попълнете production `.env`, след което изпълнете:

```bash
php artisan migrate --force
php artisan admin:create
php artisan optimize
chmod -R ug+rwX storage bootstrap/cache
```

Добавете cPanel Cron Job:

```cron
* * * * * cd /home/account/fragzone && /usr/local/bin/ea-php85 artisan schedule:run >> /dev/null 2>&1
```

Използвайте `which php`, за да намерите действителния cPanel PHP binary. Версиите на cron и web PHP трябва да съвпадат. Включете AutoSSL, задайте точния HTTPS `APP_URL` и `SESSION_SECURE_COOKIE=true`, след което изпълнете `php artisan optimize:clear && php artisan optimize`.

## Обновяване

Първо направете backup, след което:

```bash
php artisan down --retry=60
git pull --ff-only origin main
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan queue:restart
php artisan up
```

## Отстраняване на проблеми

- **Всички routes връщат 404:** включете `mod_rewrite`, разрешете `.htaccess` и насочете към `public`.
- **Грешка 500:** проверете `storage/logs/laravel.log`, потвърдете `APP_KEY` и поправете writable permissions.
- **Липсва CSS:** domain root е грешен; assets са commit-нати в `public/assets` и не изискват Node.js build.
- **Сървърът е offline:** изпълнете `php artisan servers:refresh` и потвърдете изходящ UDP/PHP stream sockets.
- **Статусът не се променя:** добавете cron всяка минута и проверете използвания PHP binary.
- **HTTPS login loop:** задайте точен `APP_URL`, `SESSION_SECURE_COOKIE=true`, изчистете config cache и проверете session таблицата.
