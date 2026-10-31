@extends('admin.layouts.app')

@section('titulo', 'Nuevo integrante')

@section('contenido')
    <h1 class="admin-titulo">Agregar al roster</h1>

    <form method="post" action="{{ route('admin.integrantes.store') }}" class="admin-form">
        @csrf
        @include('admin.integrantes._form', compact('integrante', 'juegos', 'juegoId'))
        <button type="submit" class="admin-btn">Guardar</button>
        <a class="admin-btn admin-btn--secundario" href="{{ route('admin.integrantes.index') }}">Cancelar</a>
    </form>
@endsection
