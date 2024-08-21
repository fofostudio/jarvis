@extends('layouts.app')

@section('content')
    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.models') }} ({{$data->total()}})</span>
        <a href="{{ route('settings.models.create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
            <i class="bi-plus-lg"></i> {{ __('admin.create_model') }}
        </a>
    </h5>

    <div class="content">
        <div class="row mb-4">
            @foreach ($grupos as $grupo)
                <div class="col-md-3 mb-3">
                    <div class="card shadow-custom border-0">
                        <div class="card-body">
                            <h5 class="card-title">{{ $grupo->nombre_grupo }}</h5>
                            <p class="card-text">{{ trans('admin.models_count') }}: {{ $grupo->chicas->count() }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

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
                            <form id="searchForm" class="mt-lg-0 mt-2 position-relative d-flex align-items-center" role="search" autocomplete="off" action="{{ url('settings/models') }}" method="get">
                           <div class="me-3">

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
                                        <th class="active">{{ trans('admin.model_code') }}</th>
                                        <th class="active">{{ trans('admin.model_name') }}</th>
                                        <th class="active">{{ trans('admin.groups_model') }}</th>
                                        <th class="active">{{ trans('admin.actions') }}</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $chica)
                                        <tr>
                                            <td>{{ $chica->codigo_chica }}</td>
                                            <td>{{ $chica->nombre_chica }}</td>
                                            <td>
                                                @foreach ($chica->grupos as $grupo)
                                                    <span class="badge bg-primary me-1">{{ $grupo->nombre_grupo }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="{{ route('settings.models.show', $chica) }}" class="btn btn-dark rounded-pill btn-sm me-2">
                                                    <i class="bi-eye"></i>
                                                </a>
                                                <a href="{{ route('settings.models.edit', $chica) }}" class="btn btn-success rounded-pill btn-sm me-2">
                                                    <i class="bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('settings.models.destroy', $chica) }}" method="POST" class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger rounded-pill btn-sm actionDelete">
                                                        <i class="bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">{{ trans('admin.no_models_found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{ $data->links() }}
            </div>
        </div>
    </div>
@endsection
