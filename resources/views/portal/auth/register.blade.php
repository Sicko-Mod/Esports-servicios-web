@extends('portal.layouts.app')

@section('titulo', 'Registro')

@section('contenido')
    <div class="portal-auth portal-tarjeta">
        <h1>Registro de miembros</h1>
        <p>Solo correos <strong>&#64;{{ config('club.correo_institucional_dominio') }}</strong>. Después completás tu perfil y elegís tu grupo.</p>

        <form method="post" action="{{ route('portal.register.store') }}" class="portal-form">
            @csrf
            <label>
                <span>Nombre completo</span>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </label>
            <label>
                <span>Correo institucional</span>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            </label>
            <label>
                <span>Contraseña</span>
                <input type="password" name="password" required autocomplete="new-password">
            </label>
            <label>
                <span>Confirmar contraseña</span>
                <input type="password" name="password_confirmation" required autocomplete="new-password">
            </label>
            <button type="submit" class="portal-btn">Crear cuenta</button>
        </form>

        <p style="margin-top:1.25rem">¿Ya tenés cuenta? <a href="{{ route('portal.login') }}">Iniciar sesión</a></p>
    </div>
@endsection
