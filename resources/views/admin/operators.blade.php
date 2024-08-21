@extends('layouts.app')

@section('content')
    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.operators') }} ({{$operators->total()}})</span>
        <a href="{{ url('settings/operators/create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
            <i class="bi-plus-lg"></i> {{ __('admin.create_operator') }}
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
                        <div class="d-lg-flex justify-content-lg-between align-items-center mb-2 w-100">
                            <form id="searchForm" class="mt-lg-0 mt-2 position-relative d-flex align-items-center" role="search" autocomplete="off" action="{{ url('panel/admin/settings/operators') }}" method="get">
                                <div class="me-3">
                                    <select name="group_filter" id="group_filter" class="form-select">
                                        <option value="">{{ __('admin.all_groups') }}</option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}" {{ request('group_filter') == $group->id ? 'selected' : '' }}>
                                                {{ $group->nombre_grupo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="position-relative">
                                    <i class="bi bi-search btn-search bar-search"></i>
                                    <input type="text" name="q" id="searchInput" class="form-control ps-5 w-auto" value="{{ $search ?? '' }}" placeholder="{{ __('general.search') }}">
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="active">ID</th>
                                        <th class="active">{{ trans('auth.full_name') }}</th>
                                        <th class="active">{{ trans('auth.email') }}</th>
                                        <th class="active">{{ trans('admin.group') }}</th>
                                        <th class="active">{{ trans('admin.shift') }}</th>
                                        <th class="active">{{ trans('admin.status') }}</th>
                                        <th class="active">{{ trans('admin.date') }}</th>
                                        <th class="active">{{ trans('admin.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($operators->total() != 0 && $operators->count() != 0)
                                        @foreach ($operators as $operator)
                                            <tr>
                                                <td>{{ $operator->id }}</td>
                                                <td>{{ $operator->name }}</td>
                                                <td>{{ $operator->email }}</td>
                                                <td>{{ $operator->grupo->nombre_grupo ?? 'N/A' }}</td>
                                                <td>{{ $operator->jornada }}</td>
                                                <td>
                                                    @if ($operator->status == 1)
                                                        <span class="badge bg-success">{{ trans('admin.active') }}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ trans('admin.inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ Helper::formatDate($operator->created_at) }}</td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ url('settings/operators/edit/'.$operator->id.'') }}" class="btn btn-success rounded-pill btn-sm me-2">
                                                            <i class="bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ url('settings/operators/'.$operator->id) }}" method="POST" class="d-inline-block align-top">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger rounded-pill btn-sm actionDelete">
                                                                <i class="bi-trash-fill"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="8">
                                                <h5 class="text-center p-5 text-muted fw-light m-0">{{ trans('general.no_results_found') }}</h5>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if ($operators->hasPages())
                    {{ $operators->appends(['q' => $search, 'group_filter' => request('group_filter')])->links() }}
                @endif
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('searchForm');
            const groupFilter = document.getElementById('group_filter');
            const searchInput = document.getElementById('searchInput');

            function submitForm() {
                form.submit();
            }

            groupFilter.addEventListener('change', submitForm);

            let debounceTimer;
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(submitForm, 500); // Envía el formulario después de 500ms de inactividad
            });
        });
    </script>
@endsection
