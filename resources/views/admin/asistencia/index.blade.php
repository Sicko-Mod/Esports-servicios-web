@extends('admin.layouts.app')

@section('titulo', 'Asistencia')

@section('contenido')
    <h1 class="admin-titulo">Reporte de asistencia</h1>

    <div class="admin-acciones">
        <form method="get" action="{{ route('admin.asistencia.index') }}" class="admin-form" style="flex:1;display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end">
            <label style="margin:0">
                <span>Semestre</span>
                <select name="semestre_id" onchange="this.form.submit()">
                    @foreach ($semestres as $opcion)
                        <option value="{{ $opcion->id }}" @selected($semestre?->id === $opcion->id)>{{ $opcion->nombre }}</option>
                    @endforeach
                </select>
            </label>
            <label style="margin:0">
                <span>Grupo</span>
                <select name="juego_id" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    @foreach ($juegos as $juego)
                        <option value="{{ $juego->id }}" @selected($juegoId === $juego->id)>{{ $juego->nombre }}</option>
                    @endforeach
                </select>
            </label>
        </form>

        @if ($semestre)
            <form method="get" action="{{ route('admin.asistencia.exportar') }}">
                <input type="hidden" name="semestre_id" value="{{ $semestre->id }}">
                @if ($juegoId)
                    <input type="hidden" name="juego_id" value="{{ $juegoId }}">
                @endif
                <button type="submit" class="admin-btn">Detalle por sesión (CSV)</button>
            </form>
        @endif
    </div>

    <p style="font-size:0.9rem;color:var(--jg-tenue)">
        El CSV trae una fila por miembro y sesión para compararlo con el Excel del piloto. Incluye nombres y correos:
        es de uso interno de la directiva, no se envía a Registro Académico.
        Umbral: {{ config('club.umbral_asistencia') }} %.
        Justificados: {{ config('club.justificados_descuentan_del_total') ? 'se descuentan del total de sesiones' : 'cuentan como ausencia' }}.
    </p>

    @if (! $semestre)
        <p>No hay semestre activo. Creá uno en Semestres.</p>
    @endif

    @foreach ($reportes as $reporte)
        <h2 style="font-size:1.15rem;margin-top:1.75rem">
            {{ $reporte['juego']->nombre }}
            <small style="color:var(--jg-tenue)">· {{ $reporte['sesiones']->count() }} sesiones</small>
        </h2>

        @if ($reporte['filas']->isEmpty())
            <p>Sin miembros en este grupo.</p>
        @else
            <table class="admin-tabla">
                <thead>
                    <tr>
                        <th>Miembro</th>
                        <th>Presentes</th>
                        <th>Ausentes</th>
                        <th>Justificados</th>
                        <th>Sin marca</th>
                        <th>%</th>
                        <th>Cumple</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reporte['filas'] as $fila)
                        <tr>
                            <td>{{ $fila['miembro']->gamer_tag }}<br><small>{{ $fila['miembro']->user?->email }}</small></td>
                            <td>{{ $fila['presentes'] }}</td>
                            <td>{{ $fila['ausentes'] }}</td>
                            <td>{{ $fila['justificados'] }}</td>
                            <td>{{ $fila['sin_marca'] }}</td>
                            <td>{{ $fila['porcentaje'] }}%</td>
                            <td>{{ $fila['cumple_umbral'] ? 'Sí' : 'No' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach
@endsection
