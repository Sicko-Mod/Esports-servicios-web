@extends('layouts.app')

@section('hero')
    <section class="jg-hero">
        <div class="jg-hero__interior">
            <p class="jg-hero__kicker">{{ config('club.universidad') }}</p>
            <h1 class="jg-hero__titulo">Jaguares<br><em>E-Sports</em></h1>
            <p class="jg-hero__bajada">El club de deportes electrónicos de la UAM. Cinco juegos, equipos que representan a la universidad y torneos abiertos. Competir acá también cuenta para tus créditos de vida universitaria.</p>
            <div class="jg-acciones">
                <a class="jg-boton" href="{{ route('como-unirse') }}">Quiero entrar</a>
                <a class="jg-boton jg-boton--linea" href="{{ route('equipos.index') }}">Ver los equipos</a>
            </div>
        </div>
    </section>
@endsection

@section('contenido')
    <ul class="jg-datos">
        <li><strong>{{ $totalJuegos }}</strong><span>Juegos</span></li>
        <li><strong>{{ count(\App\Enums\Nivel::cases()) }}</strong><span>Niveles de roster</span></li>
        <li><strong>{{ config('club.umbral_asistencia') }}%</strong><span>Asistencia para convalidar</span></li>
        <li><strong>{{ config('club.horas_por_semestre') }}</strong><span>Horas laborales por semestre</span></li>
    </ul>

    <h2>Nuestros equipos</h2>
    <p>Cada juego tiene su grupo, su día de reunión y su capitán del semestre.</p>
    <x-lista-juegos :juegos="$juegos" />

    <h2>Por qué entrar</h2>
    <ul class="jg-cards">
        <li class="jg-card">
            <h3>Competí en serio</h3>
            <p>Tier 1 representa a la universidad en competencias externas. Tier 2 es el segundo equipo. El resto del club entrena y juega igual.</p>
            <a href="{{ route('equipos.index') }}">Ver los rosters</a>
        </li>
        <li class="jg-card">
            <h3>Convalidá un crédito</h3>
            <p>Con {{ config('club.umbral_asistencia') }}% de asistencia a las reuniones de tu grupo, el semestre equivale a {{ config('club.horas_por_semestre') }} horas laborales. Una convalidación por persona por semestre.</p>
            <a href="{{ route('convalidacion') }}">Cómo funciona</a>
        </li>
        <li class="jg-card">
            <h3>Torneos todo el año</h3>
            <p>Internos y abiertos a otras universidades. Individuales y por equipo, con inscripción en línea.</p>
            <a href="{{ route('torneos.index') }}">Ver el calendario</a>
        </li>
        <li class="jg-card">
            <h3>Sin costo y sin filtro</h3>
            <p>No se cobra membresía ni se pide nivel mínimo. Caés a una reunión, hablás con el capitán y ya estás dentro.</p>
            <a href="{{ route('como-unirse') }}">Los pasos</a>
        </li>
    </ul>

    <h2>Cómo entrar</h2>
    <p>No hace falta ser jugador de alto nivel ni anotarse antes. Mirá el día y la hora de tu juego, caé a la reunión y hablá con el capitán.</p>
    <div class="jg-acciones">
        <a class="jg-boton" href="{{ route('como-unirse') }}">Ver los pasos</a>
        <a class="jg-boton jg-boton--linea" href="{{ route('contacto') }}">Escribirnos</a>
    </div>
@endsection
