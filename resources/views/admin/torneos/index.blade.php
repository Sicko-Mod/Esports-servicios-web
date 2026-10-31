@extends('admin.layouts.app')

@section('titulo', 'Torneos')

@section('contenido')
    <div class="admin-acciones">
        <h1 class="admin-titulo" style="margin:0; flex:1">Torneos</h1>
        <a class="admin-btn" href="{{ route('admin.torneos.create') }}">Nuevo torneo</a>
    </div>

    @if ($torneos->isEmpty())
        <p>No hay torneos publicados.</p>
    @else
        <table class="admin-tabla">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Grupo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($torneos as $torneo)
                    <tr>
                        <td>{{ $torneo->nombre }}</td>
                        <td>{{ $torneo->fecha_inicio->format('d/m/Y') }}</td>
                        <td>{{ $torneo->juego?->nombre ?? '—' }}</td>
                        <td class="admin-inline">
                            <a class="admin-btn admin-btn--secundario" href="{{ route('admin.torneos.edit', $torneo) }}">Editar</a>
                            <form method="post" action="{{ route('admin.torneos.destroy', $torneo) }}" onsubmit="return confirm('¿Eliminar este torneo?');">
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
