@extends('layouts.app')

@section('content')
    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <a class="text-reset" href="{{ route('settings.groups') }}">{{ __('admin.groups') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.create_group') }}</span>
    </h5>

    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-custom border-0">
                    <div class="card-body p-lg-5">
                        <form method="post" action="{{ route('settings.groups.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="nombre_grupo" class="form-label">{{ __('admin.group_name') }}</label>
                                <input type="text" class="form-control @error('nombre_grupo') is-invalid @enderror" id="nombre_grupo" name="nombre_grupo" value="{{ old('nombre_grupo') }}">
                                @error('nombre_grupo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">{{ __('admin.create_group') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
