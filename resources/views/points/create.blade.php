@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <div class="container">
        <h2 class="mb-4">{{ __('admin.create_points') }}</h2>

        <form id="fileUploadForm" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="pointsFile" class="form-label">{{ __('admin.upload_points_file') }}</label>
                <input type="file" class="form-control" id="pointsFile" name="pointsFile" accept=".txt" required>
            </div>
            <div class="mb-3">
                <label for="shift" class="form-label">{{ __('admin.shift') }}</label>
                <select class="form-select" id="shift" name="shift" required>
                    <option value="">{{ __('admin.select_shift') }}</option>
                    @foreach ($shiftOptions as $shiftOption)
                        <option value="{{ $shiftOption }}">{{ __('admin.shift_' . $shiftOption) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">{{ __('admin.date') }}</label>
                <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}"
                    required>
            </div>
            <button type="submit" class="btn btn-primary">{{ __('admin.upload_and_preview') }}</button>
        </form>

        <div id="previewContainer" class="mt-4" style="display: none;">
            <h3>{{ __('admin.preview_and_edit') }}</h3>
            <form id="pointsForm" action="{{ route('points.store') }}" method="POST">
                @csrf
                <input type="hidden" name="shift" id="hiddenShift">
                <input type="hidden" name="date" id="hiddenDate">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('admin.group') }}</th>
                            <th>{{ __('admin.operator') }}</th>
                            <th>{{ __('admin.points') }}</th>
                            <th>{{ __('admin.goal') }}</th>
                        </tr>
                    </thead>
                    <tbody id="previewTableBody">
                    </tbody>
                </table>
                <button type="submit" class="btn btn-success">{{ __('admin.save_points') }}</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileUploadForm = document.getElementById('fileUploadForm');
            const previewContainer = document.getElementById('previewContainer');
            const previewTableBody = document.getElementById('previewTableBody');
            const pointsForm = document.getElementById('pointsForm');
            const hiddenShift = document.getElementById('hiddenShift');
            const hiddenDate = document.getElementById('hiddenDate');
            const fileInput = document.getElementById('pointsFile');
            const shiftInput = document.getElementById('shift');
            const dateInput = document.getElementById('date');

            fileUploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                // Disable inputs
                fileInput.disabled = true;
                shiftInput.disabled = true;
                dateInput.disabled = true;

                // Show loading alert
                Swal.fire({
                    title: 'Cargando archivo',
                    html: 'Por favor espere...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('{{ route('points.preview') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Close loading alert
                        Swal.close();

                        // Re-enable inputs
                        fileInput.disabled = false;
                        shiftInput.disabled = false;
                        dateInput.disabled = false;

                        if (data.success) {
                            previewTableBody.innerHTML = '';
                            data.preview.forEach(item => {
                                const row = `
                        <tr>
                            <td>${item.group}</td>
                            <td>
                                <select name="operators[${item.group_id}]" class="form-select">
                                    ${item.operators.map(op => `<option value="${op.id}" ${op.id === item.assigned_operator_id ? 'selected' : ''}>${op.name}</option>`).join('')}
                                </select>
                            </td>
                            <td><input type="number" name="points[${item.group_id}]" value="${item.points}" class="form-control" required></td>
                            <td><input type="number" name="goals[${item.group_id}]" value="${item.goal}" class="form-control" required></td>
                        </tr>
                    `;
                                previewTableBody.insertAdjacentHTML('beforeend', row);
                            });
                            hiddenShift.value = shiftInput.value;
                            hiddenDate.value = dateInput.value;
                            previewContainer.style.display = 'block';

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Se ha Cargado el Punto',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        // Re-enable inputs
                        fileInput.disabled = false;
                        shiftInput.disabled = false;
                        dateInput.disabled = false;

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al procesar el archivo.'
                        });
                    });
            });

            pointsForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                Swal.fire({
                    title: 'Guardando puntos',
                    html: 'Por favor espere...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Puntos guardados exitosamente',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = data.redirect;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Ocurrió un error al guardar los puntos.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al guardar los puntos.'
                        });
                    });
            });
        });
    </script>
@endsection
