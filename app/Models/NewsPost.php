<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Represents an editorial news post. */
class NewsPost extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'body', 'is_published', 'published_at'];

    /** Defines model casts. */
    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'published_at' => 'datetime'];
    }

    /** Limits a query to visible news. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->where('published_at', '<=', now());
    }
}
