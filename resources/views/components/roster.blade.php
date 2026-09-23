@props(['roster'])

@if ($roster->isEmpty())
    <p class="jg-vacio">El roster de este grupo todavía no está publicado.</p>
@else
    <div class="jg-roster">
        @foreach (\App\Enums\Nivel::cases() as $nivel)
            @continue(! $roster->has($nivel->value))

            <section class="jg-nivel jg-nivel--{{ $nivel->value }}">
                <h3 class="jg-nivel__titulo">{{ $nivel->titulo() }}</h3>

                @if ($nivel->nota() !== '')
                    <p class="jg-nivel__nota">{{ $nivel->nota() }}</p>
                @endif

                <ul class="jg-nivel__lista">
                    @foreach ($roster[$nivel->value] as $integrante)
                        <li class="jg-integrante">
                            <span class="jg-integrante__tag">{{ $integrante->gamer_tag }}</span>
                            @if ($integrante->nombre_publico)
                                <span class="jg-integrante__nombre">{{ $integrante->nombre_publico }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>
@endif
