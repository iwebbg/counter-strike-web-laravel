# Blade theme system

FragZone themes are update-safe public Blade overrides. A theme is one directory under `resources/views/themes` and needs no PHP class, package, JSON manifest or duplicated business logic.

## Directory structure

A custom `raagzone` theme may contain only the pages it changes:

```text
resources/views/themes/raagzone/
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

No manifest is required. The administration selector discovers direct child directories automatically. A slug such as `raagzone-dark` is displayed as `Raagzone Dark`. Optional custom labels can be added in `config/themes.php`:

```php
'labels' => [
    'fragzone' => 'FragZone Core',
    'raagzone' => 'RaagZone',
],
```

## Override and fallback behavior

Theme files use the same relative name as core files:

| Core view | Theme override |
| --- | --- |
| `resources/views/home.blade.php` | `resources/views/themes/raagzone/home.blade.php` |
| `resources/views/layouts/app.blade.php` | `resources/views/themes/raagzone/layouts/app.blade.php` |
| `resources/views/servers/index.blade.php` | `resources/views/themes/raagzone/servers/index.blade.php` |

When an override is absent, Laravel renders the core view. A small theme can therefore replace only `home.blade.php`; a full redesign can replace the public layout and every public page.

Administration and `/admin/login` always use core views so a broken public theme cannot lock operators out.

If the selected theme directory is removed during an update, FragZone falls back to `fragzone` automatically. The database value is not trusted unless the matching installed directory exists.

## Creating a theme

1. Create `resources/views/themes/<slug>`.
2. Copy only the core Blade files that need visual changes.
3. Keep route names, form methods, CSRF tokens and expected variables compatible with the core view.
4. Open **Administration → SEO & Settings → Active theme** and select the discovered folder.
5. Test home, server list/detail, news, both locales and responsive layouts.

Example:

```bash
mkdir -p resources/views/themes/raagzone/layouts
cp resources/views/home.blade.php resources/views/themes/raagzone/home.blade.php
cp resources/views/layouts/app.blade.php resources/views/themes/raagzone/layouts/app.blade.php
```

Theme-specific styling can be written inside an overridden Blade layout or can reference a versioned file placed in `public/themes/<slug>/`. Public assets are optional; the theme system itself requires only Blade files and continues to use core assets when the layout is not replaced.

## Update rules

- Never modify controllers, models, migrations or core services inside a theme request.
- Do not copy every core view unless the design genuinely replaces it.
- After a core update, compare only the overridden files with their current core counterparts.
- Preserve accessibility labels, locale helpers, CSRF fields and admin-free public navigation.
- Store each commissioned theme directory in Git so it can be deployed and rolled back with the application.

After adding/removing a theme on a cached production installation, clear cached views/configuration:

```bash
php artisan optimize:clear
php artisan optimize
```

Inside Docker, prefix these commands with `./docker/compose exec -T app`.
