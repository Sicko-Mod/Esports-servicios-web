@extends('portal.layouts.app')

@section('titulo', 'Mi perfil')

@section('contenido')
    <h1>Perfil de miembro</h1>
    <p>Estos datos son confidenciales. Tu <strong>gamer tag</strong> y grupo se publican en el roster del sitio al guardar (el tier lo ajusta un administrador).</p>

    <form method="post" action="{{ route('portal.perfil.update') }}" class="portal-form">
        @csrf
        @method('PUT')

        <label>
            <span>Gamer tag</span>
            <input type="text" name="gamer_tag" value="{{ old('gamer_tag', $miembro->gamer_tag) }}" required>
        </label>

        <label>
            <span>Grupo / juego</span>
            <select name="juego_id" required>
                <option value="">— Elegir —</option>
                @foreach ($juegos as $juego)
                    <option value="{{ $juego->id }}" @selected((int) old('juego_id', $miembro->juego_id) === $juego->id)>{{ $juego->nombre }}</option>
                @endforeach
            </select>
        </label>

        <label>
            <span>CIF / carné</span>
            <input type="text" name="cif" value="{{ old('cif', $miembro->cif) }}" required autocomplete="off">
        </label>

        <label>
            <span>Carrera</span>
            <input type="text" name="carrera" value="{{ old('carrera', $miembro->carrera) }}" required>
        </label>

        <label>
            <span>Teléfono</span>
            <input type="text" name="telefono" value="{{ old('telefono', $miembro->telefono) }}" required>
        </label>

        <label>
            <span>Fecha de nacimiento</span>
            <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $miembro->fecha_nacimiento?->format('Y-m-d')) }}" required>
        </label>

        <div class="portal-tarjeta" id="datos-tutor">
            <h2 style="margin-top:0;font-size:1.1rem">Menores de edad</h2>
            <p style="font-size:0.9rem;color:var(--jg-tenue)">Si tenés menos de 18 años, completá los datos del tutor y el consentimiento.</p>
            <label>
                <span>Nombre del tutor</span>
                <input type="text" name="tutor_nombre" value="{{ old('tutor_nombre', $miembro->tutor_nombre) }}">
            </label>
            <label>
                <span>Teléfono del tutor</span>
                <input type="text" name="tutor_telefono" value="{{ old('tutor_telefono', $miembro->tutor_telefono) }}">
            </label>
            <label style="display:flex;align-items:flex-start;gap:0.5rem">
                <input type="checkbox" name="tutor_consentimiento" value="1" @checked(old('tutor_consentimiento'))>
                <span>Confirmo que el tutor autoriza la participación y el tratamiento de estos datos.</span>
            </label>
        </div>

        <button type="submit" class="portal-btn">Guardar perfil</button>
    </form>
@endsection
