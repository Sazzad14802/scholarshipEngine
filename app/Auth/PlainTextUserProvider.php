<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Plain-text password provider for lab/demo purposes.
 * Skips bcrypt — compares passwords as plain strings.
 */
class PlainTextUserProvider extends EloquentUserProvider
{
    /**
     * Compare password as plain text instead of using Hash::check().
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return $user->getAuthPassword() === $credentials['password'];
    }

    /**
     * Disable Laravel 12's automatic password rehashing.
     * Without this it tries to UPDATE "PASSWORD" (wrong column name).
     */
    public function rehashPasswordIfRequired(
        Authenticatable $user,
        array $credentials,
        bool $force = false
    ): void {
        // No-op — plain text mode, no rehashing needed.
    }
}
