@extends('portal.layouts.app')

@section('titulo', 'Inicio')

@section('contenido')
    <h1>Hola, {{ auth()->user()->name }}</h1>

    @if ($miembro?->juego)
        <p>Grupo: <strong>{{ $miembro->juego->nombre }}</strong> · Tag: <strong>{{ $miembro->gamer_tag }}</strong></p>
    @endif

    @if ($semestre && $resumen)
        <div class="portal-tarjeta">
            <h2 style="margin-top:0">{{ $semestre->nombre }}</h2>
            <p class="portal-metrica">{{ $resumen['porcentaje'] }}%</p>
            <p>Asistencia: {{ $resumen['presentes'] }} de {{ $resumen['total_sesiones'] }} sesiones del grupo.</p>
            <p>Umbral para convalidación: {{ config('club.umbral_asistencia') }}%.
                @if ($resumen['cumple_umbral'])
                    <strong style="color:var(--jg-cian)">Vas en camino.</strong>
                @else
                    Seguí sumando presencias.
                @endif
            </p>
            <a class="portal-btn portal-btn--secundario" href="{{ route('portal.asistencia.index') }}">Ver detalle</a>
            <a class="portal-btn portal-btn--secundario" href="{{ route('portal.convalidacion.index') }}">Convalidación</a>
        </div>
    @else
        <div class="portal-tarjeta">
            <p>Todavía no hay un semestre activo configurado. El equipo administrativo lo habilitará pronto.</p>
        </div>
    @endif

    @if ($gruposCapitan->isNotEmpty())
        <div class="portal-tarjeta">
            <h2 style="margin-top:0">Sos capitán</h2>
            <ul>
                @foreach ($gruposCapitan as $grupo)
                    <li>{{ $grupo->nombre }}</li>
                @endforeach
            </ul>
            <a class="portal-btn" href="{{ route('portal.capitan.sesiones.index') }}">Pasar lista</a>
        </div>
    @endif
@endsection
