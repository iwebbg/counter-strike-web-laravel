# Инсталация с Docker

[Български](installation.md) | [English](en/installation.md)

Docker е препоръчителният начин за стартиране на FragZone. Той осигурява очакваните версии на PHP, Nginx, MariaDB и Redis и включва дългоживеещи scheduler и queue услуги.

За сървър без Docker вижте [LAMP, Apache и cPanel инсталация](installation-manual.md). Всички настройки са описани в [Конфигурация на средата](configuration.md).

## Изисквания

- Docker Engine и Docker Compose v2.
- Git.
- Domain с редактируем DNS за директен production HTTPS.
- Изходящ UDP достъп до query порта на всеки наблюдаван Counter-Strike сървър.
- Свободни host портове 80 и 443 в директен production режим.

## Клониране и избор на версия

Поддържайте production инсталацията като Git working copy:

```bash
git clone https://github.com/iwebbg/counter-strike-web-laravel.git
cd counter-strike-web-laravel
git switch main
git pull --ff-only origin main
```

Когато има version tags, използвайте фиксирана production версия:

```bash
git fetch origin --tags --prune
git tag --sort=-version:refname
git switch --detach v1.0.0
```

Пазете deployment стойностите само в непроследяваните root `.env` и `docker/.env`.

## Локална инсталация

```bash
cp .env.example .env
cp docker/.env.example docker/.env
./docker/compose up --detach --build
./docker/compose exec -T app php artisan key:generate
./docker/compose exec -T app php artisan migrate --seed --force
```

Оставете `DOCKER_ENV=local` в `docker/.env` и отворете `http://localhost`.

```text
Email: admin@fragzone.local
Password: ChangeMe123!
```

Тези credentials и примерното съдържание са само за локална оценка. Source кодът е bind-mounted; Composer dependencies са в Docker volume и се инсталират автоматично при първото стартиране.

```bash
./docker/compose ps
./docker/compose logs --follow app scheduler nginx database redis
./docker/compose exec -T app php artisan about
./docker/compose exec -T app php artisan servers:refresh
./docker/compose down
```

Обикновеното `down` запазва data volumes. Не добавяйте `--volumes`, освен ако умишлено изтривате всички записани данни.

## Директен production режим с автоматичен HTTPS

### DNS и firewall

Насочете A/AAAA записите на domain-а към Docker host-а. Разрешете входящ TCP 80/443 и изходящ/ответен UDP трафик за конфигурираните query портове на игровите сървъри, обичайно около 27015.

### Environment

```bash
cp .env.example .env
cp docker/.env.example docker/.env
```

Използвайте силни уникални secrets и следната production структура в root `.env`:

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

Генерирайте application key и копирайте целия output в `APP_KEY`:

```bash
php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

Настройте `docker/.env`:

```dotenv
DOCKER_ENV=production
APP_DOMAIN=cs.example.com
NGINX_CONTAINER_NAME=fragzone-nginx
EXTERNAL_DOCKER_NETWORK=server1_net
```

`APP_DOMAIN` е само host име, без `https://` или path.

### Стартиране и конфигуриране

```bash
./docker/compose up --detach --build
./docker/compose ps
./docker/compose exec -T app php artisan migrate --force
./docker/compose exec app php artisan admin:create
./docker/compose artisan optimize
```

Не изпълнявайте `db:seed` в production. `admin:create` изисква уникален email и парола с поне 12 символа, малки/главни букви, цифра и специален символ.

При първото стартиране Nginx заявява Let's Encrypt сертификат чрез `webmaster@APP_DOMAIN`, след което пренасочва HTTP към HTTPS. Подновяването се проверява на всеки 12 часа, а успешно подновяване reload-ва Nginx.

```bash
./docker/compose logs --follow nginx app
```

## Зад съществуващ reverse proxy

Използвайте external режим, когато Traefik, Nginx Proxy Manager, Caddy или друг proxy вече управлява портове 80/443 и TLS.

Създайте proxy network само ако още не съществува:

```bash
docker network create server1_net
```

Настройте `docker/.env`:

```dotenv
DOCKER_ENV=external
APP_DOMAIN=cs.example.com
NGINX_CONTAINER_NAME=fragzone-nginx
EXTERNAL_DOCKER_NETWORK=server1_net
```

Оставете `APP_URL=https://cs.example.com`, `APP_ENV=production` и `APP_DEBUG=false`, след което изпълнете:

```bash
./docker/compose up --detach --build
./docker/compose exec -T app php artisan migrate --force
./docker/compose exec app php artisan admin:create
```

FragZone Nginx се включва в посочената network като `fragzone-nginx`, expose-ва вътрешен порт 80 и не публикува host port. Насочете domain-а към `http://fragzone-nginx:80` и изпращайте `X-Forwarded-Proto: https`.

## Обновяване

Първо архивирайте базата и environment файловете:

```bash
git fetch origin --tags --prune
git pull --ff-only origin main
./docker/compose up --detach --build
./docker/compose exec -T app php artisan migrate --force
./docker/compose artisan optimize
```

При deployment по tag преминете към избрания по-нов tag, вместо да pull-вате `main`.

## Backup и restore

Създайте dump извън контейнерите:

```bash
./docker/compose exec -T database sh -c 'mariadb-dump -uroot -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"' > fragzone-backup.sql
```

Тествайте backups на отделна инсталация. Възстановявайте само в maintenance прозорец, защото операцията заменя текущите данни:

```bash
./docker/compose exec -T database sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"' < fragzone-backup.sql
```

Persistent Docker volumes не заменят външен backup.

## Отстраняване на проблеми

### Липсва `vendor/autoload.php`

Изчакайте първата инсталация на dependencies и проверете логовете:

```bash
./docker/compose logs --follow app
./docker/compose exec -T app composer install --no-interaction --prefer-dist
```

### Сървърът винаги е offline

```bash
./docker/compose exec -T app php artisan servers:refresh
./docker/compose logs --tail=200 scheduler
```

Проверете host/query port, Valve A2S достъпа и UDP firewall правилата. Успешната TCP връзка не доказва, че UDP заявката работи.

### Заявката за сертификат е неуспешна

Потвърдете, че DNS сочи към този host, портове 80/443 са свободни, `APP_DOMAIN` няма scheme и Let's Encrypt достига порт 80. Използвайте external режим, когато друга услуга управлява публичните портове.
