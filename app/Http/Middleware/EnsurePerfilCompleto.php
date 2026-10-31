<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePerfilCompleto
{
    public function handle(Request $request, Closure $next): Response
    {
        $miembro = $request->user()?->miembro;

        if (! $miembro || ! $miembro->perfilCompleto()) {
            return redirect()
                ->route('portal.perfil.edit')
                ->with('estado', 'Completá tu perfil de miembro para continuar.');
        }

        return $next($request);
    }
}
