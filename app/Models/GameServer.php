<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Stores a community game server and its last known telemetry. */
class GameServer extends Model
{
    protected $fillable = ['server_category_id', 'name', 'slug', 'game', 'protocol', 'host', 'port', 'description', 'location', 'is_featured', 'is_published', 'is_online', 'current_map', 'current_players', 'max_players', 'last_ping_ms', 'last_queried_at', 'last_query_error'];

    /** Defines model casts. */
    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'is_published' => 'boolean', 'is_online' => 'boolean', 'last_queried_at' => 'datetime'];
    }

    /** Limits a query to public servers. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /** Returns the editorial category. */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServerCategory::class, 'server_category_id');
    }

    /** Returns historical query snapshots. */
    public function snapshots(): HasMany
    {
        return $this->hasMany(ServerSnapshot::class);
    }

    /** Returns the address players can copy. */
    public function address(): string
    {
        return $this->host.':'.$this->port;
    }
}
