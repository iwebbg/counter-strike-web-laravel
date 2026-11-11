<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Groups servers by gameplay style. */
class ServerCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'accent_color'];

    /** Returns servers assigned to the category. */
    public function servers(): HasMany
    {
        return $this->hasMany(GameServer::class);
    }
}
