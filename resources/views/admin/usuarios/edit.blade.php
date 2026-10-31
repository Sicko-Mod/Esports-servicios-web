@extends('admin.layouts.app')

@section('titulo', 'Cambiar rol')

@section('contenido')
    <h1 class="admin-titulo">Rol de {{ $usuario->name }}</h1>
    <p>{{ $usuario->email }}</p>

    <form method="post" action="{{ route('admin.usuarios.update', $usuario) }}" class="admin-form">
        @csrf
        @method('PUT')
        <label>
            <span>Rol</span>
            <select name="rol" required>
                @foreach ($roles as $rol)
                    <option value="{{ $rol->value }}" @selected(old('rol', $usuario->rol->value) === $rol->value)>{{ $rol->etiqueta() }}</option>
                @endforeach
            </select>
        </label>
        <p style="font-size:0.9rem;color:var(--jg-tenue)">
            Administrador: gestiona el panel. Superadministrador: además asigna roles. Los capitanes se asignan desde Grupos.
        </p>
        <button type="submit" class="admin-btn">Guardar rol</button>
        <a class="admin-btn admin-btn--secundario" href="{{ route('admin.usuarios.index') }}">Cancelar</a>
    </form>
@endsection
