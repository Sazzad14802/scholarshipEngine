<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table      = 'USERS';
    protected $primaryKey = 'user_id';
    public    $timestamps = false;

    protected $fillable = [
        'username',
        'name',
        'password',
        'role',
    ];

    protected $hidden = ['password'];

    // ── Auth mappings ──────────────────────────────────────────

    /** Map Laravel's expected 'password' to our column name. */
    public function getAuthPassword(): string
    {
        return $this->password;
    }

    /** Disable remember_token — column does not exist in our table. */
    public function getRememberTokenName(): string
    {
        return '';
    }

    // ── Helpers ────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    public function isStudent(): bool
    {
        return $this->role === 'STUDENT';
    }

}
