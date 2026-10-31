@php
    $integrante = $integrante ?? new \App\Models\Integrante;
    $juegoId = old('juego_id', $juegoId ?? $integrante->juego_id);
@endphp

<label>
    <span>Grupo</span>
    <select name="juego_id" required>
        <option value="">— Elegir —</option>
        @foreach ($juegos as $juego)
            <option value="{{ $juego->id }}" @selected((int) $juegoId === $juego->id)>{{ $juego->nombre }}</option>
        @endforeach
    </select>
</label>

<label>
    <span>Gamer tag</span>
    <input type="text" name="gamer_tag" value="{{ old('gamer_tag', $integrante->gamer_tag) }}" required>
</label>

<label>
    <span>Nivel en el roster</span>
    <select name="nivel" required>
        @foreach (\App\Enums\Nivel::cases() as $nivel)
            <option value="{{ $nivel->value }}" @selected(old('nivel', $integrante->nivel?->value) === $nivel->value)>
                {{ $nivel->titulo() }}@if ($nivel->nota()) — {{ $nivel->nota() }}@endif
            </option>
        @endforeach
    </select>
</label>

<label>
    <span>Nombre público (opcional, solo con permiso y si es mayor de edad)</span>
    <input type="text" name="nombre_publico" value="{{ old('nombre_publico', $integrante->nombre_publico) }}">
</label>
