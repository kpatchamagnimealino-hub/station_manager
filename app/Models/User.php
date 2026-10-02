<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public const ROLE_ADMINISTRATOR = 'administrator';

    public const ROLE_STATION_MANAGER = 'station_manager';

    public const ROLE_GENERAL_DIRECTOR = 'general_director';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMINISTRATOR;
    }

    public function getRoleAttribute(?string $value): string
    {
        if ((bool) ($this->attributes['is_admin'] ?? false)) {
            return self::ROLE_ADMINISTRATOR;
        }

        return $value ?? self::ROLE_STATION_MANAGER;
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMINISTRATOR => 'admin.dashboard',
            self::ROLE_STATION_MANAGER => 'manager.dashboard',
            self::ROLE_GENERAL_DIRECTOR => 'director.dashboard',
            default => 'login',
        };
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_ADMINISTRATOR => 'Administrateur',
            self::ROLE_STATION_MANAGER => 'Responsable de station',
            self::ROLE_GENERAL_DIRECTOR => 'Directeur général',
            default => 'Utilisateur',
        };
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
            'is_admin' => 'boolean',
        ];
    }
}
