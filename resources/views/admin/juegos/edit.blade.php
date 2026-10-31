@extends('admin.layouts.app')

@section('titulo', 'Editar grupo')

@section('contenido')
    <h1 class="admin-titulo">Editar: {{ $juego->nombre }}</h1>

    <form method="post" action="{{ route('admin.juegos.update', $juego) }}" class="admin-form">
        @csrf
        @method('PUT')
        @include('admin.juegos._form', ['juego' => $juego, 'capitanesCandidatos' => $capitanesCandidatos])
        <button type="submit" class="admin-btn">Guardar cambios</button>
        <a class="admin-btn admin-btn--secundario" href="{{ route('admin.juegos.index') }}">Cancelar</a>
    </form>
@endsection
