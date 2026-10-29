<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    public function isGuru(): bool
    {
        return $this->role === UserRole::Guru;
    }

    public function isKepsek(): bool
    {
        return $this->role === UserRole::Kepsek;
    }

    public function isTu(): bool
    {
        return $this->role === UserRole::Tu;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function canManageMasterData(): bool
    {
        return $this->isAdmin();
    }

    public function canViewAllJournals(): bool
    {
        return $this->isKepsek() || $this->isTu();
    }

    public function canModifyAcademicData(): bool
    {
        return $this->isGuru();
    }
}
