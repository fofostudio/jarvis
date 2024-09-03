@extends('layouts.app')

@section('content')
    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.user_admin') }} ({{ $users->count() }})</span>
        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
            <i class="bi-plus-lg"></i> {{ __('admin.create_user_admin') }}
        </a>
    </h5>

    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                @if (session('success_message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                <div class="card shadow-custom border-0">
                    <div class="card-body p-lg-4">
                        <div class="table-responsive p-0">
                            <table class="table table-hover" id="users-table">
                                <thead>
                                    <tr>
                                        <th class="active">{{ trans('admin.name') }}</th>
                                        <th class="active">{{ trans('admin.email') }}</th>
                                        <th class="active">{{ trans('admin.role') }}</th>
                                        <th class="active">{{ trans('admin.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->role }}</td>
                                            <td>
                                                <a href="{{ route('users.edit', $user) }}"
                                                    class="btn btn-success rounded-pill btn-sm me-2">
                                                    <i class="bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                    class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-danger rounded-pill btn-sm actionDelete">
                                                        <i class="bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">{{ trans('admin.no_users_found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
@section('javascript')
<script>
    $(document).ready(function() {
        $('#users-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/es_es.json"
            },
            "lengthMenu": [30, 50, 500],  // Agrega las opciones de filtro de registros
            "order": [[0, "asc"]] // Ordena la tabla por la primera columna por defecto (puedes cambiar el índice según tu necesidad)
        });
    });
</script>

@endsection
