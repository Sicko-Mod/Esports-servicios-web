<?php

namespace App\Models;

use App\Enums\Rol;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'rol'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'rol' => Rol::class,
        ];
    }

    public function miembro(): HasOne
    {
        return $this->hasOne(Miembro::class);
    }

    public function juegosComoCapitan(): HasMany
    {
        return $this->hasMany(Juego::class, 'capitan_user_id');
    }

    public function isAdmin(): bool
    {
        return $this->rol?->puedeAdministrarPanel() ?? false;
    }

    public function esSuperadmin(): bool
    {
        return $this->rol === Rol::Superadmin;
    }

    public function esCapitanDe(Juego $juego): bool
    {
        return (int) $juego->capitan_user_id === (int) $this->id;
    }
}
