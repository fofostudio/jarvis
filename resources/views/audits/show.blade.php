@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Detalles de la Auditoría</h1>

        <div class="card mb-4">
            <div class="card-header">
                <h2>Información General</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Auditor:</strong> {{ $audit->auditor->name }}</p>
                        <p><strong>Operador:</strong> {{ $audit->operator->name }}</p>
                        <p><strong>Fecha de Conversación:</strong> {{ $audit->conversation_date->format('d/m/Y') }}</p>
                        <p><strong>Fecha de Revisión:</strong> {{ $audit->review_date->format('d/m/Y') }}</p>
                        <p><strong>Plataforma:</strong> {{ $audit->platform->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Grupo:</strong> {{ $audit->group->name }}</p>
                        <p><strong>Chica:</strong> {{ $audit->girl->name }} (ID: {{ $audit->girl->internal_id }})</p>
                        <p><strong>Cliente:</strong> {{ $audit->client_name }}</p>
                        <p><strong>ID del Cliente:</strong> {{ $audit->client_id }}</p>
                        <p><strong>Estado del Cliente:</strong> {{ $audit->client_status }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h2>Checklist de Auditoría</h2>
            </div>
            <div class="card-body">
                @foreach ($checklistItems as $key => $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $item['label'] }}
                        @if (isset($audit->checklist[$key]) && $audit->checklist[$key])
                            <span class="badge bg-success rounded-pill">Sí</span>
                        @else
                            <span class="badge bg-danger rounded-pill">No</span>
                        @endif
                    </li>
                @endforeach
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h2>Evaluación</h2>
            </div>
            <div class="card-body">
                <p><strong>Calificación General:</strong> {{ $audit->general_score }}</p>
                <div class="progress mb-3">
                    <div class="progress-bar" role="progressbar" style="width: {{ $audit->general_score }}%;"
                        aria-valuenow="{{ $audit->general_score }}" aria-valuemin="0" aria-valuemax="100">
                        {{ $audit->general_score }}%</div>
                </div>
                <p><strong>Observación General:</strong></p>
                <p>{{ $audit->general_observation }}</p>
                @if ($audit->recommendations)
                    <p><strong>Recomendaciones:</strong></p>
                    <p>{{ $audit->recommendations }}</p>
                @endif
            </div>
        </div>

        @if ($audit->screenshots)
            <div class="card mb-4">
                <div class="card-header">
                    <h2>Capturas de Pantalla</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($audit->screenshots as $screenshot)
                            <div class="col-md-4 mb-3">
                                <img src="{{ $screenshot }}" class="img-fluid" alt="Captura de pantalla">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('audits.index') }}" class="btn btn-secondary">Volver al Listado</a>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .progress {
            height: 25px;
        }

        .progress-bar {
            line-height: 25px;
        }
    </style>
@endpush
