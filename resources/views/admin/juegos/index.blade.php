@extends('admin.layouts.app')

@section('titulo', 'Grupos')

@section('contenido')
    <div class="admin-acciones">
        <h1 class="admin-titulo" style="margin:0; flex:1">Grupos / juegos</h1>
        <a class="admin-btn" href="{{ route('admin.juegos.create') }}">Nuevo grupo</a>
    </div>

    @if ($juegos->isEmpty())
        <p>No hay grupos. Creá uno o ejecutá <code>php artisan db:seed</code>.</p>
    @else
        <table class="admin-tabla">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Activo</th>
                    <th>Reunión</th>
                    <th>Capitán</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($juegos as $juego)
                    <tr>
                        <td>
                            <strong>{{ $juego->nombre }}</strong><br>
                            <small style="color:var(--jg-tenue)">/{{ $juego->slug }}</small>
                        </td>
                        <td>{{ $juego->activo ? 'Sí' : 'No' }}</td>
                        <td>
                            @if ($juego->dia)
                                {{ $juego->dia_legible }} {{ $juego->hora_legible }}
                                @if ($juego->lugar)
                                    <br><small>{{ $juego->lugar }}</small>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $juego->capitan ?: '—' }}</td>
                        <td class="admin-inline">
                            <a class="admin-btn admin-btn--secundario" href="{{ route('admin.juegos.edit', $juego) }}">Editar</a>
                            <a class="admin-btn admin-btn--secundario" href="{{ route('admin.integrantes.index', ['juego_id' => $juego->id]) }}">Roster</a>
                            <form method="post" action="{{ route('admin.juegos.destroy', $juego) }}" onsubmit="return confirm('¿Eliminar este grupo y todo su roster?');">
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
