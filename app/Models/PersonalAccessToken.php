<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'last_used_at',
        'device_info',
    ];

    protected $casts = [
        'abilities' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'device_info' => 'array',
    ];

    /**
     * Get the user that owns the token.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'tokenable_id');
    }

    /**
     * Determine if the token has a given ability.
     */
    public function can($ability): bool
    {
        if (in_array('*', $this->abilities)) {
            return true;
        }

        return in_array($ability, $this->abilities);
    }

    /**
     * Determine if the token is missing a given ability.
     */
    public function cant($ability): bool
    {
        return !$this->can($ability);
    }
}