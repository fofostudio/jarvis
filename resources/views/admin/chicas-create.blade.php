@extends('layouts.app')

@section('content')
    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <a class="text-reset" href="{{ route('settings.models') }}">{{ __('admin.create_model') }} </a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.create_model') }}</span>
    </h5>

    <div class="content">
        <div class="card shadow-custom border-0">
            <div class="card-body p-lg-4">
                <form action="{{ route('settings.models.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nombre_chica" class="form-label">{{ trans('admin.model_name') }}</label>
                        <input type="text" name="nombre_chica" id="nombre_chica" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="codigo_chica" class="form-label">{{ trans('admin.model_code') }}</label>
                        <input type="text" name="codigo_chica" id="codigo_chica" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="grupos" class="form-label">{{ trans('admin.groups_model') }}</label>
                        <div class="row">
                            @foreach ($grupos as $grupo)
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="grupos[]" id="grupo_{{ $grupo->id }}" value="{{ $grupo->id }}">
                                    <label class="form-check-label" for="grupo_{{ $grupo->id }}">
                                        {{ $grupo->nombre_grupo }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <h5 class="mb-3 fw-light">{{ trans('admin.accesses') }} ({{ trans('admin.one_minimal') }})</h5>
                    <div id="accesosContainer" class="row">
                        @foreach ($plataformas as $plataforma)
                        <div class="col-md-3 mb-3">
                            <div class="card shadow-custom border-0" style="background-color: {{ $plataforma->color }};">
                                <div class="card-header">
                                    {{ $plataforma->nombre }} | ({{ $plataforma->modo_acceso }})
                                    <div class="form-check form-switch float-end">
                                        <input class="form-check-input" type="checkbox" role="switch" id="acceso_{{ $plataforma->id }}_active" name="accesos[{{ $plataforma->id }}][active]" value="1">
                                        <label class="form-check-label" for="acceso_{{ $plataforma->id }}_active">{{ trans('admin.active') }}</label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="acceso_{{ $plataforma->id }}_username" class="form-label">{{ trans('admin.username') }}</label>
                                        <input type="text" class="form-control" id="acceso_{{ $plataforma->id }}_username" name="accesos[{{ $plataforma->id }}][username]">
                                    </div>
                                    <div class="mb-3">
                                        <label for="acceso_{{ $plataforma->id }}_password" class="form-label">{{ trans('admin.password') }}</label>
                                        <input type="text" class="form-control" id="acceso_{{ $plataforma->id }}_password" name="accesos[{{ $plataforma->id }}][password]">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>

                    <button type="submit" class="btn btn-dark">{{ trans('admin.create') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
