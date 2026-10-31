@extends('admin.layouts.app')

@section('titulo', 'Convalidaciones')

@section('contenido')
    <h1 class="admin-titulo">Solicitudes de convalidación</h1>

    <div class="admin-acciones">
        <form method="get" action="{{ route('admin.convalidaciones.index') }}" class="admin-form" style="flex:1;display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end">
            <label style="margin:0">
                <span>Semestre</span>
                <select name="semestre_id" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    @foreach ($semestres as $semestre)
                        <option value="{{ $semestre->id }}" @selected($semestreId === $semestre->id)>{{ $semestre->nombre }}</option>
                    @endforeach
                </select>
            </label>
            <label style="margin:0">
                <span>Estado (solo listado)</span>
                <select name="estado" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    @foreach ($estados as $estado)
                        <option value="{{ $estado->value }}" @selected($estadoFiltro === $estado->value)>{{ $estado->etiqueta() }}</option>
                    @endforeach
                </select>
            </label>
        </form>

        @if ($semestreId)
            <form method="get" action="{{ route('admin.convalidaciones.exportar') }}">
                <input type="hidden" name="semestre_id" value="{{ $semestreId }}">
                <input type="hidden" name="tipo" value="informe">
                <button type="submit" class="admin-btn">Informe completo (CSV)</button>
            </form>
            <form method="get" action="{{ route('admin.convalidaciones.exportar') }}">
                <input type="hidden" name="semestre_id" value="{{ $semestreId }}">
                <input type="hidden" name="tipo" value="registro">
                <button type="submit" class="admin-btn admin-btn--secundario">Solo aprobadas (Registro)</button>
            </form>
        @endif
    </div>

    @if ($resumenInforme)
        <div class="admin-alerta" style="margin-bottom:1.25rem">
            <strong>Resumen del semestre</strong> (todas las solicitudes, sin filtro de tabla)
            <ul style="margin:0.5rem 0 0;padding-left:1.2rem">
                <li>Total solicitudes: {{ $resumenInforme['total'] }}</li>
                <li>Aprobadas: {{ $resumenInforme['aprobadas'] }} · Rechazadas: {{ $resumenInforme['rechazadas'] }}
                    · En revisión: {{ $resumenInforme['en_revision'] }} · Enviadas: {{ $resumenInforme['enviadas'] }}</li>
                <li><strong>Horas otorgadas</strong> (aprobadas): {{ $resumenInforme['horas_aprobadas'] }} h</li>
                <li>Horas en trámite (enviadas + en revisión): {{ $resumenInforme['horas_en_tramite'] }} h</li>
                <li>Horas equivalentes en solicitudes rechazadas (no otorgadas): {{ $resumenInforme['horas_rechazadas_no_otorgadas'] }} h</li>
            </ul>
        </div>
    @endif

    <p style="font-size:0.9rem;color:var(--jg-tenue)">
        El <strong>informe completo</strong> incluye resumen de horas y el detalle de cada solicitud (aprobadas, rechazadas y pendientes), sin nombres — solo CIF.
        El archivo <strong>solo aprobadas</strong> es el envío mínimo a Registro Académico.
    </p>

    @if ($solicitudes->isEmpty())
        <p>No hay solicitudes con este filtro.</p>
    @else
        <table class="admin-tabla">
            <thead>
                <tr>
                    <th>Miembro</th>
                    <th>Grupo</th>
                    <th>Semestre</th>
                    <th>%</th>
                    <th>Horas</th>
                    <th>Estado</th>
                    <th>Enviada</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($solicitudes as $solicitud)
                    <tr>
                        <td>{{ $solicitud->miembro->gamer_tag }}<br><small>{{ $solicitud->miembro->user->email }}</small></td>
                        <td>{{ $solicitud->miembro->juego?->nombre ?? '—' }}</td>
                        <td>{{ $solicitud->semestre->nombre }}</td>
                        <td>{{ $solicitud->porcentaje_asistencia }}%</td>
                        <td>{{ $solicitud->horas_equivalentes }}</td>
                        <td>{{ $solicitud->estado->etiqueta() }}</td>
                        <td>{{ $solicitud->enviada_at->format('d/m/Y') }}</td>
                        <td>
                            <a class="admin-btn admin-btn--secundario" href="{{ route('admin.convalidaciones.edit', $solicitud) }}">Revisar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
