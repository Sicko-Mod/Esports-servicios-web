@extends('layouts.app')

@section('titulo', $juego->nombre)

@section('contenido')
    <p><a href="{{ route('equipos.index') }}">← Todos los equipos</a></p>

    @unless ($juego->activo)
        <p class="jg-vacio">Este grupo no está activo este semestre.</p>
    @endunless

    @if ($juego->descripcion)
        <p>{!! nl2br(e($juego->descripcion)) !!}</p>
    @endif

    @if ($juego->dia_legible || $juego->hora_legible || $juego->lugar || $juego->capitan)
        <div class="jg-ficha">
            <dl class="jg-juego__datos">
                @if ($juego->dia_legible || $juego->hora_legible)
                    <dt>Se reúne</dt>
                    <dd>{{ trim($juego->dia_legible . ' ' . $juego->hora_legible) }}</dd>
                @endif
                @if ($juego->lugar)
                    <dt>Lugar</dt>
                    <dd>{{ $juego->lugar }}</dd>
                @endif
                @if ($juego->capitan)
                    <dt>Capitán</dt>
                    <dd>{{ $juego->capitan }}</dd>
                @endif
            </dl>
        </div>
    @endif

    <h2>Roster</h2>
    <x-roster :roster="$roster" />
@endsection
