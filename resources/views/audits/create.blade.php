@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Crear Nueva Auditoría</h1>

    <form action="{{ route('audits.store') }}" method="POST" enctype="multipart/form-data">
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
                            <select name="platform" id="platform" class="form-control" required>
                                @foreach($platforms as $platform)
                                    <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="group">Grupo</label>
                            <select name="group" id="group" class="form-control" required>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dama_id">ID Dama</label>
                            <input type="text" name="dama_id" id="dama_id" class="form-control" required>
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
                            <input type="text" name="client_status" id="client_status" class="form-control" required>
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
                        @php
                            $checklistItems = [
                                'interesting_greetings' => 'Saludos interesantes',
                                'conversation_flow' => 'Flujo de conversación',
                                'new_conversation_topics' => 'Nuevos temas de conversación',
                                'sentence_structure' => 'Estructura de frases',
                                'generates_love_bond' => 'Genera vínculo amoroso',
                                'moderate_gift_request' => 'Petición moderada de regalos',
                                'material_sending' => 'Envío de material',
                                'commits_profile' => 'Compromete el perfil',
                                'response_times' => 'Tiempos de respuesta',
                                'initiates_hot_chat' => 'Inicia/incentiva chat caliente',
                                'conversation_coherence' => 'Coherencia en conversación'
                            ];
                        @endphp

                        @foreach($checklistItems as $key => $label)
                            <div class="form-check mb-2">
                                <input type="checkbox" name="{{ $key }}" id="{{ $key }}" class="form-check-input" value="1">
                                <label for="{{ $key }}" class="form-check-label">{{ $label }}</label>
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
                            <input type="number" name="general_score" id="general_score" class="form-control" min="0" max="10" step="0.1" required>
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


<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Autocompletar para ID Dama
    const damaInput = document.getElementById('dama_id');
    const damas = @json($damas); // Asegúrate de pasar esta variable desde el controlador

    $(damaInput).autocomplete({
        source: damas.map(dama => dama.id_int),
        minLength: 2
    });
});
</script>
@endsection
