@extends('admin.layouts.app')

@section('titulo', 'Revisar solicitud')

@section('contenido')
    <h1 class="admin-titulo">Solicitud de convalidación</h1>

    <div class="admin-tabla" style="margin-bottom:1.5rem">
        <p><strong>Miembro:</strong> {{ $solicitud->miembro->gamer_tag }} ({{ $solicitud->miembro->user->name }})</p>
        <p><strong>Correo:</strong> {{ $solicitud->miembro->user->email }}</p>
        <p><strong>CIF:</strong> {{ $solicitud->miembro->cif }}</p>
        <p><strong>Grupo:</strong> {{ $solicitud->miembro->juego?->nombre }}</p>
        <p><strong>Semestre:</strong> {{ $solicitud->semestre->nombre }}</p>
        <p><strong>Asistencia al enviar:</strong> {{ $solicitud->porcentaje_asistencia }}%
            ({{ $solicitud->sesiones_presentes }}/{{ $solicitud->sesiones_total }})</p>
        <p><strong>Horas equivalentes:</strong> {{ $solicitud->horas_equivalentes }}</p>
        <p><strong>Enviada:</strong> {{ $solicitud->enviada_at->format('d/m/Y H:i') }}</p>
        @if ($solicitud->notas_miembro)
            <p><strong>Comentario del miembro:</strong> {{ $solicitud->notas_miembro }}</p>
        @endif
    </div>

    <form method="post" action="{{ route('admin.convalidaciones.update', $solicitud) }}" class="admin-form">
        @csrf
        @method('PUT')

        <label>
            <span>Estado</span>
            <select name="estado" required>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->value }}" @selected(old('estado', $solicitud->estado->value) === $estado->value)>
                        {{ $estado->etiqueta() }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>
            <span>Notas internas (admin)</span>
            <textarea name="notas_admin">{{ old('notas_admin', $solicitud->notas_admin) }}</textarea>
        </label>

        <button type="submit" class="admin-btn">Guardar</button>
        <a class="admin-btn admin-btn--secundario" href="{{ route('admin.convalidaciones.index', ['semestre_id' => $solicitud->semestre_id]) }}">Volver</a>
    </form>
@endsection
