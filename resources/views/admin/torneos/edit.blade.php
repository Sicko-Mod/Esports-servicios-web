@extends('admin.layouts.app')

@section('titulo', 'Editar torneo')

@section('contenido')
    <h1 class="admin-titulo">Editar: {{ $torneo->nombre }}</h1>

    <form method="post" action="{{ route('admin.torneos.update', $torneo) }}" class="admin-form">
        @csrf
        @method('PUT')
        @include('admin.torneos._form', compact('torneo', 'juegos'))
        <button type="submit" class="admin-btn">Guardar cambios</button>
        <a class="admin-btn admin-btn--secundario" href="{{ route('admin.torneos.index') }}">Cancelar</a>
    </form>
@endsection
