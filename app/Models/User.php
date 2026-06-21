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
        'password_hash',
        'role',
    ];

    protected $hidden = ['password_hash'];

    // ── Auth mappings ──────────────────────────────────────────

    /** Map Laravel's expected 'password' to our column name. */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
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

    // ── Relationships ──────────────────────────────────────────

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id', 'user_id');
    }

    public function scholarships()
    {
        return $this->hasMany(ScholarshipProgram::class, 'created_by', 'user_id');
    }
}
