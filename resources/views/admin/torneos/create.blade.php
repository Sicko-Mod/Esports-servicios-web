@extends('admin.layouts.app')

@section('titulo', 'Nuevo torneo')

@section('contenido')
    <h1 class="admin-titulo">Nuevo torneo</h1>

    <form method="post" action="{{ route('admin.torneos.store') }}" class="admin-form">
        @csrf
        @include('admin.torneos._form', compact('torneo', 'juegos'))
        <button type="submit" class="admin-btn">Guardar</button>
        <a class="admin-btn admin-btn--secundario" href="{{ route('admin.torneos.index') }}">Cancelar</a>
    </form>
@endsection
