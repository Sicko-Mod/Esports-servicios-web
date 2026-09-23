@props(['juegos'])

@if ($juegos->isEmpty())
    <p class="jg-vacio">Todavía no hay grupos activos. Volvé a revisar al inicio del semestre.</p>
@else
    <ul class="jg-juegos">
        @foreach ($juegos as $juego)
            <li class="jg-juego">
                <h3 class="jg-juego__nombre"><a href="{{ route('equipos.show', $juego->slug) }}">{{ $juego->nombre }}</a></h3>

                <dl class="jg-juego__datos">
                    @if ($juego->dia_legible || $juego->hora_legible)
                        <dt>Se reúne</dt>
                        <dd>{{ trim($juego->dia_legible . ' ' . $juego->hora_legible) }}</dd>
                    @endif
                    @if ($juego->lugar)
                        <dt>Lugar</dt>
                        <dd>{{ $juego->lugar }}</dd>
                    @endif
                    @if ($juego->capitan)
                        <dt>Capitán</dt>
                        <dd>{{ $juego->capitan }}</dd>
                    @endif
                </dl>
            </li>
        @endforeach
    </ul>
@endif
