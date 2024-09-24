@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Gestión de Links</h1>
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createLinkModal">
            Crear Nuevo Link
        </button>
        <table id="linksTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Favicon</th>
                    <th>Nombre</th>
                    <th>Link</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($links as $link)
                    <tr>
                        <td>{{ $link->categoryLink->name }}</td>
                        <td><img src="{{ $link->favicon }}" alt="favicon" style="width: 16px; height: 16px;"></td>
                        <td>{{ $link->title }}</td>
                        <td><a href="{{ $link->url }}" target="_blank">{{ $link->url }}</a></td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-link" data-id="{{ $link->id }}">Editar</button>
                            <button class="btn btn-sm btn-danger delete-link"
                                data-id="{{ $link->id }}">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal para crear/editar link -->
    <div class="modal fade" id="createLinkModal" tabindex="-1" aria-labelledby="createLinkModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createLinkModalLabel">Crear Nuevo Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="linkForm">
                        @csrf
                        <input type="hidden" id="linkId" name="id">
                        <div class="mb-3">
                            <label for="title" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="url" class="form-label">URL</label>
                            <input type="url" class="form-control" id="url" name="url" required>
                        </div>
                        <div class="mb-3">
                            <label for="category_link_id" class="form-label">Categoría</label>
                            <select class="form-control" id="category_link_id" name="category_link_id" required>
                                @foreach ($categoryLinks as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="saveLink">Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
@endpush

@push('scripts')
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            let table = $('#linksTable').DataTable();

            // Crear/Editar link
            $('#saveLink').click(function() {
                let formData = $('#linkForm').serialize();
                let url = $('#linkId').val() ? '/links/' + $('#linkId').val() : '/links';
                let method = $('#linkId').val() ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        $('#createLinkModal').modal('hide');
                        table.ajax.reload();
                        alert('Link guardado exitosamente');
                    },
                    error: function(error) {
                        alert('Error al guardar el link');
                    }
                });
            });

            // Abrir modal para editar
            $('#linksTable').on('click', '.edit-link', function() {
                let id = $(this).data('id');
                $.get('/links/' + id + '/edit', function(data) {
                    $('#linkId').val(data.id);
                    $('#title').val(data.title);
                    $('#url').val(data.url);
                    $('#category_link_id').val(data.category_link_id);
                    $('#createLinkModalLabel').text('Editar Link');
                    $('#createLinkModal').modal('show');
                });
            });

            // Eliminar link
            $('#linksTable').on('click', '.delete-link', function() {
                if (confirm('¿Estás seguro de que quieres eliminar este link?')) {
                    let id = $(this).data('id');
                    $.ajax({
                        url: '/links/' + id,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(result) {
                            table.ajax.reload();
                            alert('Link eliminado exitosamente');
                        },
                        error: function(error) {
                            alert('Error al eliminar el link');
                        }
                    });
                }
            });

            // Limpiar modal al cerrarlo
            $('#createLinkModal').on('hidden.bs.modal', function() {
                $('#linkForm')[0].reset();
                $('#linkId').val('');
                $('#createLinkModalLabel').text('Crear Nuevo Link');
            });
        });
    </script>
@endpush
