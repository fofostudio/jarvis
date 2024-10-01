@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Crear Nueva Auditoría</h1>

    <form action="{{ route('audits.store') }}" method="POST" id="auditForm" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Información General</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="operator_id">Operador</label>
                            <select name="operator_id" id="operator_id" class="form-control" required>
                                @foreach($operators as $operator)
                                    <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="conversation_date">Fecha de Conversación</label>
                            <input type="date" name="conversation_date" id="conversation_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="review_date">Fecha de Revisión</label>
                            <input type="date" name="review_date" id="review_date" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="platform">Plataforma</label>
                            <select name="platform_id" id="platform_id" class="form-control" required>
                                @foreach($platforms as $platform)
                                    <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="group">Grupo</label>
                            <select name="group_id" id="group_id" class="form-control" required>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="girl_search">Buscar Chica</label>
                            <input type="text" id="girl_search" class="form-control" placeholder="Buscar por nombre, username o ID interno">
                        </div>
                        <div class="mb-3">
                            <label for="girl_id">ID Chica</label>
                            <input type="text" name="girl_id" id="girl_id" class="form-control" required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="girl_name">Nombre Chica</label>
                            <input type="text" name="girl_name" id="girl_name" class="form-control" required readonly>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Información del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="client_name">Nombre del Cliente</label>
                            <input type="text" name="client_name" id="client_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="client_id">ID del Cliente</label>
                            <input type="text" name="client_id" id="client_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="client_status">Estatus del Cliente</label>
                            <select name="client_status" id="client_status" class="form-control" required>
                                <option value="">Seleccione un estatus</option>
                                <option value="Nuevo" {{ old('client_status', $audit->client_status ?? '') == 'Nuevo' ? 'selected' : '' }}>Nuevo</option>
                                <option value="Antiguo" {{ old('client_status', $audit->client_status ?? '') == 'Antiguo' ? 'selected' : '' }}>Antiguo</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Checklist de Auditoría</h5>
                    </div>
                    <div class="card-body">
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                        @foreach($checklistItems as $key => $item)
                            <div class="form-check mb-2">
                                <input type="checkbox" name="checklist[{{ $key }}]" id="{{ $key }}" class="form-check-input checklist-item" value="1" data-score="{{ $item['score'] }}">
                                <label for="{{ $key }}" class="form-check-label">{{ $item['label'] }} ({{ $item['score'] }} puntos)</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Evaluación y Comentarios</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="general_score">Calificación General</label>
                            <input type="number" name="general_score" id="general_score" class="form-control" min="0" max="100" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="general_observation">Observación General</label>
                            <textarea name="general_observation" id="general_observation" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="recommendations">Recomendaciones</label>
                            <textarea name="recommendations" id="recommendations" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Capturas de Pantalla</h5>
                    </div>
                    <div class="card-body">
                        <div id="screenshot-paste-area" class="border p-3 mb-3" style="min-height: 100px;">
                            Haga clic aquí y pegue sus capturas de pantalla (Ctrl+V)
                        </div>
                        <div id="screenshot-preview" class="d-flex flex-wrap"></div>
                        <input type="hidden" name="screenshots" id="screenshots">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Guardar Auditoría</button>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script>
$(document).ready(function() {
    $("#girl_search").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{ route('girls.search') }}",
                dataType: "json",
                data: { term: request.term },
                success: function(data) {
                    response(data.map(function(item) {
                        return {
                            label: item.name + ' (' + item.username + ' - ' + item.internal_id + ')',
                            value: item.name,
                            id: item.internal_id,
                            name: item.name
                        };
                    }));
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            $("#girl_id").val(ui.item.id);
            $("#girl_name").val(ui.item.name);
        }
    });

    let totalScore = 0;
    $('.checklist-item').on('change', function() {
        let score = parseInt($(this).data('score'));
        if ($(this).is(':checked')) {
            totalScore += score;
        } else {
            totalScore -= score;
        }
        updateProgressBar(totalScore);
    });

    function updateProgressBar(score) {
        let percentage = Math.min(score, 100);
        $('.progress-bar').css('width', percentage + '%').attr('aria-valuenow', percentage).text(percentage + '%');
        $('#general_score').val(percentage);
    }

    const screenshotArea = document.getElementById('screenshot-paste-area');
    const screenshotPreview = document.getElementById('screenshot-preview');
    const screenshotInput = document.getElementById('screenshots');
    let screenshots = [];

    screenshotArea.addEventListener('paste', function(e) {
        e.preventDefault();
        const items = e.clipboardData.items;
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                const blob = items[i].getAsFile();
                const reader = new FileReader();
                reader.onload = function(event) {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.style.maxWidth = '200px';
                    img.style.maxHeight = '200px';
                    img.className = 'm-2';
                    screenshotPreview.appendChild(img);
                    screenshots.push(event.target.result);
                    screenshotInput.value = JSON.stringify(screenshots);
                };
                reader.readAsDataURL(blob);
            }
        }
    });

    $('#auditForm').on('submit', function(e) {
        e.preventDefault();
        this.submit();
    });
});
</script>
@endpush