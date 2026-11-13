# Архитектура

[Български](architecture.md) | [English](en/architecture.md)

## Технологичен стек

- PHP 8.5 и Laravel 13
- Blade шаблони с ръчно разработен CSS и малки подобрения с vanilla JavaScript
- MariaDB 11.8 за данните на приложението
- Redis 8 за cache, опашки и сесии
- Nginx/PHP-FPM контейнери с local режим, production режим с автоматичен Let's Encrypt и режим за външен reverse proxy

## Граница на темите

Публичните теми са директории само с Blade файлове под `resources/views/themes/<slug>`. Пълната директория `fragzone` е физическата тема по подразбиране и fallback. Активният slug се пази в `site_settings`, валидира се спрямо инсталираните директории и се прилага от web middleware преди render на изгледа. Laravel търси първо в активната тема, а след това в `themes/fragzone`, затова custom темите могат да заменят отделни страници без копиране на контролери, модели или бизнес логика. Администрацията използва отделен core layout, за да се запази достъпът на операторите.

## Domain модел

- `users`: администраторски профили с capability флаг `is_admin`.
- `server_categories`: редакционни групи със slug и акцентен цвят.
- `game_servers`: данни за връзка, избор на игра/протокол, визуални данни и последно известно състояние.
- `server_snapshots`: резултати от заявки във времето за графики и класации.
- `news_posts`: чернови или публикувано редакционно съдържание.

## Граница на заявките

`GameServerQuery` е договорът на приложението. `SourceEngineQuery` реализира Valve A2S UDP заявки към Source и GoldSrc сървъри. Контролерите никога не отварят sockets. Планираната команда `servers:refresh` поставя в опашка или изпълнява обновяванията, записва компактно последното известно състояние във всеки сървър и добавя snapshot.

LGSL 7 се използва само като референция за протоколите. Неговият branch `lgsl7` е GPL-3.0, отбелязан е като experimental/unstable и изисква стара версия на PHPUnit като runtime Composer dependency. Не е импортиран LGSL код, с което се запазват лицензът на проекта и съвместимостта с PHPUnit 12. Adapter границата не позволява protocol код от трета страна да навлиза в контролерите и моделите.

## Операционни режими

- Local: bind-mounted source, MariaDB и Redis, достъпни на порт 80.
- Production: immutable PHP и Nginx images, постоянни application/database volumes, HTTP към HTTPS redirect и автоматично издаване/подновяване на сертификат.
- External: Nginx на приложението се включва в съществуваща Docker network, а host reverse proxy управлява TLS и публичните портове.

Заявките към сървърите използват изходящ UDP. Production firewall-ът трябва да позволява UDP трафик до конфигурираните портове на игровите сървъри. Timeout стойностите остават кратки, а обновяванията се изпълняват извън публичните HTTP заявки.

## Базова сигурност

Admin маршрутите изискват session authentication и middleware `is_admin`. Form requests валидират адреси, портове и publish състояние. Паролите използват Laravel hashing, CSRF защитата е включена, Nginx изпраща security headers, а secrets остават в environment файловете.
