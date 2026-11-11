# Blade themes

Each direct child directory is an automatically discovered public theme. `fragzone/` is the complete default theme and fallback for every custom theme.

For example, `resources/views/themes/fragzone-dark/home.blade.php` overrides the public home page. Missing files fall back to the matching Blade file in `resources/views/themes/fragzone/`.

See `docs/themes.md` for the complete contract.
