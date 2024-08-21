@extends('layouts.app')

@section('content')
    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <a class="text-reset" href="{{ route('settings.models') }}">{{ __('admin.models') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.data_model') }}</span>
    </h5>

    <div class="content">
        <div class="card shadow-custom border-0">
            <div class="card-body p-lg-4">
                <h2 class="mb-4">{{ $chica->nombre_chica }}</h2>
                <p class="mb-4">{{ trans('admin.model_code') }}: {{ $chica->codigo_chica }}</p>

                <h5 class="mb-3 fw-light">{{ trans('admin.groups_model') }}</h5>
                <div class="row mb-4">
                    @foreach ($chica->grupos as $grupo)
                        <div class="col-md-3 mb-2">
                            <span class="badge bg-primary">{{ $grupo->nombre_grupo }}</span>
                        </div>
                    @endforeach
                </div>

                <h5 class="mb-3 fw-light">{{ trans('admin.accesses') }}</h5>
                <div id="accesosContainer" class="row">
                    @foreach ($chica->accesos as $acceso)
                        <div class="col-md-3 mb-3">
                            <div class="card shadow-custom border-0" style="background-color: {{ $loop->index % 4 == 0 ? '#cce5ff' : ($loop->index % 4 == 1 ? '#d1e7dd' : ($loop->index % 4 == 2 ? '#e8f4f8' : '#f1f8e9')) }};">
                                <div class="card-header">
                                    {{ $acceso->plataforma->nombre }} | ({{ $acceso->plataforma->modo_acceso }})
                                    <div class="form-check form-switch float-end">
                                        <input class="form-check-input" type="checkbox" role="switch" id="acceso_{{ $acceso->plataforma_id }}_active" name="accesos[{{ $acceso->plataforma_id }}][active]" value="1" {{ $acceso->active ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="acceso_{{ $acceso->plataforma_id }}_active">{{ trans('admin.active') }}</label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="acceso_{{ $acceso->plataforma_id }}_username" class="form-label">{{ trans('admin.username') }}</label>
                                        <input type="text" class="form-control" id="acceso_{{ $acceso->plataforma_id }}_username" name="accesos[{{ $acceso->plataforma_id }}][username]" value="{{ $acceso->username }}" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="acceso_{{ $acceso->plataforma_id }}_password" class="form-label">{{ trans('admin.password') }}</label>
                                        <input type="text" class="form-control" id="acceso_{{ $acceso->plataforma_id }}_password" name="accesos[{{ $acceso->plataforma_id }}][password]" value="{{ $acceso->password }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
