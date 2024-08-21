@extends('layouts.app')

@section('content')
    <h5 class="mb-4 fw-light">
        <a class="text-reset" href="{{ url('dashboard') }}">{{ __('admin.dashboard') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <a class="text-reset" href="{{ route('settings.operators') }}">{{ __('admin.operators') }}</a>
        <i class="bi-chevron-right me-1 fs-6"></i>
        <span class="text-muted">{{ __('admin.create_operator') }}</span>
    </h5>

    <div class="content">
        <div class="card shadow-custom border-0">
            <div class="card-body p-lg-5">
                <form method="post" action="{{ route('settings.operators.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-4">{{ __('admin.general_information') }}</h5>
                            <div class="mb-3 text-center">
                                <label for="avatar" class="form-label">{{ __('admin.avatar') }}</label>
                                <div class="avatar-preview rounded-circle mb-3" style="background-image: url('{{ old('avatar', asset('images/placeholder.png')) }}')"></div>
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('auth.full_name') }}</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('auth.email') }}</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('auth.password') }}</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="jornada" class="form-label">{{ __('admin.shift') }}</label>
                                <select class="form-select @error('jornada') is-invalid @enderror" id="jornada" name="jornada" required>
                                    <option value="">{{ __('admin.select_shift') }}</option>
                                    <option value="Mañana" {{ old('jornada') == 'Mañana' ? 'selected' : '' }}>{{ __('admin.morning') }}</option>
                                    <option value="Completa" {{ old('jornada') == 'Completa' ? 'selected' : '' }}>{{ __('admin.complete') }}</option>
                                    <option value="Tarde" {{ old('jornada') == 'Tarde' ? 'selected' : '' }}>{{ __('admin.afternoon') }}</option>
                                    <option value="Nocturna" {{ old('jornada') == 'Nocturna' ? 'selected' : '' }}>{{ __('admin.night') }}</option>
                                </select>
                                @error('jornada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="grupo_id" class="form-label">{{ __('admin.group') }}</label>
                                <select class="form-select @error('grupo_id') is-invalid @enderror" id="grupo_id" name="grupo_id" required>
                                    <option value="">{{ __('admin.select_group') }}</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" {{ old('grupo_id') == $group->id ? 'selected' : '' }}>{{ $group->nombre_grupo }}</option>
                                    @endforeach
                                </select>
                                @error('grupo_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-4">{{ __('admin.additional_information') }}</h5>


                            <div class="mb-3">
                                <label for="identification" class="form-label">{{ __('admin.identification') }}</label>
                                <input type="text" class="form-control @error('identification') is-invalid @enderror" id="identification" name="identification" value="{{ old('identification') }}">
                                @error('identification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="date_of_birth" class="form-label">{{ __('admin.date_of_birth') }}</label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">{{ __('admin.phone') }}</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">{{ __('admin.address') }}</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="neighborhood" class="form-label">{{ __('admin.neighborhood') }}</label>
                                <input type="text" class="form-control @error('neighborhood') is-invalid @enderror" id="neighborhood" name="neighborhood" value="{{ old('neighborhood') }}">
                                @error('neighborhood')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-dark float-lg-end mt-1 mt-lg-0">{{ __('admin.create_operator') }}</button>
                </form>
            </div>
        </div>
    </div>


    <script>
        document.getElementById('avatar').addEventListener('change', function() {
            var avatarPreview = document.querySelector('.avatar-preview');
            avatarPreview.style.backgroundImage = 'url("' + URL.createObjectURL(this.files[0]) + '")';
        });
    </script>

@endsection
