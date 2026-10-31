@extends('layouts.app')

@section('titulo', 'Convalidación de créditos')

@section('contenido')
    <p>Participar en Jaguares E-Sports puede contar como actividad de vida universitaria. Esta página resume el proceso; en caso de diferencia, vale el reglamento oficial de la UAM.</p>

    <h2>Qué se necesita</h2>
    <ul>
        <li>Ser miembro activo del club con cuenta en el <a href="{{ route('portal.login') }}">portal de miembros</a> (correo &#64;{{ config('club.correo_institucional_dominio') }}).</li>
        <li>Al menos <strong>{{ config('club.umbral_asistencia') }}%</strong> de asistencia a las reuniones de tu grupo en el semestre activo.</li>
        <li>Perfil completo en el portal (datos para el club; no se publican en el sitio).</li>
        <li><strong>Una</strong> solicitud de convalidación por persona por semestre.</li>
    </ul>

    <h2>Cómo se registra la asistencia</h2>
    <p>El capitán de tu grupo pasa lista en cada reunión desde el portal (celular). Podés ver tu porcentaje en <em>Mi asistencia</em> cuando iniciás sesión.</p>

    <h2>Cómo se solicita</h2>
    <p>Cuando cumplás los requisitos, entrá al portal → <strong>Convalidación</strong> → <em>Enviar solicitud</em>. El equipo del club la revisa y prepara el reporte para Registro Académico.</p>

    <h2>Horas equivalentes</h2>
    <p>Al aprobarse, el semestre equivale a <strong>{{ config('club.horas_por_semestre') }} horas laborales</strong> según las reglas del club (confirmá con tu coordinación académica).</p>

    <h2>Datos personales</h2>
    <p>El sitio público solo muestra gamer tags y tiers. CIF, teléfono y carrera se guardan en el portal seguro de miembros. El reporte institucional identifica por CIF sin publicar nombres en el archivo para Registro.</p>
@endsection
