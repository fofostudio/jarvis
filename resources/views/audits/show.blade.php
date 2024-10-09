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
                        <p><strong>Tipo de Auditoría:</strong> {{ ucfirst($audit->audit_type) }}</p>
                        <p><strong>Fecha de Auditoría:</strong> {{ $audit->audit_date->format('d/m/Y') }}</p>
                        @if($audit->audit_type == 'group')
                            <p><strong>Grupo:</strong> {{ $audit->group->name }}</p>
                        @else
                            <p><strong>Operador:</strong> {{ $audit->operator->name }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <p><strong>Cantidad de Chicas Auditadas:</strong> {{ $audit->auditDetails->count() }}</p>
                        <p><strong>Calificación Total:</strong> {{ number_format($audit->total_score, 2) }}%</p>
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" style="width: {{ $audit->total_score }}%;"
                                aria-valuenow="{{ $audit->total_score }}" aria-valuemin="0" aria-valuemax="100">
                                {{ number_format($audit->total_score, 2) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach($audit->auditDetails as $detail)
            <div class="card mb-4">
                <div class="card-header">
                    <h2>Detalle de Auditoría - {{ $detail->girl->name }}</h2>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Chica:</strong> {{ $detail->girl->name }} (ID: {{ $detail->girl->internal_id }})</p>
                            <p><strong>Plataforma:</strong> {{ $detail->platform->name }}</p>
                            <p><strong>Cliente:</strong> {{ $detail->client_name }}</p>
                            <p><strong>ID del Cliente:</strong> {{ $detail->client_id }}</p>
                            <p><strong>Estado del Cliente:</strong> {{ $detail->client_status }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Calificación:</strong> {{ $detail->general_score }}</p>
                            <div class="progress mb-3">
                                <div class="progress-bar" role="progressbar" style="width: {{ $detail->general_score }}%;"
                                    aria-valuenow="{{ $detail->general_score }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $detail->general_score }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    <h3>Checklist de Auditoría</h3>
                    <ul class="list-group mb-3">
                        @foreach ($checklistItems as $key => $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $item['label'] }}
                                @if (isset($detail->checklist[$key]) && $detail->checklist[$key])
                                    <span class="badge bg-success rounded-pill">Sí</span>
                                @else
                                    <span class="badge bg-danger rounded-pill">No</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    <h3>Evaluación</h3>
                    <p><strong>Observación General:</strong></p>
                    <p>{{ $detail->general_observation }}</p>
                    @if ($detail->recommendations)
                        <p><strong>Recomendaciones:</strong></p>
                        <p>{{ $detail->recommendations }}</p>
                    @endif

                    @if ($detail->screenshots)
                        <h3>Capturas de Pantalla</h3>
                        <div class="row">
                            @foreach (json_decode($detail->screenshots) as $screenshot)
                                <div class="col-md-4 mb-3">
                                    <img src="{{ $screenshot }}" class="img-fluid" alt="Captura de pantalla">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

       
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