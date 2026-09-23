<?php

namespace Database\Seeders;

use App\Models\Juego;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Los cinco grupos del club. Nacen inactivos: se activan cuando tengan
 * día, hora, lugar y capitán del semestre.
 */
class JuegoSeeder extends Seeder
{
    public function run(): void
    {
        // «Warzone y Redsec» es un solo grupo, no dos.
        $juegos = ['Valorant', 'Warzone y Redsec', 'Overwatch', 'Fight Games', 'Marvel Rivals'];

        foreach ($juegos as $orden => $nombre) {
            Juego::firstOrCreate(
                ['slug' => Str::slug($nombre)],
                ['nombre' => $nombre, 'orden' => $orden]
            );
        }
    }
}
