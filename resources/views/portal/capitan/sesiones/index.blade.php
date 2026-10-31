@extends('portal.layouts.app')

@section('titulo', 'Pasar lista')

@section('contenido')
    <h1>Pasar lista</h1>
    @if ($semestre)
        <p>{{ $semestre->nombre }}</p>
    @else
        <div class="portal-tarjeta">
            <p>No hay semestre activo. Un administrador debe configurarlo antes de pasar lista.</p>
        </div>
    @endif

    @if ($grupos->isEmpty())
        <div class="portal-tarjeta">
            <p>No tenés ningún grupo asignado como capitán. Pedile a un administrador que te designe en el panel.</p>
        </div>
    @elseif ($semestre)
        <div class="portal-tarjeta">
            <h2 style="margin-top:0">Nueva sesión</h2>
            <form method="post" action="{{ route('portal.capitan.sesiones.store') }}" class="portal-form">
                @csrf
                <label>
                    <span>Grupo</span>
                    <select name="juego_id" required>
                        @foreach ($grupos as $grupo)
                            <option value="{{ $grupo->id }}">{{ $grupo->nombre }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Fecha de la reunión</span>
                    <input type="date" name="fecha" value="{{ now()->toDateString() }}" required>
                </label>
                <label>
                    <span>Notas (opcional)</span>
                    <textarea name="notas" rows="2"></textarea>
                </label>
                <button type="submit" class="portal-btn">Abrir lista de hoy</button>
            </form>
        </div>

        <h2>Sesiones recientes</h2>
        @if ($sesiones->isEmpty())
            <p>Todavía no hay sesiones este semestre.</p>
        @else
            <ul class="portal-lista-asistencia">
                @foreach ($sesiones as $sesion)
                    <li>
                        <strong>{{ $sesion->juego->nombre }}</strong> · {{ $sesion->fecha->format('d/m/Y') }}
                        <div style="margin-top:0.5rem">
                            <a class="portal-btn portal-btn--secundario" href="{{ route('portal.capitan.lista.edit', $sesion) }}">Editar lista</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    @endif
@endsection
