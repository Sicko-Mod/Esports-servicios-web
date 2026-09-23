<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@hasSection('titulo')@yield('titulo') · @endif{{ config('club.nombre') }}</title>
    <meta name="description" content="Club de Deportes Electrónicos de la Universidad Americana (UAM), Managua.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/jaguares.css') }}?v={{ filemtime(public_path('css/jaguares.css')) }}">
</head>
<body>
    <a class="salto" href="#contenido">Saltar al contenido</a>

    @php
        $menu = [
            ['Inicio', 'inicio', 'inicio'],
            ['Equipos', 'equipos.index', 'equipos.*'],
            ['Torneos', 'torneos.index', 'torneos.*'],
            ['Convalidación', 'convalidacion', 'convalidacion'],
            ['Cómo unirse', 'como-unirse', 'como-unirse'],
            ['Contacto', 'contacto', 'contacto'],
        ];
    @endphp

    <header class="sitio-header">
        <div class="sitio-header__interior">
            <a class="sitio-marca" href="{{ route('inicio') }}">{{ config('club.nombre') }}</a>

            <button class="menu-boton" type="button" aria-expanded="false" aria-controls="menu-principal" data-menu-boton>Menú</button>

            <nav id="menu-principal" class="menu" aria-label="Principal">
                <ul>
                    @foreach ($menu as [$texto, $ruta, $patron])
                        @php $activo = request()->routeIs($patron); @endphp
                        <li>
                            <a href="{{ route($ruta) }}" @class(['activo' => $activo]) @if ($activo) aria-current="page" @endif>{{ $texto }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </header>

    <main id="contenido">
        @yield('hero')

        <div class="contenedor contenido">
            @hasSection('titulo')
                <h1 class="pagina-titulo">@yield('titulo')</h1>
            @endif

            @yield('contenido')
        </div>
    </main>

    <footer class="sitio-pie">
        <div class="sitio-pie__interior">
            <p>Club de Deportes Electrónicos · {{ config('club.universidad') }} · {{ now()->year }}</p>
            <ul>
                <li><a href="{{ route('privacidad') }}">Política de privacidad</a></li>
                <li><a href="{{ route('contacto') }}">Contacto</a></li>
            </ul>
        </div>
    </footer>

    <script>
        document.querySelectorAll('[data-menu-boton]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                var abierto = boton.getAttribute('aria-expanded') === 'true';
                boton.setAttribute('aria-expanded', abierto ? 'false' : 'true');
                document.getElementById('menu-principal').classList.toggle('abierto', !abierto);
            });
        });
    </script>
</body>
</html>
