# Конфигурация на средата

[Български](configuration.md) | [English](en/configuration.md)

FragZone зарежда runtime настройките от непроследявания root файл `.env`. Docker wrapper-ът чете `docker/.env`, за да избере инфраструктурен режим.

```bash
cp .env.example .env
cp docker/.env.example docker/.env
```

Никога не commit-вайте истински secrets. След production промени изчистете и изградете отново конфигурацията. При manual/LAMP инсталация изпълнете:

```bash
php artisan optimize:clear
php artisan optimize
```

В Docker използвайте helper-а за application user, който предотвратява root-owned cache файлове:

```bash
./docker/compose artisan optimize:clear
./docker/compose artisan optimize
```

## Приложение

| Променлива | Пример | Предназначение |
| --- | --- | --- |
| `APP_NAME` | `FragZone` | Вътрешно Laravel име; публичният брандинг се управлява от администрацията. |
| `APP_ENV` | `local` / `production` | Runtime среда. |
| `APP_KEY` | `base64:...` | Ключ за encryption/signing; генерирайте веднъж и го запазете. |
| `APP_DEBUG` | `false` | Подробни exceptions; винаги `false` публично. |
| `APP_URL` | `https://cs.example.com` | Каноничен URL, включително scheme. |
| `APP_TIMEZONE` | `Europe/Sofia` | Timezone за публикуване и scheduler. |
| `APP_LOCALE` | `bg` | Език по подразбиране от `config/localization.php`. |
| `APP_FALLBACK_LOCALE` | `en` | Fallback при липсващи преводи. |
| `APP_PREVIOUS_KEYS` | празно | Опционални предишни ключове при контролирана key rotation. |

Промяната на `APP_KEY` анулира сесиите и може да направи encrypted стойности нечетими. Пазете сигурен backup.

## MariaDB/MySQL

| Променлива | Docker | Manual/cPanel | Предназначение |
| --- | --- | --- | --- |
| `DB_CONNECTION` | `mariadb` | `mariadb` | Database driver. |
| `DB_HOST` | `database` | `127.0.0.1` или `localhost` | Host, видим от PHP. |
| `DB_PORT` | `3306` | `3306` | Database port. |
| `DB_DATABASE` | `fragzone` | име от hosting доставчика | Application database. |
| `DB_USERNAME` | `fragzone` | ограничен потребител | Runtime login. |
| `DB_PASSWORD` | силен secret | силен secret | Runtime парола. |
| `DB_ROOT_PASSWORD` | различен силен secret | не се използва | Само за начална Docker MariaDB конфигурация. |

Docker initialization стойностите важат само при първото създаване на `database-data`. Последващата им промяна не сменя credentials, които вече са записани в MariaDB.

## Сесии, cache, опашки и Redis

| Променлива | Docker production | Опростен cPanel | Предназначение |
| --- | --- | --- | --- |
| `SESSION_DRIVER` | `database` | `database` | Администраторски сесии. |
| `SESSION_SECURE_COOKIE` | `true` | `true` при HTTPS | Session cookie само през HTTPS. |
| `SESSION_DOMAIN` | празно | празно | Опционален споделен cookie domain. |
| `CACHE_STORE` | `redis` | `database` | Cache backend. |
| `QUEUE_CONNECTION` | `redis` | `sync` | Queue backend; при стойност различна от `sync` е нужен worker. |
| `REDIS_CLIENT` | `phpredis` | опционално | Redis client. |
| `REDIS_HOST` | `redis` | host от доставчика | Redis host. |
| `REDIS_PORT` | `6379` | port от доставчика | Redis port. |
| `REDIS_PASSWORD` | празно/private network | secret от доставчика | Опционална автентикация. |

Database sessions/cache/queues използват таблиците, включени в migrations.

## Заявки към игровите сървъри

| Променлива | По подразбиране | Предназначение |
| --- | --- | --- |
| `GAME_QUERY_TIMEOUT_SECONDS` | `1.0` | UDP query timeout. Поддържайте го кратък. |
| `GAME_QUERY_MAX_PACKET_BYTES` | `65535` | Максимален размер на сглобен A2S response. |

Host, query port, protocol и game се пазят за всеки сървър в администрацията. В контейнер `127.0.0.1` сочи към самия контейнер, а не към Docker host-а. Използвайте достъпен host адрес, DNS име или публичен IP.

## Logging и migrations

| Променлива | Пример | Предназначение |
| --- | --- | --- |
| `LOG_CHANNEL` | `stack` | Laravel log channel. |
| `LOG_LEVEL` | `warning` | Production threshold; използвайте `debug` локално. |
| `AUTO_MIGRATE` | `true` Docker / `false` manual | Docker entrypoint изпълнява migrations; никога не seed-ва. |

## Docker инфраструктура

Тези стойности са в `docker/.env`:

| Променлива | Стойности | Предназначение |
| --- | --- | --- |
| `DOCKER_ENV` | `local`, `production`, `external` | Избира Compose файловете. |
| `APP_DOMAIN` | `cs.example.com` | Production Nginx domain без scheme. |
| `NGINX_CONTAINER_NAME` | `fragzone-nginx` | Стабилно и уникално име на контейнера. |
| `EXTERNAL_DOCKER_NETWORK` | `server1_net` | Съществуваща reverse-proxy network. |

- `local`: host port 80, bind-mounted source, MariaDB, Redis и scheduler.
- `production`: портове 80/443, Let's Encrypt, immutable app, queue и scheduler.
- `external`: production services в съществуваща proxy network, без публикувани Nginx портове.

## Production checklist

- `APP_ENV=production`, `APP_DEBUG=false` и точен HTTPS `APP_URL`.
- Уникални и запазени `APP_KEY`, database runtime парола и root парола.
- `SESSION_SECURE_COOKIE=true` при използване на TLS.
- Без demo seed; създайте администратора чрез `admin:create`.
- Scheduler всяка минута и queue worker, когато queue не е `sync`.
- Потвърден изходящ UDP чрез `servers:refresh`.
- Тествани backups на базата и `.env`.
