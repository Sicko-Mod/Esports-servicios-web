@extends('admin.layouts.app')

@section('titulo', 'Usuarios')

@section('contenido')
    <h1 class="admin-titulo">Usuarios y roles</h1>

    @if ($superadmins < 2)
        <div class="admin-errores" role="alert">
            El reglamento pide dos superadministradores con años de graduación distintos. Hoy hay {{ $superadmins }}.
        </div>
    @endif

    @unless ($puedeGestionar)
        <p style="font-size:0.9rem;color:var(--jg-tenue)">Solo un superadministrador puede cambiar roles.</p>
    @endunless

    <form method="get" action="{{ route('admin.usuarios.index') }}" class="admin-form" style="display:flex;gap:1rem;align-items:flex-end;margin-bottom:1rem">
        <label style="margin:0;flex:1">
            <span>Buscar por nombre o correo</span>
            <input type="search" name="q" value="{{ $busqueda }}">
        </label>
        <button type="submit" class="admin-btn admin-btn--secundario">Buscar</button>
    </form>

    @if ($usuarios->isEmpty())
        <p>No hay usuarios con ese filtro.</p>
    @else
        <table class="admin-tabla">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Gamer tag</th>
                    <th>Rol</th>
                    <th>Correo confirmado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ $usuario->miembro?->gamer_tag ?? '—' }}</td>
                        <td>{{ $usuario->rol->etiqueta() }}</td>
                        <td>{{ $usuario->email_verified_at ? 'Sí' : 'No' }}</td>
                        <td>
                            @if ($puedeGestionar)
                                <a class="admin-btn admin-btn--secundario" href="{{ route('admin.usuarios.edit', $usuario) }}">Cambiar rol</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $usuarios->links('pagination::simple-default') }}
    @endif
@endsection
