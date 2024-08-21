@extends('layouts.app')

@section('content')
    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <a class="text-reset" href="{{ route('settings.platforms') }}">{{ __('admin.platforms') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.create_platform') }}</span>
    </h5>

    <div class="content">
        <div class="card shadow-custom border-0">
            <div class="card-body p-lg-4">
                <form action="{{ route('platforms.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nombre" class="form-label">{{ trans('admin.platform_name') }}</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="url_plataforma" class="form-label">{{ trans('admin.platform_url') }}</label>
                        <input type="text" name="url_plataforma" id="url_plataforma" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modo_acceso" class="form-label">{{ trans('admin.platform_access_mode') }}</label>
                        <select name="modo_acceso" id="modo_acceso" class="form-control" required>
                            <option value="PanelUnico">{{ trans('admin.single_panel') }}</option>
                            <option value="MultiPanel">{{ trans('admin.multi_panel') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="color" class="form-label">{{ trans('admin.platform_color') }}</label>
                        <input type="color" name="color" id="color" class="form-control form-control-color" value="#000000" title="{{ trans('admin.platform_color') }}">
                    </div>
                    <div class="mb-3">
                        <label for="logo_url" class="form-label">{{ trans('admin.platform_logo_url') }}</label>
                        <input type="text" name="logo_url" id="logo_url" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="grupos" class="form-label">{{ trans('admin.groups') }}</label>
                        <select name="grupos[]" id="grupos" class="form-control select2" multiple required>
                            @foreach ($grupos as $grupo)
                                <option value="{{ $grupo->id }}">{{ $grupo->nombre_grupo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-dark">{{ trans('admin.create') }}</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#grupos').select2({
                placeholder: "{{ trans('admin.search_groups') }}",
                allowClear: true
            });
        });
    </script>
    @endpush
@endsection
