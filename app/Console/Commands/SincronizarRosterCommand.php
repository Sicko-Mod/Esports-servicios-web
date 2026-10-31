<?php

namespace App\Console\Commands;

use App\Models\Miembro;
use App\Support\SincronizadorRosterPublico;
use Illuminate\Console\Command;

class SincronizarRosterCommand extends Command
{
    protected $signature = 'roster:sincronizar';

    protected $description = 'Sincroniza el roster público con los perfiles del portal de miembros';

    public function handle(): int
    {
        $total = 0;

        Miembro::query()
            ->whereNotNull('perfil_completado_at')
            ->each(function (Miembro $miembro) use (&$total) {
                SincronizadorRosterPublico::sincronizar($miembro);
                $total++;
            });

        $this->info("Perfiles procesados: {$total}");

        return self::SUCCESS;
    }
}
