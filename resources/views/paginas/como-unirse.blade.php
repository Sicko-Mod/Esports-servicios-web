@extends('layouts.app')

@section('titulo', 'Cómo unirse')

@section('contenido')
    <p>Está abierto a cualquier estudiante activo de la UAM. No se cobra membresía.</p>

    <h2>Los pasos</h2>
    <ol>
        <li>Elegí el juego que te interesa en la página de <a href="{{ route('equipos.index') }}">Equipos</a> y mirá el día y hora de reunión.</li>
        <li>Caé a una reunión. No hace falta avisar ni anotarse antes.</li>
        <li>Hablá con el capitán del grupo para que te sume al roster.</li>
        <li>Si querés que tu participación cuente para convalidación de créditos, revisá los <a href="{{ route('convalidacion') }}">requisitos</a>.</li>
    </ol>

    <h2>Sobre tus datos</h2>
    <p>En el roster público aparece tu gamer tag, no tu nombre real. Nunca te vamos a pedir por este sitio tu CIF, tu teléfono, tu correo personal ni tu fecha de nacimiento. Si alguien lo hace en nombre del club, avisanos.</p>

    <h2>Contacto</h2>
    <p>Dudas antes de caer a una reunión: escribinos desde la página de <a href="{{ route('contacto') }}">Contacto</a>.</p>
@endsection
