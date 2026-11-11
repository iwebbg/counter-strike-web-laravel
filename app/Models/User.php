<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/** Represents an administrator identity. */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'is_admin'];

    protected $hidden = ['password', 'remember_token'];

    /** Defines model casts. */
    protected function casts(): array
    {
        return ['password' => 'hashed', 'is_admin' => 'boolean', 'email_verified_at' => 'datetime'];
    }
}
