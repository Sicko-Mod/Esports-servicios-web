@extends('admin.layouts.app')

@section('titulo', 'Nuevo semestre')

@section('contenido')
    <h1 class="admin-titulo">Nuevo semestre</h1>

    <form method="post" action="{{ route('admin.semestres.store') }}" class="admin-form">
        @csrf
        @include('admin.semestres._form', ['semestre' => $semestre])
        <button type="submit" class="admin-btn">Guardar</button>
        <a class="admin-btn admin-btn--secundario" href="{{ route('admin.semestres.index') }}">Cancelar</a>
    </form>
@endsection
