<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Portal') · {{ config('club.nombre') }}</title>
    <link rel="stylesheet" href="{{ asset('css/jaguares.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}?v={{ filemtime(public_path('css/portal.css')) }}">
</head>
<body class="portal-body">
    @auth
        <header class="portal-header">
            <div class="portal-header__interior">
                <a class="portal-marca" href="{{ route('portal.dashboard') }}">Portal miembros</a>
                <nav class="portal-nav" aria-label="Portal">
                    <a href="{{ route('portal.dashboard') }}" @class(['activo' => request()->routeIs('portal.dashboard')])>Inicio</a>
                    <a href="{{ route('portal.asistencia.index') }}" @class(['activo' => request()->routeIs('portal.asistencia.*')])>Mi asistencia</a>
                    <a href="{{ route('portal.convalidacion.index') }}" @class(['activo' => request()->routeIs('portal.convalidacion.*')])>Convalidación</a>
                    @if (auth()->user()->juegosComoCapitan()->exists())
                        <a href="{{ route('portal.capitan.sesiones.index') }}" @class(['activo' => request()->routeIs('portal.capitan.*')])>Pasar lista</a>
                    @endif
                    <a href="{{ route('portal.perfil.edit') }}">Perfil</a>
                    <a href="{{ route('inicio') }}">Sitio público</a>
                </nav>
                <form method="post" action="{{ route('portal.logout') }}" style="margin-left:auto">
                    @csrf
                    <button type="submit" class="portal-btn portal-btn--secundario">Salir</button>
                </form>
            </div>
        </header>
    @endauth

    <main class="portal-main">
        @if (session('estado'))
            <p class="portal-alerta" role="status">{{ session('estado') }}</p>
        @endif

        @if ($errors->any())
            <div class="portal-alerta" role="alert">
                <ul style="margin:0.25rem 0 0;padding-left:1.2rem">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('contenido')
    </main>
</body>
</html>
