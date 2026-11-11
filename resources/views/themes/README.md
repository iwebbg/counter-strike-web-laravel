# Blade themes

Each direct child directory is an automatically discovered public theme. A theme contains only Blade files and mirrors paths from `resources/views`.

For example, `resources/views/themes/raagzone/home.blade.php` overrides the public home page, while `resources/views/themes/raagzone/servers/index.blade.php` overrides the server directory. Missing files fall back to the core views.

See `docs/themes.md` for the complete contract.
