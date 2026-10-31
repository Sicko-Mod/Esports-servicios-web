<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Semestre;
use App\Support\CalculadoraAsistencia;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $miembro = $user->miembro;
        $semestre = Semestre::actual();
        $resumen = $semestre && $miembro
            ? CalculadoraAsistencia::paraMiembro($miembro, $semestre)
            : null;

        $gruposCapitan = $user->juegosComoCapitan()->activos()->ordenados()->get();

        return view('portal.dashboard', compact('miembro', 'semestre', 'resumen', 'gruposCapitan'));
    }
}
