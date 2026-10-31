<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Panel') · {{ config('club.nombre') }}</title>
    <link rel="stylesheet" href="{{ asset('css/jaguares.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
</head>
<body class="admin-body">
    <aside class="admin-nav" aria-label="Administración">
        <a class="admin-nav__marca" href="{{ route('admin.dashboard') }}">Panel</a>
        <ul>
            <li><a href="{{ route('admin.juegos.index') }}" @class(['activo' => request()->routeIs('admin.juegos.*')])>Grupos</a></li>
            <li><a href="{{ route('admin.integrantes.index') }}" @class(['activo' => request()->routeIs('admin.integrantes.*')])>Roster</a></li>
            <li><a href="{{ route('admin.torneos.index') }}" @class(['activo' => request()->routeIs('admin.torneos.*')])>Torneos</a></li>
            <li><a href="{{ route('admin.semestres.index') }}" @class(['activo' => request()->routeIs('admin.semestres.*')])>Semestres</a></li>
            <li><a href="{{ route('admin.convalidaciones.index') }}" @class(['activo' => request()->routeIs('admin.convalidaciones.*')])>Convalidaciones</a></li>
            <li><a href="{{ route('admin.asistencia.index') }}" @class(['activo' => request()->routeIs('admin.asistencia.*')])>Asistencia</a></li>
            <li><a href="{{ route('admin.usuarios.index') }}" @class(['activo' => request()->routeIs('admin.usuarios.*')])>Usuarios</a></li>
        </ul>
        <div class="admin-nav__pie">
            <p><a href="{{ route('inicio') }}">Ver sitio público</a></p>
            <form method="post" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="admin-btn admin-btn--secundario">Cerrar sesión</button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        @if (session('estado'))
            <p class="admin-alerta" role="status">{{ session('estado') }}</p>
        @endif

        @if ($errors->any())
            <div class="admin-errores" role="alert">
                <strong>Revisá los datos:</strong>
                <ul>
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
