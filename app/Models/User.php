<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'class_id', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return BelongsTo<SchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * The whitelist entry this user registered with (students only).
     *
     * @return HasOne<AllowedStudent, $this>
     */
    public function allowedStudent(): HasOne
    {
        return $this->hasOne(AllowedStudent::class, 'claimed_by');
    }

    public function hasRole(Role|string ...$roles): bool
    {
        foreach ($roles as $role) {
            if ($this->role === ($role instanceof Role ? $role : Role::tryFrom($role))) {
                return true;
            }
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isModerator(): bool
    {
        return $this->role === Role::Moderator;
    }

    public function isTeacher(): bool
    {
        return $this->role === Role::Teacher;
    }

    public function isStudent(): bool
    {
        return $this->role === Role::Student;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_active' => 'boolean',
        ];
    }
}
