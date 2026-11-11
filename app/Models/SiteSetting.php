<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/** Stores global branding, homepage copy and localized SEO metadata. */
class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'site_abbreviation',
        'theme_slug',
        'network_label',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'hero_eyebrow',
        'hero_primary',
        'hero_accent',
        'home_description',
        'footer_text',
    ];

    /** Defines localized JSON casts. */
    protected function casts(): array
    {
        return [
            'network_label' => 'array',
            'meta_title' => 'array',
            'meta_description' => 'array',
            'meta_keywords' => 'array',
            'hero_eyebrow' => 'array',
            'hero_primary' => 'array',
            'hero_accent' => 'array',
            'home_description' => 'array',
            'footer_text' => 'array',
        ];
    }

    /** Returns one localized setting with a configured fallback. */
    public function translated(string $attribute, ?string $locale = null): string
    {
        $translations = $this->getAttribute($attribute) ?? [];
        $locale ??= app()->getLocale();

        $fallback = $translations[$locale] ?? $translations[config('app.fallback_locale')] ?? reset($translations);

        return is_string($fallback) ? $fallback : '';
    }

    /** Returns the unaccented portion of the visual brand name. */
    public function brandPrefix(): string
    {
        $length = mb_strlen($this->site_name);
        $accentLength = min(4, max(1, $length - 1));

        return Str::upper(mb_substr($this->site_name, 0, $length - $accentLength));
    }

    /** Returns the accented ending of the visual brand name. */
    public function brandAccent(): string
    {
        $length = mb_strlen($this->site_name);
        $accentLength = min(4, max(1, $length - 1));

        return Str::upper(mb_substr($this->site_name, -$accentLength));
    }
}
