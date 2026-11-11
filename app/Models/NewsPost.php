<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

/** Represents an editorial news post. */
class NewsPost extends Model
{
    protected $fillable = ['title', 'title_translations', 'slug', 'excerpt', 'excerpt_translations', 'body', 'body_translations', 'is_published', 'published_at'];

    /** Defines model casts. */
    protected function casts(): array
    {
        return ['title_translations' => 'array', 'excerpt_translations' => 'array', 'body_translations' => 'array', 'is_published' => 'boolean', 'published_at' => 'datetime'];
    }

    /** Limits a query to visible news. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->where('published_at', '<=', now());
    }

    /** Returns a localized editorial field. */
    public function localized(string $attribute, ?string $locale = null): string
    {
        $translations = $this->getAttribute($attribute.'_translations') ?? [];
        $locale ??= app()->getLocale();

        return (string) ($translations[$locale] ?? $translations[config('app.fallback_locale')] ?? $this->getAttribute($attribute) ?? '');
    }

    /** Returns safe rich text, preserving legacy plain-text posts. */
    public function localizedBodyHtml(?string $locale = null): HtmlString
    {
        $body = $this->localized('body', $locale);

        return new HtmlString($body === strip_tags($body) ? nl2br(e($body)) : $body);
    }
}
