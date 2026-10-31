@extends('portal.layouts.app')

@section('titulo', 'Mi asistencia')

@section('contenido')
    <h1>Mi asistencia</h1>
    <p>{{ $semestre->nombre }} · {{ $miembro->juego->nombre }}</p>

    <div class="portal-tarjeta">
        <p class="portal-metrica">{{ $resumen['porcentaje'] }}%</p>
        <p>{{ $resumen['presentes'] }} presente(s) en {{ $resumen['total_sesiones'] }} sesión(es) registradas.</p>
    </div>

    @if ($historial->isEmpty())
        <p>Aún no hay sesiones con marca para tu grupo.</p>
    @else
        <table class="admin-tabla" style="width:100%">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($historial as $fila)
                    <tr>
                        <td>{{ $fila->sesion->fecha->format('d/m/Y') }}</td>
                        <td>{{ $fila->estado->etiqueta() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
