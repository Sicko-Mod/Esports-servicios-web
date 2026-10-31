<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Rol;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = trim($request->string('q')->toString());

        $usuarios = User::query()
            ->with('miembro')
            ->when($busqueda !== '', function ($consulta) use ($busqueda) {
                $consulta->where(function ($q) use ($busqueda) {
                    $q->where('name', 'like', '%'.$busqueda.'%')
                        ->orWhere('email', 'like', '%'.$busqueda.'%');
                });
            })
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        return view('admin.usuarios.index', [
            'usuarios' => $usuarios,
            'busqueda' => $busqueda,
            'superadmins' => User::query()->where('rol', Rol::Superadmin)->count(),
            'puedeGestionar' => $request->user()->esSuperadmin(),
        ]);
    }

    public function edit(Request $request, User $usuario): View
    {
        abort_unless($request->user()->esSuperadmin(), 403);

        return view('admin.usuarios.edit', [
            'usuario' => $usuario,
            'roles' => Rol::cases(),
        ]);
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        abort_unless($request->user()->esSuperadmin(), 403);

        $datos = $request->validate([
            'rol' => ['required', Rule::enum(Rol::class)],
        ]);

        $nuevoRol = Rol::from($datos['rol']);

        $quedariaSinSuperadmin = $usuario->esSuperadmin()
            && $nuevoRol !== Rol::Superadmin
            && User::query()->where('rol', Rol::Superadmin)->count() <= 1;

        if ($quedariaSinSuperadmin) {
            return back()->withErrors(['rol' => 'No se puede quitar el último superadministrador.']);
        }

        $usuario->update(['rol' => $nuevoRol]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('estado', 'Rol de '.$usuario->name.' actualizado a '.$nuevoRol->etiqueta().'.');
    }
}
