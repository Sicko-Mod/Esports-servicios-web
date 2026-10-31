@php
    $juego = $juego ?? new \App\Models\Juego;
@endphp

<label>
    <span>Nombre del grupo</span>
    <input type="text" name="nombre" value="{{ old('nombre', $juego->nombre) }}" required>
</label>

<label>
    <span>Descripción (opcional)</span>
    <textarea name="descripcion">{{ old('descripcion', $juego->descripcion) }}</textarea>
</label>

<label>
    <span>Día de reunión</span>
    <select name="dia">
        <option value="">— Sin definir —</option>
        @foreach (\App\Models\Juego::DIAS as $valor => $etiqueta)
            <option value="{{ $valor }}" @selected(old('dia', $juego->dia) === $valor)>{{ $etiqueta }}</option>
        @endforeach
    </select>
</label>

<label>
    <span>Hora</span>
    <input type="time" name="hora" value="{{ old('hora', $juego->hora ? \Carbon\Carbon::parse($juego->hora)->format('H:i') : '') }}">
</label>

<label>
    <span>Lugar</span>
    <input type="text" name="lugar" value="{{ old('lugar', $juego->lugar) }}">
</label>

<label>
    <span>Capitán (gamer tag en el sitio público)</span>
    <input type="text" name="capitan" value="{{ old('capitan', $juego->capitan) }}">
</label>

@if (isset($capitanesCandidatos))
    <label>
        <span>Capitán en el portal (pasa lista)</span>
        <select name="capitan_user_id">
            <option value="">— Sin asignar —</option>
            @foreach ($capitanesCandidatos as $usuario)
                <option value="{{ $usuario->id }}" @selected((int) old('capitan_user_id', $juego->capitan_user_id) === $usuario->id)>
                    {{ $usuario->name }} ({{ $usuario->miembro?->gamer_tag ?? $usuario->email }})
                </option>
            @endforeach
        </select>
    </label>
    <p style="font-size:0.85rem;color:var(--jg-tenue)">Solo aparecen miembros con perfil completo en este grupo.</p>
@endif

<label>
    <span>Orden en listados</span>
    <input type="number" name="orden" min="0" value="{{ old('orden', $juego->orden ?? 0) }}">
</label>

<div class="admin-form__fila">
    <input type="hidden" name="activo" value="0">
    <input type="checkbox" name="activo" id="activo" value="1" @checked(old('activo', $juego->activo))>
    <label for="activo" style="margin:0">Grupo activo este semestre (visible en el sitio público)</label>
</div>
