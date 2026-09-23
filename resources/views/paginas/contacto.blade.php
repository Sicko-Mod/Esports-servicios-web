@extends('layouts.app')

@section('titulo', 'Contacto')

@section('contenido')
    @php $contacto = config('club.contacto'); @endphp

    <p>Para consultas sobre el club, torneos abiertos o alianzas con otras universidades y organizaciones.</p>

    <ul>
        <li>
            Correo:
            @if ($contacto['correo'])
                <a href="mailto:{{ $contacto['correo'] }}">{{ $contacto['correo'] }}</a>
            @else
                [completar correo institucional del club]
            @endif
        </li>
        <li>
            Instagram:
            @if ($contacto['instagram'])
                {{ $contacto['instagram'] }}
            @else
                [completar usuario]
            @endif
        </li>
        <li>
            Discord:
            @if ($contacto['discord'] && str_starts_with($contacto['discord'], 'http'))
                <a href="{{ $contacto['discord'] }}" rel="noopener noreferrer">{{ $contacto['discord'] }}</a>
            @elseif ($contacto['discord'])
                {{ $contacto['discord'] }}
            @else
                [completar enlace de invitación]
            @endif
        </li>
        <li>
            Presencial:
            @if ($contacto['presencial'])
                {{ $contacto['presencial'] }}
            @else
                [completar aula o espacio y horario]
            @endif
        </li>
    </ul>

    <p>No respondemos consultas académicas ni trámites de Registro Académico: para eso, escribí directamente a la universidad.</p>
@endsection
