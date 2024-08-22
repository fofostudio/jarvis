@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Mis Reportes Operativos</h1>

        <div class="row">
            @if (session('success_message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endif

            <!-- Calendario -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Calendario de Reportes</h6>
                    </div>
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>

            <!-- Reportes del Grupo y Mis Reportes -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Nuevo Reporte Operativo</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('operative-reports.store') }}" method="POST" enctype="multipart/form-data"
                            id="reportForm">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="report_date">Fecha del Reporte</label>
                                <input type="date" class="form-control @error('report_date') is-invalid @enderror"
                                    id="report_date" name="report_date" required
                                    value="{{ old('report_date', now()->toDateString()) }}">
                                @error('report_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="report_type">Tipo de Reporte</label>
                                <select class="form-control @error('report_type') is-invalid @enderror" id="report_type"
                                    name="report_type">
                                    <option value="manual" {{ old('report_type') == 'manual' ? 'selected' : '' }}>
                                        Sugerencia/Queja/Reclamo</option>
                                    <option value="conversational"
                                        {{ old('report_type') == 'conversational' ? 'selected' : '' }}>Conversacional
                                    </option>
                                </select>
                                @error('report_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div id="conversational_input" class="form-group mb-3" style="display: none;">
                                <label>Conversaciones</label>
                                <div id="conversations">
                                    <div class="conversation mb-3">
                                        <div class="row">
                                            <div class="col">
                                                <label for="gentleman_code[]">Código Caballero</label>
                                                <input type="text" class="form-control" name="gentleman_code[]" >
                                            </div>
                                            <div class="col">
                                                <label for="lady_code[]">Código Dama</label>
                                                <input type="text" class="form-control" name="lady_code[]" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="conversation_summary[]">Resumen Conversación</label>
                                            <textarea name="conversation_summary[]" class="form-control" ></textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary" id="add_conversation">Agregar
                                    Conversación</button>
                            </div>
                            <div id="manual_input" class="form-group mb-3" style="display: none;">
                                <label for="report_content">Contenido del Reporte</label>
                                <textarea id="report_content" name="report_content" class="form-control @error('report_content') is-invalid @enderror">{{ old('report_content') }}</textarea>
                                @error('report_content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-dark">Guardar Reporte</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reportes del Grupo y Mis Reportes -->
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <!-- Reportes del Grupo -->
                    <div class="col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Ultimos 5 Reportes de mi Grupo</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="groupReportsTable" width="100%"
                                        cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Operador</th>
                                                <th>Tipo</th>

                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groupReports as $report)
                                                <tr>
                                                    <td>{{ $report->report_date->format('d/m/Y') }}</td>
                                                    <td>{{ $report->user->name }}</td>
                                                    <td>
                                                        @if ($report->report_type === 'manual')
                                                            Sugerencia/Queja/Reclamo
                                                        @elseif ($report->report_type === 'conversational')
                                                            Conversacional
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <button class="btn btn-dark btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#groupReportModal{{ $report->id }}">
                                                            Ver
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mis Reportes -->
                    <div class="col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Mis Ultimos 5 Reportes</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="myReportsTable" width="100%"
                                        cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reports as $report)
                                                <tr>
                                                    <td>{{ $report->report_date->format('d/m/Y') }}</td>
                                                    <td>
                                                        @if ($report->report_type === 'manual')
                                                            Sugerencia/Queja/Reclamo
                                                        @elseif ($report->report_type === 'conversational')
                                                            Conversacional
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($report->is_approved === null)
                                                            <span class="badge badge-warning">Pendiente</span>
                                                        @elseif($report->is_approved)
                                                            <span class="badge badge-success">Aprobado</span>
                                                        @else
                                                            <span class="badge badge-danger">Rechazado</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-dark btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#reportModal{{ $report->id }}">
                                                            Ver
                                                        </button>
                                                        <form id="delete-form-{{ $report->id }}"
                                                            action="{{ route('operative-reports.destroy', $report->id) }}"
                                                            method="POST" style="display: inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="confirmDelete({{ $report->id }})">Eliminar</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales para Reportes del Grupo -->
    @foreach ($groupReports as $report)
        <div class="modal fade" id="groupReportModal{{ $report->id }}" tabindex="-1"
            aria-labelledby="groupReportModalLabel{{ $report->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="groupReportModalLabel{{ $report->id }}">Detalle del Reporte -
                            {{ $report->user->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Fecha:</strong> {{ $report->report_date->format('d/m/Y') }}</p>
                        <p><strong>Tipo:</strong>
                            @if ($report->report_type === 'manual')
                                Sugerencia/Queja/Reclamo
                            @elseif ($report->report_type === 'conversational')
                                Conversacional
                            @endif
                        </p>
                        @if ($report->report_type === 'manual')
                            <p><strong>Contenido:</strong></p>
                            <div class="border p-3 mb-3">
                                {!! $report->content !!}
                            </div>
                        @elseif ($report->report_type === 'conversational')
                            <p><strong>Conversaciones:</strong></p>
                            @foreach ($report->conversations as $conversation)
                                <div class="border p-3 mb-3">
                                    <p><strong>Código Caballero:</strong> {{ $conversation['gentleman_code'] }}</p>
                                    <p><strong>Código Dama:</strong> {{ $conversation['lady_code'] }}</p>
                                    <p><strong>Resumen Conversación:</strong></p>
                                    <div>{!! $conversation['summary'] !!}</div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modales para Mis Reportes -->
    @foreach ($reports as $report)
        <div class="modal fade" id="reportModal{{ $report->id }}" tabindex="-1"
            aria-labelledby="reportModalLabel{{ $report->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reportModalLabel{{ $report->id }}">Detalle de Mi Reporte</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Fecha:</strong> {{ $report->report_date->format('d/m/Y') }}</p>
                        <p><strong>Tipo:</strong>
                            @if ($report->report_type === 'manual')
                                Sugerencia/Queja/Reclamo
                            @elseif ($report->report_type === 'conversational')
                                Conversacional
                            @endif
                        </p>
                        <p><strong>Estado:</strong> {{ $report->status }}</p>
                        @if ($report->report_type === 'manual')
                            <p><strong>Contenido:</strong></p>
                            <div class="border p-3 mb-3">
                                {!! $report->content !!}
                            </div>
                        @elseif ($report->report_type === 'conversational')
                            <p><strong>Conversaciones:</strong></p>
                            @foreach ($report->conversations as $conversation)
                                <div class="border p-3 mb-3">
                                    <p><strong>Código Caballero:</strong> {{ $conversation['gentleman_code'] }}</p>
                                    <p><strong>Código Dama:</strong> {{ $conversation['lady_code'] }}</p>
                                    <p><strong>Resumen Conversación:</strong></p>
                                    <div>{!! $conversation['summary'] !!}</div>
                                </div>
                            @endforeach
                        @endif
                        @if ($report->auditor_comment)
                            <p><strong>Observaciones del Auditor:</strong></p>
                            <div class="border p-3">
                                {{ $report->auditor_comment }}
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <script>
        function confirmDelete(reportId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción eliminará el reporte permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + reportId).submit();
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: [
                    @foreach ($reports as $report)
                        {
                            title: 'Reporte Cargado',
                            start: '{{ $report->report_date->format('Y-m-d') }}',
                            backgroundColor: 'lightgreen',
                        },
                    @endforeach
                    // Agrega más eventos para los días sin reportes (en rojo claro)
                ],
            });
            calendar.render();

            const conversationalInput = document.getElementById('conversational_input');
            const addConversationBtn = document.getElementById('add_conversation');

            const reportType = document.getElementById('report_type');
            const manualInput = document.getElementById('manual_input');

            reportType.addEventListener('change', function() {
                if (this.value === 'manual') {
                    manualInput.style.display = 'block';
                    conversationalInput.style.display = 'none';
                } else {
                    manualInput.style.display = 'none';
                    conversationalInput.style.display = 'block';
                }
            });

            addConversationBtn.addEventListener('click', function() {
                const conversationTemplate = `
                    <div class="conversation mb-3">
                        <div class="row">
                            <div class="col">
                                <label for="gentleman_code[]">Código Caballero</label>
                                <input type="text" class="form-control" name="gentleman_code[]" required>
                            </div>
                            <div class="col">
                                <label for="lady_code[]">Código Dama</label>
                                <input type="text" class="form-control" name="lady_code[]" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="conversation_summary[]">Resumen Conversación</label>
                            <textarea name="conversation_summary[]" class="form-control" required></textarea>
                        </div>
                    </div>
                `;
                document.getElementById('conversations').insertAdjacentHTML('beforeend', conversationTemplate);
            });

            var editor = CKEDITOR.replace('report_content');

            const reportForm = document.getElementById('reportForm');

            reportForm.addEventListener('submit', function(e) {
                if (reportType.value === 'manual') {
                    editor.updateElement();
                }
            });
        });
    </script>
    @endsection
