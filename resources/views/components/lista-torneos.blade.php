@props(['torneos', 'vacio'])

@if ($torneos->isEmpty())
    <p class="jg-vacio">{{ $vacio }}</p>
@else
    <ul class="jg-torneos">
        @foreach ($torneos as $torneo)
            @php
                $detalles = array_filter([
                    $torneo->juego?->nombre,
                    $torneo->modalidad === 'equipo' ? 'Por equipo' : 'Individual',
                    $torneo->abierto_externos ? 'Abierto a externos' : 'Solo miembros del club',
                ]);
            @endphp

            <li class="jg-torneo">
                <time class="jg-torneo__fecha" datetime="{{ $torneo->fecha_inicio->toDateString() }}">
                    <span class="jg-torneo__dia">{{ $torneo->fecha_inicio->format('j') }}</span>
                    <span class="jg-torneo__mes">{{ $torneo->fecha_inicio->locale('es')->translatedFormat('M') }}</span>
                </time>

                <div class="jg-torneo__cuerpo">
                    <h3 class="jg-torneo__nombre">{{ $torneo->nombre }}</h3>
                    <p class="jg-torneo__meta">{{ implode(' · ', $detalles) }}</p>

                    @if ($torneo->descripcion)
                        <div class="jg-torneo__texto">{!! nl2br(e($torneo->descripcion)) !!}</div>
                    @endif

                    @if ($torneo->enlace_inscripcion && str_starts_with($torneo->enlace_inscripcion, 'http'))
                        <a class="jg-boton" href="{{ $torneo->enlace_inscripcion }}" rel="noopener noreferrer">Inscribirme</a>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
@endif
