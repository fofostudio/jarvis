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
                                <label for="audit_type">Tipo de Auditoría</label>
                                <select name="audit_type" id="audit_type" class="form-control" required>
                                    <option value="group">Grupal</option>
                                    <option value="individual">Individual</option>
                                </select>
                            </div>
                            <div class="mb-3" id="group_select">
                                <label for="group_id">Grupo</label>
                                <select name="group_id" id="group_id" class="form-control" required>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3" id="operator_select" style="display: none;">
                                <label for="operator_id">Operador</label>
                                <select name="operator_id" id="operator_id" class="form-control">
                                    @foreach ($operators as $operator)
                                        <option value="{{ $operator->id }}" data-group-id="{{ $operator->group_id }}">
                                            {{ $operator->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="audit_date">Fecha de Auditoría</label>
                                <input type="date" name="audit_date" id="audit_date" class="form-control" required
                                    readonly value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Seleccionar Chicas para Auditoría</h5>
                        </div>
                        <div class="card-body">
                            <div id="girls_selection" class="d-flex flex-wrap">
                                <!-- Las chicas se cargarán aquí dinámicamente -->
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Puntuación Total de Auditoría</h5>
                        </div>
                        <div class="card-body">
                            <div class="progress">
                                <div id="total_score_progress" class="progress-bar" role="progressbar" style="width: 0%;"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                            </div>
                            <p class="mt-2">Puntuación total: <span id="total_score_display">0</span>/100</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Auditorías Individuales</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="auditTabs" role="tablist">
                        <!-- Las pestañas se generarán dinámicamente aquí -->
                    </ul>
                    <div class="tab-content" id="auditTabContent">
                        <!-- El contenido de las pestañas se generará dinámicamente aquí -->
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">Guardar Auditoría</button>
        </form>
    </div>

    <template id="audit-tab-template">
        <div class="tab-pane fade" id="audit-TAB_ID" role="tabpanel">
            <div class="mt-3">
                <input type="hidden" name="audit_details[TAB_ID][girl_id]" value="GIRL_ID">
                <div class="mb-3">
                    <label>Plataforma</label>
                    <input type="text" name="audit_details[TAB_ID][platform_name]" class="form-control" readonly>
                    <input type="hidden" name="audit_details[TAB_ID][platform_id]" class="platform-id">
                </div>
                <div class="mb-3">
                    <label>Progreso de la Auditoría</label>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="100">0%</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Nombre del Cliente</label>
                    <input type="text" name="audit_details[TAB_ID][client_name]" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>ID del Cliente</label>
                    <input type="text" name="audit_details[TAB_ID][client_id]" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Estatus del Cliente</label>
                    <select name="audit_details[TAB_ID][client_status]" class="form-control" required>
                        <option value="Nuevo">Nuevo</option>
                        <option value="Antiguo">Antiguo</option>
                    </select>
                </div>
                <div class="mb-3">
                    <h6>Checklist de Auditoría</h6>
                    <div class="checklist-container">
                        @foreach ($checklistItems as $key => $item)
                            <div class="form-check mb-2">
                                <input type="checkbox" name="audit_details[TAB_ID][checklist][{{ $key }}]"
                                    id="checklist_{{ $key }}_TAB_ID" class="form-check-input checklist-item"
                                    value="1" data-score="{{ $item['score'] }}">
                                <label for="checklist_{{ $key }}_TAB_ID"
                                    class="form-check-label">{{ $item['label'] }} ({{ $item['score'] }} puntos)</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mb-3">
                    <label>Calificación General</label>
                    <input type="number" name="audit_details[TAB_ID][general_score]" class="form-control general-score"
                        min="0" max="100" required readonly>
                </div>
                <div class="mb-3">
                    <label>Observación General</label>
                    <textarea name="audit_details[TAB_ID][general_observation]" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Recomendaciones</label>
                    <textarea name="audit_details[TAB_ID][recommendations]" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label>Capturas de Pantalla</label>
                    <div class="screenshot-paste-area border p-3 mb-3" style="min-height: 100px;">
                        Haga clic aquí y pegue sus capturas de pantalla (Ctrl+V)
                    </div>
                    <div class="screenshot-preview d-flex flex-wrap"></div>
                    <input type="hidden" name="audit_details[TAB_ID][screenshots]" class="screenshot-input">
                </div>
            </div>
        </div>
    </template>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let selectedGirls = new Set();

            $('#audit_type').change(function() {
                if ($(this).val() == 'group') {
                    $('#group_select').show();
                    $('#operator_select').hide();
                } else {
                    $('#group_select').hide();
                    $('#operator_select').show();
                }
                loadGirls();
            });

            $('#group_id, #operator_id').change(loadGirls);

            function loadGirls() {
                let auditType = $('#audit_type').val();
                let groupId = auditType == 'group' ? $('#group_id').val() : null;
                let operatorId = auditType == 'individual' ? $('#operator_id').val() : null;

                $.ajax({
                    url: "{{ route('audits.getGirlsByGroup') }}",
                    data: {
                        group_id: groupId,
                        audit_type: auditType,
                        operator_id: operatorId
                    },
                    success: function(girls) {
                        $('#girls_selection').empty();
                        girls.forEach(function(girl) {
                            let badge = $('<span class="badge rounded-pill m-1 girl-badge">')
                                .text(girl.name)
                                .data('girl-id', girl.id)
                                .data('girl-name', girl.name)
                                .data('platform-id', girl.platform_id)
                                .data('platform-name', girl.platform_name)
                                .addClass('bg-secondary')
                                .css('cursor', 'pointer');
                            $('#girls_selection').append(badge);
                        });
                    }
                });
            }

            $(document).on('click', '.girl-badge', function() {
                let girlId = $(this).data('girl-id');
                let girlName = $(this).data('girl-name');
                let platformId = $(this).data('platform-id');
                let platformName = $(this).data('platform-name');

                if (selectedGirls.has(girlId)) {
                    selectedGirls.delete(girlId);
                    $(this).removeClass('bg-primary').addClass('bg-secondary');
                    removeAuditTab(girlId);
                } else {
                    selectedGirls.add(girlId);
                    $(this).removeClass('bg-secondary').addClass('bg-primary');
                    createAuditTab(girlId, girlName, platformId, platformName);
                }

                updateTotalScore();
                toggleSelectionFields();

            });

            function toggleSelectionFields() {
                if (selectedGirls.size > 0) {
                    $('#audit_type').prop('disabled', true);
                    $('#group_id').prop('disabled', true);
                    $('#operator_id').prop('disabled', true);
                } else {
                    $('#audit_type').prop('disabled', false);
                    $('#group_id').prop('disabled', false);
                    $('#operator_id').prop('disabled', false);
                }
            }

            function createAuditTab(girlId, girlName, platformId, platformName) {
                let tabId = 'girl-' + girlId;

                if ($('#' + tabId).length === 0) {
                    console.log('Creando nueva pestaña para:', girlName);

                    // Crear la pestaña
                    let tabButton = $('<button>')
                        .addClass('nav-link')
                        .attr({
                            'id': tabId + '-tab',
                            'data-bs-toggle': 'tab',
                            'data-bs-target': '#' + tabId,
                            'type': 'button',
                            'role': 'tab',
                            'aria-controls': tabId,
                            'aria-selected': 'false'
                        })
                        .text(girlName);

                    $('#auditTabs').append(
                        $('<li>').addClass('nav-item').attr('role', 'presentation').append(tabButton)
                    );

                    // Crear el contenido de la pestaña
                    let tabContent = $('#audit-tab-template').html();
                    if (!tabContent) {
                        console.error('El template de la pestaña no se encontró');
                        return;
                    }

                    tabContent = tabContent
                        .replace(/TAB_ID/g, tabId)
                        .replace(/GIRL_ID/g, girlId);

                    // Añadir el contenido al contenedor de pestañas
                    let newTabPane = $(tabContent);
                    newTabPane.attr('id', tabId);
                    $('#auditTabContent').append(newTabPane);

                    console.log('Contenido de la pestaña creado:', newTabPane);

                    // Configurar la plataforma
                    $(`#${tabId} input[name="audit_details[${tabId}][platform_name]"]`).val(platformName);
                    $(`#${tabId} input[name="audit_details[${tabId}][platform_id]"]`).val(platformId);

                    // Inicializar el contenido de la pestaña
                    setTimeout(() => {
                        let tabPane = $('#' + tabId);
                        if (tabPane.length) {
                            console.log('Inicializando pestaña:', tabId);
                            initializeChecklist(tabPane);
                            let screenshotPasteArea = tabPane.find('.screenshot-paste-area')[0];
                            if (screenshotPasteArea) {
                                initializeScreenshotPaste(screenshotPasteArea);
                            }

                            // Activar la nueva pestaña
                            tabButton.tab('show');

                            // Asegurarse de que el contenido sea visible
                            tabPane.addClass('active show');

                            console.log('Pestaña activada y mostrada:', tabId);
                        } else {
                            console.error('No se pudo encontrar el contenido de la pestaña', tabId);
                        }
                    }, 0);
                } else {
                    console.log('Activando pestaña existente:', tabId);
                    $(`#${tabId}-tab`).tab('show');
                    $(`#${tabId}`).addClass('active show');
                }
            }

            function removeAuditTab(girlId) {
                let tabId = 'girl-' + girlId;
                $('#' + tabId + '-tab').parent().remove();
                $('#' + tabId).remove();

                if ($('#auditTabs .nav-link').length > 0) {
                    $('#auditTabs .nav-link:first').tab('show');
                }
            }

            function initializeChecklist(tabElement) {
                let checklistItems = tabElement.find('.checklist-item');
                let generalScoreInput = tabElement.find('.general-score');
                let progressBar = tabElement.find('.progress-bar');

                checklistItems.on('change', function() {
                    let totalScore = 0;
                    let checkedItems = 0;
                    checklistItems.each(function() {
                        if ($(this).is(':checked')) {
                            totalScore += parseInt($(this).data('score'));
                            checkedItems++;
                        }
                    });
                    generalScoreInput.val(totalScore);

                    let progress = (checkedItems / checklistItems.length) * 100;
                    progressBar.css('width', progress + '%').attr('aria-valuenow', progress).text(Math
                        .round(progress) + '%');

                    updateTotalScore();
                });
            }

            function updateTotalScore() {
                let totalScore = 0;
                let girlCount = selectedGirls.size;
                $('.general-score').each(function() {
                    totalScore += parseInt($(this).val()) || 0;
                });
                let averageScore = girlCount > 0 ? Math.round(totalScore / girlCount) : 0;

                $('#total_score_progress').css('width', averageScore + '%').attr('aria-valuenow', averageScore)
                    .text(averageScore + '%');
                $('#total_score_display').text(averageScore);
            }

            function initializeScreenshotPaste(pasteArea) {
                if (!pasteArea) {
                    console.error('Área de pegado no encontrada');
                    return;
                }

                let previewArea = $(pasteArea).siblings('.screenshot-preview');
                let screenshotInput = $(pasteArea).siblings('.screenshot-input');
                let screenshots = [];

                pasteArea.addEventListener('paste', function(e) {
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
                                previewArea.append(img);
                                screenshots.push(event.target.result);
                                screenshotInput.val(JSON.stringify(screenshots));
                            };
                            reader.readAsDataURL(blob);
                        }
                    }
                });
            }

            $('#auditForm').on('submit', function(e) {
                e.preventDefault();
                if (selectedGirls.size === 0) {
                    alert('Por favor, seleccione al menos una chica para la auditoría.');
                    return;
                }
                this.submit();
            });

            // Inicializar carga de chicas al cargar la página
            loadGirls();
        });
    </script>
@endpush
