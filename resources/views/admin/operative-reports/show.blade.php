@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Detalle del Reporte Operativo</h1>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Reporte del {{ $report->report_date->format('d/m/Y') }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Operador:</strong> {{ $report->user->name }}</p>
                        <p><strong>Grupo:</strong> {{ $report->group->name }}</p>
                        <p><strong>Tipo:</strong>
                            @if($report->report_type === 'conversational')
                            Conversacional
                        @elseif($report->report_type === 'manual')
                            Quejas/Reclamos
                        @else
                            {{ $report->report_type }}
                        @endif</p>
                        <p><strong>Estado actual:</strong>
                            @if ($report->is_approved === null)
                                <span class="badge badge-warning bg-warning">Pendiente</span>
                            @elseif ($report->is_approved)
                                <span class="badge badge-success bg-success">Aprobado</span>
                            @else
                                <span class="badge badge-danger bg-danger">Incompleto</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('operative-reports.update-status', $report) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="form-group">
                                <label for="is_approved">Actualizar Estado</label>
                                <select name="is_approved" id="is_approved" class="form-control">
                                    <option value="">Pendiente</option>
                                    <option value="1" {{ $report->is_approved === true ? 'selected' : '' }}>Aprobado
                                    </option>
                                    <option value="0" {{ $report->is_approved === false ? 'selected' : '' }}>Incompleto
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="auditor_comment">Observaciones del Auditor</label>
                                <textarea name="auditor_comment" id="auditor_comment" rows="3" class="form-control">{{ $report->auditor_comment }}</textarea>
                            </div><br/>
                            <button type="submit" class="btn btn-dark">Actualizar Estado y Observaciones</button>
                        </form>
                    </div>
                </div>

                <hr>
                <h5>Contenido del Reporte</h5>
                @if ($report->report_type === 'manual')
                    <div class="border p-3">
                        <h6>Contenido general:</h6>
                        {!! $report->content !!}
                        @if($report->content_quejas)
                            <h6 class="mt-3">Quejas:</h6>
                            {!! $report->content_quejas !!}
                        @endif
                    </div>
                @elseif ($report->report_type === 'conversational')
                    @if(is_array($report->conversations))
                        @foreach ($report->conversations as $conversation)
                            <div class="border p-3 mb-3">
                                <p><strong>Código Caballero:</strong> {{ $conversation['gentleman_code'] ?? 'N/A' }}</p>
                                <p><strong>Código Dama:</strong> {{ $conversation['lady_code'] ?? 'N/A' }}</p>
                                <p><strong>Resumen Conversación:</strong></p>
                                <div>{!! $conversation['summary'] ?? 'No hay resumen disponible' !!}</div>
                            </div>
                        @endforeach
                    @elseif(is_array($report->conversations))
                        <div class="border p-3 mb-3">
                            <p><strong>Código Caballero:</strong> {{ $report->conversations['gentleman_code'] ?? 'N/A' }}</p>
                            <p><strong>Código Dama:</strong> {{ $report->conversations['lady_code'] ?? 'N/A' }}</p>
                            <p><strong>Resumen Conversación:</strong></p>
                            <div>{!! $report->conversations['summary'] ?? 'No hay resumen disponible' !!}</div>
                        </div>
                    @else
                        <p>No hay datos de conversación disponibles o el formato es incorrecto.</p>
                    @endif
                @endif

                @if ($report->file_path)
                    <div class="mt-3">
                        <h6>Archivo adjunto:</h6>
                        <a href="{{ route('operative-reports.download', $report) }}"
                            class="btn btn-primary btn-sm">Descargar archivo</a>
                    </div>
                @endif

                <!-- Nueva sección para imágenes -->
                @if ($report->images->count() > 0)
                    <div class="mt-4">
                        <h6>Imágenes del reporte:</h6>
                        <div class="d-flex flex-wrap">
                            @foreach ($report->images as $image)
                                <div class="m-2">
                                    <a href="{{ asset('storage/' . $image->path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="Imagen del reporte"
                                             class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
