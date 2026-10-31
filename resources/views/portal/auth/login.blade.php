@extends('portal.layouts.app')

@section('titulo', 'Iniciar sesión')

@section('contenido')
    <div class="portal-auth portal-tarjeta">
        <h1>Iniciar sesión</h1>
        <p>Usá tu correo institucional <strong>&#64;{{ config('club.correo_institucional_dominio') }}</strong>.</p>

        <form method="post" action="{{ route('portal.login.store') }}" class="portal-form">
            @csrf
            <label>
                <span>Correo</span>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            </label>
            <label>
                <span>Contraseña</span>
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <label style="display:flex;align-items:center;gap:0.5rem">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                <span style="margin:0">Recordarme</span>
            </label>
            <button type="submit" class="portal-btn">Entrar</button>
        </form>

        <p style="margin-top:1.25rem">¿Primera vez? <a href="{{ route('portal.register') }}">Crear cuenta</a></p>
        <p><a href="{{ route('inicio') }}">← Volver al sitio público</a></p>
    </div>
@endsection
