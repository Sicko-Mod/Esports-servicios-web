@php
    $torneo = $torneo ?? new \App\Models\Torneo;
@endphp

<label>
    <span>Nombre del torneo</span>
    <input type="text" name="nombre" value="{{ old('nombre', $torneo->nombre) }}" required>
</label>

<label>
    <span>Grupo (opcional)</span>
    <select name="juego_id">
        <option value="">— General del club —</option>
        @foreach ($juegos as $juego)
            <option value="{{ $juego->id }}" @selected((int) old('juego_id', $torneo->juego_id) === $juego->id)>{{ $juego->nombre }}</option>
        @endforeach
    </select>
</label>

<label>
    <span>Descripción</span>
    <textarea name="descripcion">{{ old('descripcion', $torneo->descripcion) }}</textarea>
</label>

<label>
    <span>Fecha de inicio</span>
    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $torneo->fecha_inicio?->format('Y-m-d')) }}" required>
</label>

<label>
    <span>Fecha de fin (opcional)</span>
    <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $torneo->fecha_fin?->format('Y-m-d')) }}">
</label>

<label>
    <span>Modalidad</span>
    <select name="modalidad" required>
        <option value="individual" @selected(old('modalidad', $torneo->modalidad ?? 'individual') === 'individual')>Individual</option>
        <option value="equipo" @selected(old('modalidad', $torneo->modalidad) === 'equipo')>Equipo</option>
    </select>
</label>

<div class="admin-form__fila">
    <input type="hidden" name="abierto_externos" value="0">
    <input type="checkbox" name="abierto_externos" id="abierto_externos" value="1" @checked(old('abierto_externos', $torneo->abierto_externos))>
    <label for="abierto_externos" style="margin:0">Abierto a personas fuera del club</label>
</div>

<label>
    <span>Enlace de inscripción (opcional)</span>
    <input type="url" name="enlace_inscripcion" value="{{ old('enlace_inscripcion', $torneo->enlace_inscripcion) }}">
</label>
