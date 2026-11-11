<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Groups servers by gameplay style. */
class ServerCategory extends Model
{
    protected $fillable = ['name', 'name_translations', 'slug', 'description', 'description_translations', 'accent_color'];

    /** Defines localized content casts. */
    protected function casts(): array
    {
        return ['name_translations' => 'array', 'description_translations' => 'array'];
    }

    /** Returns the category name for the active locale. */
    public function localizedName(?string $locale = null): string
    {
        return $this->localized('name_translations', 'name', $locale);
    }

    /** Returns the category description for the active locale. */
    public function localizedDescription(?string $locale = null): string
    {
        return $this->localized('description_translations', 'description', $locale);
    }

    /** Returns servers assigned to the category. */
    public function servers(): HasMany
    {
        return $this->hasMany(GameServer::class);
    }

    /** Resolves localized content with legacy and configured fallbacks. */
    private function localized(string $translationsAttribute, string $legacyAttribute, ?string $locale): string
    {
        $translations = $this->getAttribute($translationsAttribute) ?? [];
        $locale ??= app()->getLocale();

        return (string) ($translations[$locale] ?? $translations[config('app.fallback_locale')] ?? $this->getAttribute($legacyAttribute) ?? '');
    }
}
