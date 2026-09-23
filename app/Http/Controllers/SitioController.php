<?php

namespace App\Http\Controllers;

use App\Models\Juego;
use App\Models\Torneo;
use Illuminate\View\View;

/**
 * Páginas públicas del club. Solo lectura: no hay registro ni inicio de sesión aquí.
 */
class SitioController extends Controller
{
    public function inicio(): View
    {
        return view('inicio', [
            'juegos' => Juego::activos()->ordenados()->get(),
            'totalJuegos' => Juego::count(),
        ]);
    }

    public function equipos(): View
    {
        return view('equipos.index', [
            'juegos' => Juego::activos()->ordenados()->get(),
        ]);
    }

    public function equipo(Juego $juego): View
    {
        $juego->load('integrantes');

        return view('equipos.show', [
            'juego' => $juego,
            'roster' => $juego->rosterPorNivel(),
        ]);
    }

    public function torneos(): View
    {
        return view('torneos.index', [
            'proximos' => Torneo::with('juego')->proximos()->limit(10)->get(),
            'pasados' => Torneo::with('juego')->pasados()->limit(10)->get(),
        ]);
    }
}
