<table id="recordsTable" class="table table-hover">
    <thead>
        <tr>
            <th class="active">{{ trans('admin.user') }}</th>
            <th class="active">{{ trans('admin.group') }}</th>
            <th class="active">{{ trans('admin.shift') }}</th>
            <th class="active">{{ trans('admin.date') }}</th>
            <th class="active">{{ trans('admin.points') }}</th>
            <th class="active">{{ trans('admin.goal') }}</th>
            <th class="active">{{ trans('admin.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($points as $point)
            <tr>
                <td>{{ $point->user->name }}</td>
                <td>{{ $point->group->name }}</td>
                <td>{{ trans('admin.' . $point->shift) }}</td>
                <td>{{ Helper::formatDate($point->date) }}</td>
                <td>{{ $point->points }}</td>
                <td>{{ $point->goal }}</td>
                <td>
                    <form action="{{ route('points.destroy', $point) }}" method="POST" class="d-inline-block">
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
                <td colspan="7" class="text-center">{{ trans('admin.no_points_found') }}</td>
            </tr>
        @endforelse
    </tbody>
</table>
