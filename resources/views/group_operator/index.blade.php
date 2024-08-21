@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('admin.group_operator_assignments') }}</h2>

    <div class="mb-3">
        <a href="{{ route('group_operator.create') }}" class="btn btn-sm btn-dark mt-1 mt-lg-0">
            <i class="bi-plus-lg"></i> {{ __('admin.create_assignment') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @foreach(['morning', 'afternoon', 'night'] as $shift)
        <div class="card mb-4">
            <div class="card-header">
                <h4>{{ __('admin.shift_' . $shift) }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('admin.group') }}</th>
                                <th>{{ __('admin.operator') }}</th>
                                <th>{{ __('admin.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments->where('shift', $shift) as $assignment)
                                <tr>
                                    <td>{{ $assignment->group->name }}</td>
                                    <td>{{ $assignment->user->name }}</td>
                                    <td>
                                        <a href="{{ route('group_operator.edit', $assignment->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bi-pencil"></i> {{ __('admin.edit') }}
                                        </a>
                                        <form action="{{ route('group_operator.destroy', $assignment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('admin.confirm_delete') }}')">
                                                <i class="bi-trash"></i> {{ __('admin.delete') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('admin.no_assignments_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    <div class="mt-3">
        {{ $assignments->links() }}
    </div>
</div>
@endsection
