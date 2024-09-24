@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Gestión de Categorías de Links</h1>
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createCategoryLinkModal">
            Crear Nueva Categoría
        </button>
        <table id="categoryLinksTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Cantidad de Links</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categoryLinks as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->links->count() }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-category" data-id="{{ $category->id }}">Editar</button>
                            <button class="btn btn-sm btn-danger delete-category"
                                data-id="{{ $category->id }}">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal para crear/editar categoría -->
    <div class="modal fade" id="createCategoryLinkModal" tabindex="-1" aria-labelledby="createCategoryLinkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCategoryLinkModalLabel">Crear Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryLinkForm">
                        @csrf
                        <input type="hidden" id="categoryLinkId" name="id">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre de la Categoría</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="saveCategoryLink">Guardar</button>
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
            let table = $('#categoryLinksTable').DataTable();

            // Crear/Editar categoría
            $('#saveCategoryLink').click(function() {
                let formData = $('#categoryLinkForm').serialize();
                let url = $('#categoryLinkId').val() ? '/category_links/' + $('#categoryLinkId').val() :
                    '/category_links';
                let method = $('#categoryLinkId').val() ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        $('#createCategoryLinkModal').modal('hide');
                        table.ajax.reload();
                        alert('Categoría guardada exitosamente');
                    },
                    error: function(error) {
                        alert('Error al guardar la categoría');
                    }
                });
            });

            // Abrir modal para editar
            $('#categoryLinksTable').on('click', '.edit-category', function() {
                let id = $(this).data('id');
                $.get('/category_links/' + id + '/edit', function(data) {
                    $('#categoryLinkId').val(data.id);
                    $('#name').val(data.name);
                    $('#createCategoryLinkModalLabel').text('Editar Categoría');
                    $('#createCategoryLinkModal').modal('show');
                });
            });

            // Eliminar categoría
            $('#categoryLinksTable').on('click', '.delete-category', function() {
                if (confirm(
                        '¿Estás seguro de que quieres eliminar esta categoría? Esto también eliminará todos los links asociados.'
                        )) {
                    let id = $(this).data('id');
                    $.ajax({
                        url: '/category_links/' + id,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(result) {
                            table.ajax.reload();
                            alert('Categoría eliminada exitosamente');
                        },
                        error: function(error) {
                            alert('Error al eliminar la categoría');
                        }
                    });
                }
            });

            // Limpiar modal al cerrarlo
            $('#createCategoryLinkModal').on('hidden.bs.modal', function() {
                $('#categoryLinkForm')[0].reset();
                $('#categoryLinkId').val('');
                $('#createCategoryLinkModalLabel').text('Crear Nueva Categoría');
            });
        });
    </script>
@endpush
