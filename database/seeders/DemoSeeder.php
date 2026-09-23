<?php

namespace Database\Seeders;

use App\Models\Integrante;
use App\Models\Juego;
use App\Models\Torneo;
use Illuminate\Database\Seeder;

/**
 * Datos FALSOS para ver cómo queda el diseño. No se corre en producción.
 *
 *   php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('DemoSeeder no se corre en producción.');

            return;
        }

        $this->call(JuegoSeeder::class);

        $horarios = [
            'valorant' => ['martes', '18:00', 'Laboratorio 3'],
            'warzone-y-redsec' => ['miercoles', '17:30', 'Aula 204'],
            'overwatch' => ['jueves', '18:00', 'Laboratorio 3'],
            'fight-games' => ['viernes', '16:00', 'Sala de estudiantes'],
            'marvel-rivals' => ['lunes', '17:00', 'Discord del club'],
        ];

        $roster = [
            'tier-1' => ['Alfa', 'Bravo'],
            'tier-2' => ['Charly', 'Delta'],
            'miembro' => ['Eco', 'Foxtrot', 'Golf'],
        ];

        foreach ($horarios as $slug => [$dia, $hora, $lugar]) {
            $juego = Juego::where('slug', $slug)->firstOrFail();

            $juego->update([
                'dia' => $dia,
                'hora' => $hora,
                'lugar' => $lugar,
                'capitan' => 'Demo_Alfa',
                'activo' => true,
            ]);

            foreach ($roster as $nivel => $tags) {
                foreach ($tags as $tag) {
                    Integrante::firstOrCreate(
                        ['juego_id' => $juego->id, 'gamer_tag' => "Demo_{$tag}"],
                        ['nivel' => $nivel]
                    );
                }
            }
        }

        $valorant = Juego::where('slug', 'valorant')->first();

        Torneo::firstOrCreate(
            ['nombre' => 'Demo: Copa Jaguares'],
            [
                'juego_id' => $valorant?->id,
                'descripcion' => 'Torneo de ejemplo para revisar el diseño.',
                'fecha_inicio' => now()->addDays(21),
                'modalidad' => 'equipo',
                'abierto_externos' => true,
                'enlace_inscripcion' => 'https://example.com/inscripcion',
            ]
        );

        Torneo::firstOrCreate(
            ['nombre' => 'Demo: Liga interna'],
            [
                'juego_id' => null,
                'fecha_inicio' => now()->subDays(40),
                'modalidad' => 'individual',
                'abierto_externos' => false,
            ]
        );
    }
}
