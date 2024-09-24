@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">{{ __('admin.Profile') }}</h2>

    @if (session('success_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check2 me-1"></i> {{ session('success_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">{{ __('admin.avatar') }}</div>
                <div class="card-body text-center">
                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}"
                         alt="{{ $user->name }}'s Avatar"
                         class="img-fluid rounded-circle mb-3"
                         style="width: 150px; height: 150px; object-fit: cover;">
                    <h5 class="card-title">{{ $user->name }}</h5>
                    <p class="card-text">{{ $user->email }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">{{ __('admin.profile_info') }}</div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ __('admin.profile_name') }}</h6>
                            <p>{{ $user->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ __('admin.email') }}</h6>
                            <p>{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ __('admin.phone') }}</h6>
                            <p>{{ $user->phone ?: __('admin.not_provided') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ __('admin.address') }}</h6>
                            <p>{{ $user->address ?: __('admin.not_provided') }}</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">{{ __('admin.neighborhood') }}</h6>
                        <p>{{ $user->neighborhood ?: __('admin.not_provided') }}</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">{{ __('admin.update_password') }}</div>
                <div class="card-body">
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('admin.current_password') }}</label>
                            <input id="current_password" name="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('admin.new_password') }}</label>
                            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('admin.confirm_password') }}</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('admin.update_password') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
