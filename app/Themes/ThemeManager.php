<?php

namespace App\Themes;

use Illuminate\Support\Str;

/** Discovers theme manifests and resolves the active update-safe view layer. */
class ThemeManager
{
    private ?array $themes = null;

    private ?array $activeTheme = null;

    public function __construct(private readonly string $themesPath, private readonly string $defaultTheme, private readonly array $labels = []) {}

    /** Returns all valid installed themes keyed by slug. */
    public function available(): array
    {
        if ($this->themes !== null) {
            return $this->themes;
        }

        $slugs = [$this->defaultTheme];
        foreach (glob(rtrim($this->themesPath, '/').'/*', GLOB_ONLYDIR) ?: [] as $themePath) {
            $slugs[] = basename($themePath);
        }

        $themes = [];
        foreach (array_unique($slugs) as $slug) {
            if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $slug)) {
                continue;
            }

            $themes[$slug] = [
                'slug' => $slug,
                'name' => $this->labels[$slug] ?? Str::headline($slug),
                'views_path' => rtrim($this->themesPath, '/').'/'.$slug,
            ];
        }

        ksort($themes);

        return $this->themes = $themes;
    }

    /** Activates an installed theme or falls back to the configured default. */
    public function activate(?string $slug): array
    {
        $themes = $this->available();
        $this->activeTheme = $themes[$slug ?? ''] ?? $themes[$this->defaultTheme] ?? reset($themes);

        if (! is_array($this->activeTheme)) {
            $this->activeTheme = [
                'slug' => $this->defaultTheme,
                'name' => 'Default',
                'views_path' => '',
            ];
        }

        return $this->activeTheme;
    }

    /** Returns the active theme, activating the default when needed. */
    public function active(): array
    {
        return $this->activeTheme ?? $this->activate($this->defaultTheme);
    }
}
