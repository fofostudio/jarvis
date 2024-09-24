<form id="editPointForm" action="{{ route('points.update', $point) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="date" class="form-label">{{ __('admin.date') }}</label>
        <input type="text" class="form-control-plaintext" id="date" name="date"
            value="{{ $point->date instanceof \DateTime ? $point->date->format('Y-m-d') : $point->date }}" readonly>
    </div>

    <div class="mb-3">
        <label for="group_id" class="form-label">{{ __('admin.group') }}</label>
        <input type="text" class="form-control-plaintext" id="group_name"
            value="{{ $point->group->name }}" readonly>
        <input type="hidden" name="group_id" value="{{ $point->group_id }}">
    </div>

    <div class="mb-3">
        <label for="user_id" class="form-label">{{ __('admin.operator') }}</label>
        <select class="form-select" id="user_id" name="user_id" required>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ old('user_id', $point->user_id) == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        <div id="user_id_error" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="points" class="form-label">{{ __('admin.points') }}</label>
        <input type="number" class="form-control" id="points"
            name="points" value="{{ old('points', $point->points) }}" required>
        <div id="points_error" class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label for="goal" class="form-label">{{ __('admin.goal') }}</label>
        <input type="number" class="form-control-plaintext" id="goal"
            name="goal" value="{{ old('goal', $point->goal) }}" readonly>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
        <button type="submit" class="btn btn-primary">{{ __('admin.update') }}</button>
    </div>
</form>
