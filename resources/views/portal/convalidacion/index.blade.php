@extends('portal.layouts.app')

@section('titulo', 'Convalidación')

@section('contenido')
    <h1>Convalidación de créditos</h1>

    @if ($semestre)
        <p>Semestre activo: <strong>{{ $semestre->nombre }}</strong></p>
    @endif

    <div class="portal-tarjeta">
        <h2 style="margin-top:0">Tu situación</h2>

        @if ($evaluacion['resumen'])
            <p>Asistencia: <strong>{{ $evaluacion['resumen']['porcentaje'] }}%</strong>
                ({{ $evaluacion['resumen']['presentes'] }}/{{ $evaluacion['resumen']['total_sesiones'] }} sesiones)</p>
            <p>Horas equivalentes al aprobar: <strong>{{ config('club.horas_por_semestre') }}</strong> h laborales (según reglamento del club).</p>
        @endif

        @if ($evaluacion['elegible'])
            <p>Podés enviar <strong>una solicitud</strong> por semestre.</p>
            <form method="post" action="{{ route('portal.convalidacion.store') }}" class="portal-form">
                @csrf
                <label>
                    <span>Comentario opcional para el club</span>
                    <textarea name="notas_miembro" rows="3" maxlength="2000">{{ old('notas_miembro') }}</textarea>
                </label>
                <button type="submit" class="portal-btn">Enviar solicitud</button>
            </form>
        @else
            <ul>
                @foreach ($evaluacion['motivos'] as $motivo)
                    <li>{{ $motivo }}</li>
                @endforeach
            </ul>
            @isset($evaluacion['solicitud'])
                <p>Estado actual: <strong>{{ $evaluacion['solicitud']->estado->etiqueta() }}</strong>
                    · enviada el {{ $evaluacion['solicitud']->enviada_at->format('d/m/Y') }}</p>
            @endisset
        @endif
    </div>

    @if ($historial->isNotEmpty())
        <h2>Historial</h2>
        <table class="admin-tabla" style="width:100%">
            <thead>
                <tr>
                    <th>Semestre</th>
                    <th>Estado</th>
                    <th>%</th>
                    <th>Enviada</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($historial as $fila)
                    <tr>
                        <td>{{ $fila->semestre->nombre }}</td>
                        <td>{{ $fila->estado->etiqueta() }}</td>
                        <td>{{ $fila->porcentaje_asistencia }}%</td>
                        <td>{{ $fila->enviada_at->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p style="margin-top:1.5rem;font-size:0.9rem;color:var(--jg-tenue)">
        Más información en la <a href="{{ route('convalidacion') }}">página pública de convalidación</a>.
    </p>
@endsection
