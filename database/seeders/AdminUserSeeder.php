<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crea la cuenta de panel. Definí ADMIN_EMAIL y ADMIN_PASSWORD en el .env.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('club.admin_email');
        $password = config('club.admin_password');

        if (! $email || ! $password) {
            $this->command?->warn('Omitiendo AdminUserSeeder: configurá ADMIN_EMAIL y ADMIN_PASSWORD en el .env.');

            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('club.admin_name', 'Administrador'),
                'password' => Hash::make($password),
                'rol' => Rol::Superadmin,
                'email_verified_at' => now(),
            ]
        );
    }
}
