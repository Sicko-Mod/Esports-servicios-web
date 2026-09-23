@extends('layouts.app')

@section('titulo', 'Equipos')

@section('contenido')
    <p>Estos son los grupos activos este semestre. Entrá a cualquiera para ver su roster completo, ordenado por nivel.</p>

    <x-lista-juegos :juegos="$juegos" />

    <p>¿Jugás algo que no está en la lista? Escribinos: si hay suficiente gente interesada, se abre el grupo.</p>
@endsection
