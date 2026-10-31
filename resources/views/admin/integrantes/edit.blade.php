@extends('admin.layouts.app')

@section('titulo', 'Editar integrante')

@section('contenido')
    <h1 class="admin-titulo">Editar: {{ $integrante->gamer_tag }}</h1>

    @if ($integrante->sincronizadoConPortal())
        <p class="admin-alerta">Vinculado al portal: el tag y el grupo se actualizan cuando el miembro guarda su perfil. Acá podés cambiar el <strong>nivel (tier)</strong>.</p>
    @endif

    <form method="post" action="{{ route('admin.integrantes.update', $integrante) }}" class="admin-form">
        @csrf
        @method('PUT')
        @include('admin.integrantes._form', ['integrante' => $integrante, 'juegos' => $juegos, 'juegoId' => $integrante->juego_id])
        <button type="submit" class="admin-btn">Guardar cambios</button>
        <a class="admin-btn admin-btn--secundario" href="{{ route('admin.integrantes.index', ['juego_id' => $integrante->juego_id]) }}">Cancelar</a>
    </form>
@endsection
