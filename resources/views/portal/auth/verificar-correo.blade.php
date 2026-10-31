@extends('portal.layouts.app')

@section('titulo', 'Confirmá tu correo')

@section('contenido')
    <div class="portal-auth portal-tarjeta">
        <h1>Confirmá tu correo</h1>
        <p>Te enviamos un enlace a <strong>{{ auth()->user()->email }}</strong>. Abrilo para activar tu cuenta y completar tu perfil.</p>
        <p>Si no lo ves en unos minutos, revisá la carpeta de spam o pedí uno nuevo.</p>

        <form method="post" action="{{ route('portal.verification.send') }}">
            @csrf
            <button type="submit" class="portal-btn">Reenviar enlace</button>
        </form>
    </div>
@endsection
