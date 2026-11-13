# Система за Blade теми

[Български](themes.md) | [English](en/themes.md)

FragZone темите са устойчиви при обновяване публични Blade overrides. Пълната тема по подразбиране е в `resources/views/themes/fragzone`. Всяка друга директна child директория е опционална custom тема и не изисква PHP class, package, JSON manifest или дублирана бизнес логика.

## Структура на директориите

Custom тема `fragzone-dark` може да съдържа само страниците, които променя:

```text
resources/views/themes/fragzone-dark/
├── home.blade.php
├── layouts/
│   └── app.blade.php
├── servers/
│   ├── index.blade.php
│   └── show.blade.php
└── news/
    └── index.blade.php
```

Името на директорията е slug на темата. То трябва да започва с малка латинска буква или цифра и може да съдържа само малки латински букви, цифри и тирета.

Не е нужен manifest. Selector-ът в администрацията автоматично открива директните child директории. Slug като `fragzone-dark` се показва като `Fragzone Dark`. Опционални custom имена могат да се добавят в `config/themes.php`:

```php
'labels' => [
    'fragzone' => 'FragZone Core',
    'fragzone-dark' => 'FragZone Dark',
],
```

## Override и fallback поведение

Файловете на custom темата използват същото относително име като файловете в темата FragZone:

| Изглед във FragZone | Custom override |
| --- | --- |
| `resources/views/themes/fragzone/home.blade.php` | `resources/views/themes/fragzone-dark/home.blade.php` |
| `resources/views/themes/fragzone/layouts/app.blade.php` | `resources/views/themes/fragzone-dark/layouts/app.blade.php` |
| `resources/views/themes/fragzone/servers/index.blade.php` | `resources/views/themes/fragzone-dark/servers/index.blade.php` |

Когато override липсва, Laravel render-ва съответния изглед от пълната тема `fragzone`. Така малка тема може да замени само `home.blade.php`, а пълен редизайн може да замени публичния layout и всички публични страници.

Администрацията и `/admin/login` винаги използват core изгледи, за да не може повредена публична тема да блокира достъпа на операторите.

Ако избраната theme директория бъде премахната при обновяване, FragZone автоматично преминава към физическата директория `fragzone`. Стойността в базата не се приема, освен ако съответната инсталирана директория съществува.

## Създаване на тема

1. Създайте `resources/views/themes/<slug>`.
2. Копирайте от `resources/views/themes/fragzone` само Blade файловете, които искате да промените.
3. Запазете съвместими route имената, form methods, CSRF tokens и очакваните variables.
4. Отворете **Администрация → SEO и настройки → Активна тема** и изберете откритата директория.
5. Тествайте началната страница, списъка/детайлите за сървър, новините, двата езика и responsive layout-а.

Пример:

```bash
mkdir -p resources/views/themes/fragzone-dark/layouts
cp resources/views/themes/fragzone/home.blade.php resources/views/themes/fragzone-dark/home.blade.php
cp resources/views/themes/fragzone/layouts/app.blade.php resources/views/themes/fragzone-dark/layouts/app.blade.php
```

Theme-specific стиловете могат да бъдат написани в override-нат Blade layout или да сочат към versioned файл в `public/themes/<slug>/`. Публичните assets са опционални; самата theme система изисква само Blade файлове и продължава да използва FragZone assets, когато layout-ът не е заменен.

## Правила при обновяване

- Не променяйте контролери, модели, migrations или core services в заявка за тема.
- Не копирайте всеки default изглед, освен ако дизайнът действително го заменя.
- След core update сравнявайте само override-натите файлове с актуалните им `themes/fragzone` версии.
- Запазете accessibility labels, locale helpers, CSRF fields и публичната навигация без admin линкове.
- Пазете всяка поръчана theme директория в Git, за да може да се deploy-ва и връща назад с приложението.

След добавяне или премахване на тема при cached manual/LAMP инсталация изчистете cached views/configuration:

```bash
php artisan optimize:clear
php artisan optimize
```

В Docker изпълнете същите операции като PHP-FPM потребителя, за да останат compiled views достъпни за запис:

```bash
./docker/compose artisan optimize:clear
./docker/compose artisan optimize
```
