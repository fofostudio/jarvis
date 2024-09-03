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

            <!-- Nuevo Reporte Operativo -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Nuevo Reporte Operativo</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('operative-reports.store') }}" method="POST" enctype="multipart/form-data"
                            id="reportForm">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="report_type" class="form-label">Tipo de Reporte</label>
                                    <select class="form-control @error('report_type') is-invalid @enderror" id="report_type"
                                        name="report_type">
                                        <option value="select" {{ old('report_type') == null ? 'selected' : '' }}>Seleccione
                                            Tipo Reporte</option>
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
                                <div class="col-md-4">
                                    <label for="report_date" class="form-label">Fecha del Reporte</label>
                                    <input type="date" class="form-control @error('report_date') is-invalid @enderror"
                                        id="report_date" name="report_date" required
                                        value="{{ old('report_date', now()->toDateString()) }}">
                                    @error('report_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <!-- Input para reporte manual -->
                            <div id="manual_input" class="mb-3" style="display: none;">
                                <label for="report_content" class="form-label">Contenido del Reporte</label>
                                <textarea id="report_content" name="report_content" class="form-control @error('report_content') is-invalid @enderror">{{ old('report_content') }}</textarea>
                                @error('report_content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="mt-3">
                                    <label class="form-label">Imágenes de evidencia</label>
                                    <div class="image-paste-area border p-3" style="min-height: 100px; cursor: pointer;">
                                        <p>Haga clic aquí y pegue imágenes del portapapeles (Ctrl+V)</p>
                                    </div>
                                    <div class="image-preview mt-2 d-flex flex-wrap"></div>
                                </div>
                            </div>
                            <!-- Input para reporte conversacional -->
                            <div id="conversational_input" class="mb-3" style="display: none;">
                                <div id="conversations">
                                    <!-- Las conversaciones se agregarán aquí dinámicamente -->
                                </div>
                                <button type="button" class="btn btn-primary mt-3" id="add_conversation">Agregar
                                    Conversación</button>
                            </div>

                            <button type="submit" class="btn btn-dark mt-3">Guardar Reporte</button>
                        </form>
                    </div>
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
                                <table class="table table-bordered" id="groupReportsTable" width="100%" cellspacing="0">
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
                                <table class="table table-bordered" id="myReportsTable" width="100%" cellspacing="0">
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
                                                        <span class="badge badge-warning bg-warning">Pendiente</span>
                                                    @elseif($report->is_approved)
                                                        <span class="badge badge-success bg-success">Aprobado</span>
                                                    @else
                                                        <span class="badge badge-danger bg-danger">Incompleto</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-dark btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#reportModal{{ $report->id }}">
                                                        Ver
                                                    </button>
                                                    @if ($report->is_approved === null)
                                                        <form id="delete-form-{{ $report->id }}"
                                                            action="{{ route('operative-reports.destroy', $report->id) }}"
                                                            method="POST" style="display: inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="confirmDelete({{ $report->id }})">Eliminar</button>
                                                        </form>
                                                    @endif
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
                        @if ($report->images->count() > 0)
                            <p><strong>Imágenes:</strong></p>
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
                        @if ($report->images->count() > 0)
                            <p><strong>Imágenes:</strong></p>
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
        var reportEvents = @php
            echo json_encode(
                $reports
                    ->map(function ($report) {
                        return [
                            'title' => $report->report_type === 'manual' ? 'Sugerencia/Queja/Reclamo' : 'Conversacional',
                            'start' => $report->report_date,
                            'backgroundColor' => $report->report_type === 'manual' ? '#ff9f89' : '#90caf9',
                            'borderColor' => $report->report_type === 'manual' ? '#ff9f89' : '#90caf9',
                        ];
                    })
                    ->values()
                    ->all(),
            );
        @endphp;
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicialización del calendario
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: reportEvents,
                eventContent: function(arg) {
                    return {
                        html: '<div class="fc-event-title">' + arg.event.title + '</div>'
                    };
                }
            });
            calendar.render();

            // Referencias a elementos del DOM
            const conversationalInput = document.getElementById('conversational_input');
            const addConversationBtn = document.getElementById('add_conversation');
            const reportType = document.getElementById('report_type');
            const manualInput = document.getElementById('manual_input');
            const reportForm = document.getElementById('reportForm');

            let uploadedImages = {};

            // Manejo del cambio de tipo de reporte
            reportType.addEventListener('change', function() {
                if (this.value === 'manual') {
                    manualInput.style.display = 'block';
                    conversationalInput.style.display = 'none';
                } else if (this.value === 'conversational') {
                    manualInput.style.display = 'none';
                    conversationalInput.style.display = 'block';
                } else {
                    manualInput.style.display = 'none';
                    conversationalInput.style.display = 'none';
                }
            });

            // Función para manejar el pegado de imágenes
            function handleImagePaste(e, previewElement, imageArray) {
                const items = e.clipboardData.items;
                for (let i = 0; i < items.length; i++) {
                    if (items[i].type.indexOf('image') !== -1) {
                        const blob = items[i].getAsFile();
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const img = document.createElement('img');
                            img.src = event.target.result;
                            img.style.maxWidth = '100px';
                            img.style.maxHeight = '100px';
                            img.style.margin = '5px';
                            previewElement.appendChild(img);

                            const file = new File([blob], `pasted-image-${Date.now()}.png`, {
                                type: 'image/png'
                            });
                            imageArray.push(file);
                        }
                        reader.readAsDataURL(blob);
                    }
                }
            }

            // Configurar el área de pegado para el reporte manual
            const manualPasteArea = manualInput.querySelector('.image-paste-area');
            const manualPreview = manualInput.querySelector('.image-preview');
            uploadedImages['manual'] = [];

            manualPasteArea.addEventListener('paste', (e) => handleImagePaste(e, manualPreview, uploadedImages[
                'manual']));
            manualPasteArea.addEventListener('click', () => manualPasteArea.focus());

            // Agregar nueva conversación
            addConversationBtn.addEventListener('click', addConversation);

            function addConversation() {
                const conversationId = Date.now();
                const conversationTemplate = `
                    <div id="conversation-${conversationId}" class="conversation mb-3 border p-3">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="gentleman_code[]">Código Caballero</label>
                                <input type="text" class="form-control" name="gentleman_code[]" required>
                            </div>
                            <div class="col">
                                <label for="lady_code[]">Código Dama</label>
                                <input type="text" class="form-control" name="lady_code[]" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="conversation_summary[]">Resumen Conversación</label>
                            <textarea name="conversation_summary[]" class="form-control" required></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Imágenes de evidencia</label>
                            <div class="image-paste-area border p-3" style="min-height: 100px; cursor: pointer;">
                                <p>Haga clic aquí y pegue imágenes del portapapeles (Ctrl+V)</p>
                            </div>
                            <div class="image-preview mt-2 d-flex flex-wrap"></div>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeConversation(${conversationId})">Eliminar Conversación</button>
                    </div>
                `;
                document.getElementById('conversations').insertAdjacentHTML('beforeend', conversationTemplate);

                const newPasteArea = document.querySelector(`#conversation-${conversationId} .image-paste-area`);
                const newPreview = document.querySelector(`#conversation-${conversationId} .image-preview`);
                uploadedImages[conversationId] = [];

                newPasteArea.addEventListener('paste', (e) => handleImagePaste(e, newPreview, uploadedImages[
                    conversationId]));
                newPasteArea.addEventListener('click', () => newPasteArea.focus());
            }

            // Función para remover una conversación
            window.removeConversation = function(conversationId) {
                const conversationElement = document.getElementById(`conversation-${conversationId}`);
                if (conversationElement) {
                    conversationElement.remove();
                    delete uploadedImages[conversationId];
                }
            }

            // Inicialización de CKEditor
            if (typeof CKEDITOR !== 'undefined') {
                CKEDITOR.replace('report_content');
            }

            // Manejo del envío del formulario
            reportForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Agregar el contenido del CKEditor al FormData si es necesario
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.report_content) {
                    formData.set('report_content', CKEDITOR.instances.report_content.getData());
                }

                // Agregar las imágenes al FormData
                for (let key in uploadedImages) {
                    uploadedImages[key].forEach((file, index) => {
                        formData.append(`images[${key}][]`, file);
                    });
                }

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw err;
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Éxito', data.message, 'success').then(() => {
                                window.location.href = data.redirect;
                            });
                        } else {
                            throw new Error(data.message || 'Error desconocido');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        let errorMessage = 'Hubo un problema al guardar el reporte';
                        if (error.errors) {
                            errorMessage += ':\n' + Object.values(error.errors).flat().join('\n');
                        }
                        Swal.fire('Error', errorMessage, 'error');
                    });
            });
        });

        // Función para confirmar eliminación
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
@endsection
