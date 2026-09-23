@extends('layouts.app')

@section('titulo', 'Torneos')

@section('contenido')
    <p>Torneos organizados por el club y competencias donde participamos representando a la UAM.</p>

    <x-lista-torneos
        :torneos="$proximos"
        vacio="No hay torneos programados por ahora. Seguinos en redes para enterarte del próximo." />

    <h2>Historial</h2>
    <p>Torneos que ya se jugaron.</p>

    <x-lista-torneos
        :torneos="$pasados"
        vacio="Todavía no hay torneos en el historial." />
@endsection
