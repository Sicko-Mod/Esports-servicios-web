@extends('portal.layouts.app')

@section('titulo', 'Lista de asistencia')

@section('contenido')
    <h1>{{ $sesion->juego->nombre }}</h1>
    <p>{{ $sesion->fecha->format('l d/m/Y') }}</p>

    <form method="post" action="{{ route('portal.capitan.lista.update', $sesion) }}">
        @csrf
        @method('PUT')

        <ul class="portal-lista-asistencia">
            @foreach ($miembros as $miembro)
                @php
                    $marca = $marcas->get($miembro->id);
                    $actual = old('asistencias.'.$miembro->id, $marca?->estado->value ?? 'presente');
                @endphp
                <li>
                    <div class="portal-lista-asistencia__nombre">{{ $miembro->gamer_tag }}</div>
                    <div class="portal-estados" role="group" aria-label="Asistencia de {{ $miembro->gamer_tag }}">
                        @foreach ($estados as $estado)
                            <label>
                                <input type="radio" name="asistencias[{{ $miembro->id }}]" value="{{ $estado->value }}" @checked($actual === $estado->value) required>
                                <span>{{ $estado->etiqueta() }}</span>
                            </label>
                        @endforeach
                    </div>
                </li>
            @endforeach
        </ul>

        @if ($miembros->isEmpty())
            <p>No hay miembros registrados en este grupo. Que se registren en el portal y elijan el grupo en su perfil.</p>
        @else
            <div class="portal-pie-accion">
                <button type="submit" class="portal-btn" style="width:100%;padding:1rem;font-size:1.1rem">Guardar lista</button>
            </div>
        @endif
    </form>
@endsection
