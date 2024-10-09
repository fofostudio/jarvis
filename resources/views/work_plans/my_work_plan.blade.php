@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">Mi Plan de Trabajo</h1>
    <h2 class="text-center mb-3">{{ $currentDate->format('l, F j, Y') }}</h2>
    <h3 class="text-center mb-4">Jornada: {{ ucfirst($shiftInSpanish) }}</h3>

    @if($assignments->isNotEmpty())
        <div class="row justify-content-center mb-5">
            @foreach(['mensajes', 'icebreakers', 'cartas'] as $type)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm {{ $completedPlans[$type] ? 'bg-dark text-white' : '' }}">
                        <div class="card-body text-center">
                            @if(isset($assignments[$type]))
                                <h4 class="card-title">{{ ucfirst($type) }}</h4>
                                <p class="card-text display-4">{{ $assignments[$type]->girl->name }}</p>
                                <button class="btn btn-primary mt-3 select-calendar" data-type="{{ $type }}" {{ $completedPlans[$type] ? 'disabled' : '' }}>
                                    {{ $completedPlans[$type] ? 'Completado' : 'Seleccionar' }}
                                </button>
                            @else
                                <h4 class="card-title">{{ ucfirst($type) }}</h4>
                                <p class="card-text text-muted">No hay asignación hoy</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <form id="workPlanForm" action="{{ route('work_plans.update_my_work_plan') }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <input type="hidden" name="calendar_type" id="calendar_type">
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Detalles del Plan</h5>
                    <div class="form-group">
                        <label for="cant">Cantidad</label>
                        <input type="number" class="form-control" id="cant" name="cant" required>
                    </div>

                    <div class="form-group">
                        <label for="mensaje">Mensaje</label>
                        <textarea class="form-control" id="mensaje" name="mensaje" rows="3" required></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Screenshots</h5>
                    <div class="form-group">
                        <div id="pasteArea" class="border rounded p-3 mb-3" style="min-height: 100px;">
                            Haga clic aquí y pegue sus screenshots (Ctrl+V)
                        </div>
                        <input type="file" class="d-none" id="fileInput" multiple accept="image/*">
                        <div id="previewArea" class="d-flex flex-wrap">
                            <!-- Las imágenes previsualizadas se insertarán aquí -->
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-lg btn-block mt-4">Actualizar Plan de Trabajo</button>
        </form>
    @else
        <div class="alert alert-info text-center" role="alert">
            No hay plan de trabajo disponible para hoy.
        </div>
    @endif

    <!-- Listado de todos los planes cargados -->
    @if($loadedPlans->isNotEmpty())
        <div class="card mt-5">
            <div class="card-header">
                <h5 class="mb-0">Historial de Planes de Trabajo</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Mensaje</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loadedPlans as $plan)
                                <tr>
                                    <td>{{ $plan->date->format('d/m/Y') }}</td>
                                    <td>{{ ucfirst($plan->calendar_type) }}</td>
                                    <td>{{ $plan->cantidad }}</td>
                                    <td>{{ Str::limit($plan->mensaje, 30) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-screenshots" data-plan-id="{{ $plan->id }}">Ver Screenshots</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Modal para mostrar screenshots -->
<div class="modal fade" id="screenshotModal" tabindex="-1" role="dialog" aria-labelledby="screenshotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="screenshotModalLabel">Screenshots</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Las imágenes se cargarán aquí -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let selectedFiles = [];

    $('.select-calendar').on('click', function() {
        var type = $(this).data('type');
        $('#calendar_type').val(type);
        $('.card').removeClass('border-primary');
        $(this).closest('.card').addClass('border-primary');
        $('#workPlanForm').show();
        $('html, body').animate({
            scrollTop: $("#workPlanForm").offset().top
        }, 1000);
    });

    $('#workPlanForm').hide();

    $('#pasteArea').on('click', function() {
        $('#fileInput').click();
    });

    $('#pasteArea').on('paste', function(event) {
        var items = (event.clipboardData || event.originalEvent.clipboardData).items;
        for (var i = 0; i < items.length; i++) {
            if (items[i].type.indexOf("image") !== -1) {
                var blob = items[i].getAsFile();
                addImagePreview(blob);
            }
        }
    });

    $('#fileInput').on('change', function(event) {
        for (var i = 0; i < event.target.files.length; i++) {
            addImagePreview(event.target.files[i]);
        }
    });

    function addImagePreview(file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = $('<div class="position-relative mr-2 mb-2">' +
                '<img src="' + e.target.result + '" style="max-width: 100px; max-height: 100px;">' +
                '<button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 0; right: 0;">&times;</button>' +
                '</div>');
            $('#previewArea').append(preview);
            selectedFiles.push(file);

            preview.find('button').on('click', function() {
                var index = preview.index();
                selectedFiles.splice(index, 1);
                preview.remove();
            });
        }
        reader.readAsDataURL(file);
    }

    function resetForm() {
        $('#workPlanForm')[0].reset();
        $('#previewArea').empty();
        selectedFiles = [];
        $('#pasteArea').text('Haga clic aquí y pegue sus screenshots (Ctrl+V)');
    }

    $('#workPlanForm').on('submit', function(e) {
        e.preventDefault();
        
        if (selectedFiles.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe cargar al menos un screenshot.',
            });
            return;
        }

        var formData = new FormData(this);
        selectedFiles.forEach(function(file, index) {
            formData.append('screenshots[]', file, 'screenshot_' + index + '.png');
        });

        Swal.fire({
            title: '¿Está seguro?',
            text: "Va a actualizar el plan de trabajo. Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                '¡Actualizado!',
                                response.message,
                                'success'
                            );
                            var type = $('#calendar_type').val();
                            var card = $('.select-calendar[data-type="' + type + '"]').closest('.card');
                            card.addClass('bg-dark text-white');
                            card.find('.select-calendar').prop('disabled', true).text('Completado');
                            $('#workPlanForm').hide();
                            resetForm();
                            location.reload(); // Recargar la página para mostrar el plan actualizado
                        } else {
                            Swal.fire(
                                'Error',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Hubo un error al actualizar el plan de trabajo';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire(
                            'Error',
                            errorMessage,
                            'error'
                        );
                    }
                });
            }
        });
    });

    // Función para ver los screenshots
    $('.view-screenshots').on('click', function() {
        var planId = $(this).data('plan-id');
        $.ajax({
            url: '/work-plans/' + planId + '/screenshots',
            method: 'GET',
            success: function(response) {
                var modalBody = $('#screenshotModal .modal-body');
                modalBody.empty();
                response.screenshots.forEach(function(screenshot) {
                    modalBody.append('<img src="' + screenshot + '" class="img-fluid mb-2" alt="Screenshot">');
                });
                $('#screenshotModal').modal('show');
            },
            error: function() {
                Swal.fire('Error', 'No se pudieron cargar los screenshots', 'error');
            }
        });
    });
});
</script>
@endpush