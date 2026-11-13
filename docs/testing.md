# Тестване

[Български](testing.md) | [English](en/testing.md)

## Непрекъсната интеграция

GitHub Actions изпълнява Composer validation, Laravel Pint и пълния PHPUnit suite при pull requests и push към `develop` и `main`. Pull requests към `main` трябва да идват от `develop` branch-а на това хранилище. Всички други промени се насочват към `develop` през фокусиран feature или fix branch.

`@iwebbg` е единственият code owner. И двата защитени branch-а изискват code-owner review и успешни `Branch policy` и `Backend` проверки преди merge.

PHPUnit suite-ът на FragZone работи в disposable Docker service със SQLite in-memory база. Той трябва да остане изолиран от дългоживеещия MariaDB контейнер.

## Пълен test suite

```bash
cp .env.example .env
cp docker/.env.example docker/.env
./docker/compose test
```

Test service-ът изгражда testing PHP image, инсталира Composer dependencies, изпълнява PHPUnit със `sqlite` / `:memory:` и премахва еднократния контейнер.

PHPUnit конфигурацията и базовият test bootstrap налагат:

```text
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
```

## Фокусирани тестове

```bash
./docker/compose test tests/Feature/AdminAuthenticationTest.php
./docker/compose test --filter=test_seeded_administrator_can_log_in
```

## Правило за безопасност

Никога не изпълнявайте `php artisan test` или `vendor/bin/phpunit` в дългоживеещия `app` контейнер. Винаги използвайте `./docker/compose test`.

Тестовете с database refresh умишлено нулират конфигурираната база. Насочването им към development или production MariaDB унищожава application данните. Suite-ът проверява, че средата е `testing`, driver-ът е `sqlite`, а базата е `:memory:`.

## Текущо покритие

- Публичен портал, избор на език и разглеждане на сървъри.
- Детайли за сървър и подреждане на играчите по резултат.
- Администраторски вход и access control.
- Интерактивно създаване на администратор.
- Администрация на сървъри, категории и новини.
- Rich-text sanitization, SEO настройки и locale-tab markup.
- Parsing на Valve A2S player packets.

Автоматизираните тестове не заменят ръчна UDP проверка към истински сървър. Преди production изпълнете `servers:refresh`, прегледайте публичната detail страница и потвърдете работата на scheduler-а.
