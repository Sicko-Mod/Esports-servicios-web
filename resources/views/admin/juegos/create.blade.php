@extends('admin.layouts.app')

@section('titulo', 'Nuevo grupo')

@section('contenido')
    <h1 class="admin-titulo">Nuevo grupo</h1>

    <form method="post" action="{{ route('admin.juegos.store') }}" class="admin-form">
        @csrf
        @include('admin.juegos._form', ['juego' => $juego])
        <button type="submit" class="admin-btn">Guardar</button>
        <a class="admin-btn admin-btn--secundario" href="{{ route('admin.juegos.index') }}">Cancelar</a>
    </form>
@endsection
