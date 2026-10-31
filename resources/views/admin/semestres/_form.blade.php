@php
    $semestre = $semestre ?? new \App\Models\Semestre;
@endphp

<label>
    <span>Nombre</span>
    <input type="text" name="nombre" value="{{ old('nombre', $semestre->nombre) }}" required placeholder="Ej. Segundo semestre 2026">
</label>

<label>
    <span>Fecha de inicio</span>
    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $semestre->fecha_inicio?->format('Y-m-d')) }}" required>
</label>

<label>
    <span>Fecha de fin</span>
    <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $semestre->fecha_fin?->format('Y-m-d')) }}" required>
</label>

<label>
    <span>Mínimo de sesiones del semestre (opcional)</span>
    <input type="number" name="minimo_sesiones" min="1" value="{{ old('minimo_sesiones', $semestre->minimo_sesiones) }}" placeholder="Definir cuando Registro confirme el conteo">
</label>

<div class="admin-form__fila">
    <input type="hidden" name="activo" value="0">
    <input type="checkbox" name="activo" id="semestre_activo" value="1" @checked(old('activo', $semestre->activo))>
    <label for="semestre_activo" style="margin:0">Semestre activo (solo uno a la vez; usado por asistencia y %)</label>
</div>
