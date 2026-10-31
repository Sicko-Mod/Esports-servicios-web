@extends('admin.layouts.app')

@section('titulo', 'Editar semestre')

@section('contenido')
    <h1 class="admin-titulo">Editar: {{ $semestre->nombre }}</h1>

    <form method="post" action="{{ route('admin.semestres.update', $semestre) }}" class="admin-form">
        @csrf
        @method('PUT')
        @include('admin.semestres._form', ['semestre' => $semestre])
        <button type="submit" class="admin-btn">Guardar cambios</button>
        <a class="admin-btn admin-btn--secundario" href="{{ route('admin.semestres.index') }}">Cancelar</a>
    </form>
@endsection
