@extends('layouts.app')

@section('content')
    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.groups') }} ({{$categories->sum('groups_count')}})</span>
        <a href="{{ route('groups.create') }}" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">
            <i class="bi-plus-lg"></i> {{ __('admin.create_group') }}
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
                        @foreach ($categories as $category)
                            <h6 class="mb-3">{{ $category->name }} ({{ $category->groups->count() }})</h6>
                            <div class="table-responsive p-0 mb-4">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="active">{{ trans('admin.group_name') }}</th>
                                            <th class="active">{{ trans('admin.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($category->groups as $group)
                                            <tr>
                                                <td>{{ $group->name }}</td>
                                                <td>
                                                    <a href="{{ route('groups.edit', $group) }}" class="btn btn-success rounded-pill btn-sm me-2">
                                                        <i class="bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('groups.destroy', $group) }}" method="POST" class="d-inline-block">
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
                                                <td colspan="2" class="text-center">{{ trans('admin.no_groups_found') }} en esta categoría</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
