# Blade theme system

[Български](../themes.md) | [English](themes.md)

FragZone themes are update-safe public Blade overrides. The complete default theme lives in `resources/views/themes/fragzone`. Every other direct child directory is an optional custom theme and needs no PHP class, package, JSON manifest or duplicated business logic.

## Directory structure

A custom `fragzone-dark` theme may contain only the pages it changes:

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

The folder name is the theme slug. It must start with a lowercase letter or number and may contain only lowercase letters, numbers and hyphens.

No manifest is required. The administration selector discovers direct child directories automatically. A slug such as `fragzone-dark` is displayed as `Fragzone Dark`. Optional custom labels can be added in `config/themes.php`:

```php
'labels' => [
    'fragzone' => 'FragZone Core',
    'fragzone-dark' => 'FragZone Dark',
],
```

## Override and fallback behavior

Custom theme files use the same relative name as the default FragZone files:

| Default FragZone view | Custom override |
| --- | --- |
| `resources/views/themes/fragzone/home.blade.php` | `resources/views/themes/fragzone-dark/home.blade.php` |
| `resources/views/themes/fragzone/layouts/app.blade.php` | `resources/views/themes/fragzone-dark/layouts/app.blade.php` |
| `resources/views/themes/fragzone/servers/index.blade.php` | `resources/views/themes/fragzone-dark/servers/index.blade.php` |

When an override is absent, Laravel renders the matching view from the complete `fragzone` theme. A small theme can therefore replace only `home.blade.php`; a full redesign can replace the public layout and every public page.

Administration and `/admin/login` always use core views so a broken public theme cannot lock operators out.

If the selected theme directory is removed during an update, FragZone falls back to the physical `fragzone` directory automatically. The database value is not trusted unless the matching installed directory exists.

## Creating a theme

1. Create `resources/views/themes/<slug>`.
2. Copy only the Blade files that need visual changes from `resources/views/themes/fragzone`.
3. Keep route names, form methods, CSRF tokens and expected variables compatible with the default view.
4. Open **Administration → SEO & Settings → Active theme** and select the discovered folder.
5. Test home, server list/detail, news, both locales and responsive layouts.

Example:

```bash
mkdir -p resources/views/themes/fragzone-dark/layouts
cp resources/views/themes/fragzone/home.blade.php resources/views/themes/fragzone-dark/home.blade.php
cp resources/views/themes/fragzone/layouts/app.blade.php resources/views/themes/fragzone-dark/layouts/app.blade.php
```

Theme-specific styling can be written inside an overridden Blade layout or can reference a versioned file placed in `public/themes/<slug>/`. Public assets are optional; the theme system itself requires only Blade files and continues to use FragZone assets when the layout is not replaced.

## Update rules

- Never modify controllers, models, migrations or core services inside a theme request.
- Do not copy every default view unless the design genuinely replaces it.
- After a core update, compare only the overridden files with their current `themes/fragzone` counterparts.
- Preserve accessibility labels, locale helpers, CSRF fields and admin-free public navigation.
- Store each commissioned theme directory in Git so it can be deployed and rolled back with the application.

After adding/removing a theme on a cached manual/LAMP installation, clear cached views/configuration:

```bash
php artisan optimize:clear
php artisan optimize
```

Inside Docker run the same operations as the PHP-FPM user to keep compiled views writable:

```bash
./docker/compose artisan optimize:clear
./docker/compose artisan optimize
```
