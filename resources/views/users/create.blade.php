@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">{{ __('admin.create_operator') }}</h2>
        @if (session('success_message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="password" value="TeamSic2024">

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            {{ __('admin.personal_information') }}
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('admin.name') }}</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="identification" class="form-label">{{ __('admin.identification') }}</label>
                                <input type="text" class="form-control @error('identification') is-invalid @enderror"
                                    id="identification" name="identification" value="{{ old('identification') }}" required>
                                @error('identification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="birth_date" class="form-label">{{ __('admin.birth_date') }}</label>
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                    id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required>
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            {{ __('admin.contact_information') }}
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="phone" class="form-label">{{ __('admin.phone') }}</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">{{ __('admin.address') }}</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                    id="address" name="address" value="{{ old('address') }}" required>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="neighborhood" class="form-label">{{ __('admin.neighborhood') }}</label>
                                <input type="text" class="form-control @error('neighborhood') is-invalid @enderror"
                                    id="neighborhood" name="neighborhood" value="{{ old('neighborhood') }}" required>
                                @error('neighborhood')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            {{ __('admin.account_information') }}
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar"
                                     class="rounded-circle img-thumbnail"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            </div>
                            <div class="mb-3">
                                <label for="avatar" class="form-label">{{ __('admin.avatar') }}</label>
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                    id="avatar" name="avatar">
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('admin.email') }}</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="alert alert-info" role="alert">
                                {{ __('admin.default_password_info', ['password' => 'TeamSic2024']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">{{ __('admin.create') }}</button>
            </div>
        </form>
    </div>
@endsection

@section('javascripts')
    <script>
        // Preview avatar image
        document.getElementById('avatar').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.img-thumbnail').setAttribute('src', e.target.result);
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
@endsection
