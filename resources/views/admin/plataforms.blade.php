@extends('layouts.app')

@section('content')
    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.platforms') }} ({{$platforms->total()}})</span>
        <a href="{{ route('platforms.create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
            <i class="bi-plus-lg"></i> {{ __('admin.create_platform') }}
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
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="active">ID</th>
                                        <th class="active">{{ trans('admin.platform_name') }}</th>
                                        <th class="active">{{ trans('admin.platform_url') }}</th>
                                        <th class="active">{{ trans('admin.platform_color') }}</th>
                                        <th class="active">{{ trans('admin.groups') }}</th>
                                        <th class="active">{{ trans('admin.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($platforms as $platform)
                                        <tr>
                                            <td>{{ $platform->id }}</td>
                                            <td>{{ $platform->nombre }}</td>
                                            <td>{{ $platform->url_plataforma }}</td>
                                            <td>
                                                <div class="rounded-circle" style="width: 20px; height: 20px; background-color: {{ $platform->color }}"></div>
                                            </td>
                                            <td>
                                                @foreach ($platform->grupos as $grupo)
                                                    <span class="badge bg-primary me-1">{{ $grupo->nombre_grupo }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="{{ route('platforms.edit', $platform) }}" class="btn btn-success rounded-pill btn-sm me-2">
                                                    <i class="bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('platforms.delete', $platform) }}" method="POST" class="d-inline-block">
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
                                            <td colspan="6" class="text-center">{{ trans('admin.no_platforms_found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{ $platforms->links() }}
            </div>
        </div>
    </div>
@endsection
