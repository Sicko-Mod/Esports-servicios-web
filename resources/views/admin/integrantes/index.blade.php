@extends('admin.layouts.app')

@section('titulo', 'Roster')

@section('contenido')
    <div class="admin-acciones">
        <h1 class="admin-titulo" style="margin:0; flex:1">Roster público</h1>
        <a class="admin-btn" href="{{ route('admin.integrantes.create', $juegoId ? ['juego_id' => $juegoId] : []) }}">Agregar integrante</a>
    </div>

    <p style="font-size:0.9rem;color:var(--jg-tenue)">Las filas marcadas como <strong>Portal</strong> se actualizan al guardar el perfil del miembro. El tier (nivel) se edita acá.</p>

    <form method="get" action="{{ route('admin.integrantes.index') }}" class="admin-form" style="margin-bottom:1.5rem">
        <label>
            <span>Filtrar por grupo</span>
            <select name="juego_id" onchange="this.form.submit()">
                <option value="">Todos los grupos</option>
                @foreach ($juegos as $juego)
                    <option value="{{ $juego->id }}" @selected($juegoId === $juego->id)>{{ $juego->nombre }}</option>
                @endforeach
            </select>
        </label>
    </form>

    @if ($integrantes->isEmpty())
        <p>No hay integrantes en este filtro.</p>
    @else
        <table class="admin-tabla">
            <thead>
                <tr>
                    <th>Gamer tag</th>
                    <th>Grupo</th>
                    <th>Nivel</th>
                    <th>Origen</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($integrantes as $integrante)
                    <tr>
                        <td>{{ $integrante->gamer_tag }}</td>
                        <td>{{ $integrante->juego->nombre }}</td>
                        <td>{{ $integrante->nivel->titulo() }}</td>
                        <td>{{ $integrante->sincronizadoConPortal() ? 'Portal' : 'Manual' }}</td>
                        <td class="admin-inline">
                            <a class="admin-btn admin-btn--secundario" href="{{ route('admin.integrantes.edit', $integrante) }}">Editar</a>
                            <form method="post" action="{{ route('admin.integrantes.destroy', $integrante) }}" onsubmit="return confirm(@js('¿Quitar a '.$integrante->gamer_tag.' del roster?'));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-btn admin-btn--peligro">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
