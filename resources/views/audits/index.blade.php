@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Listado de Auditorías</h1>
    <a href="{{ route('audits.create') }}" class="btn btn-primary mb-3">Crear Nueva Auditoría</a>

    <table class="table">
        <thead>
            <tr>
                <th>Fecha de Revisión</th>
                <th>Operador</th>
                <th>Cliente</th>
                <th>Plataforma</th>
                <th>Calificación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($audits as $audit)
            <tr>
                <td>{{ $audit->review_date->format('d/m/Y') }}</td>
                <td>{{ $audit->operator->name }}</td>
                <td>{{ $audit->client->name }}</td>
                <td>{{ $audit->platform }}</td>
                <td>{{ $audit->general_score }}/10</td>
                <td>
                    <a href="{{ route('audits.show', $audit) }}" class="btn btn-sm btn-info">Ver</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $audits->links() }}
</div>
@endsection
