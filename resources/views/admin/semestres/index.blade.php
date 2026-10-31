@extends('admin.layouts.app')

@section('titulo', 'Semestres')

@section('contenido')
    <div class="admin-acciones">
        <h1 class="admin-titulo" style="margin:0; flex:1">Semestres académicos</h1>
        <a class="admin-btn" href="{{ route('admin.semestres.create') }}">Nuevo semestre</a>
    </div>

    <p>El semestre <strong>activo</strong> define el período de las sesiones de asistencia y el cálculo del porcentaje en el portal.</p>

    @if ($semestres->isEmpty())
        <p>No hay semestres configurados.</p>
    @else
        <table class="admin-tabla">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Período</th>
                    <th>Mín. sesiones</th>
                    <th>Activo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($semestres as $semestre)
                    <tr>
                        <td>{{ $semestre->nombre }}</td>
                        <td>{{ $semestre->fecha_inicio->format('d/m/Y') }} – {{ $semestre->fecha_fin->format('d/m/Y') }}</td>
                        <td>{{ $semestre->minimo_sesiones ?? '—' }}</td>
                        <td>{{ $semestre->activo ? 'Sí' : 'No' }}</td>
                        <td class="admin-inline">
                            <a class="admin-btn admin-btn--secundario" href="{{ route('admin.semestres.edit', $semestre) }}">Editar</a>
                            <form method="post" action="{{ route('admin.semestres.destroy', $semestre) }}" onsubmit="return confirm('¿Eliminar este semestre?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-btn admin-btn--secundario">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
